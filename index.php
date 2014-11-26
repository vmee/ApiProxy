<?php
/**
 * Created by PhpStorm.
 * User: kingkong
 * Date: 11/25/14
 * Time: 12:58 PM
 */

define('API_HOST', 'host');
define('API_PATH', 'path');
define('API_REQUEST_METHOD', 'get');

class proxy {

    public static $action;
    public static $parameters;

    public static $actionConfig;
    public static $apiHost;
    public static $apiPath;
    public static $apiRequestMethod;

    public static function initRequest(){

        if(empty($_GET['a']) || empty($_GET['p']))
            self::output('', -1, 'parameter lost');

        self::$action = trim($_GET['a']);
        self::$parameters = json_decode(base64_decode($_GET['p']));
    }

    public static function initActionConf(){

        self::$apiHost = API_HOST;
        self::$apiPath = API_PATH;
        self::$apiRequestMethod = API_REQUEST_METHOD;


    }

    public static function getActionConf(){

        self::$actionConfig = include "conf/".self::$action.".conf";


    }

    public static function verifyParameters(){

        if(is_array(self::$actionConfig["parameters_verify"])){

            $verify = self::$actionConfig["parameters_verify"];

            foreach($verify as $key=>$value){
                if(!isset(self::$parameters[$key])){
                    self::output('', '-1', 'params verify fail');
                }
            }
        }

    }

    public static function replaceParameters(){
        if(is_array(self::$actionConfig["parameters_replace"])){
            $replaces = self::$actionConfig["parameters_replace"];

            foreach($replaces as $s=>$r){
                if(isset(self::$parameters[$s])){
                    self::$parameters[$r] = self::$parameters[$s];
                    unset(self::$parameters[$s]);
                }
            }
        }
    }

    public static function appendParameters(){
        if(is_array(self::$actionConfig["parameters_append"])){
            $append = self::$actionConfig["parameters_append"];

            foreach($append as $key=>$value){
               self::$parameters[$key] = $value;
            }
        }
    }


    public static function requireApiData(&$data, $rule){
        foreach($rule as $key=>$value){
            if(is_array($value)){
                if(is_array($data[$key])){
                    self::replaceParameters($data[$key], $value);
                }else{
                    //record error log
                }
            }
        }
    }

    public static function replaceApiData(& $data, $rule){

    }

    public static function appendApiData(& $data, $rule){

    }

    public static function authApiData($data){
        self::requireApiData($data, self::$actionConfig['api_data_require']);
        self::repalceApiData($data, self::$actionConfig['api_data_replace']);
        self::appendApiData($data, self::$actionConfig['api_data_append']);
    }

    public static function getApiData(){


        $data = http_build_query(self::$parameters);

        $options = array(
            'http' => array(
                'method' => self::$apiRequestMethod,
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $data,
                'timeout' => 20, // 超时时间（单位:s）
            )
        );

        $url = "http://".self::$apiHost.'/'.self::$apiPath;
        $context = stream_context_create($options);
        $return = file_get_contents($url, false, $context);

        return self::authApiData($return);
    }


    public static function initParameters(){
        self::verifyParameters();
        self::replaceParameters();
        self::appendParameters();
    }

    public static function run(){

        self::initRequest();
        self::getAction();
        self::initParameters();

        if($returnData = self::getApiData()){
            self::output($returnData);
        }else{
            self::output('', -1, 'no api data');
        }

    }

    public static function output($data,$status=1,$error=''){

        header( 'Content-Type:text/html;charset=utf-8 ');

        $output = array(
            's'=>$status,
            'data'=>$data,
        );

        if($status != 1){
            header('HTTP/1.1 400 data error');
            $output['error'] = $error;
        }else{
            header('HTTP/1.1 200 OK');
        }

        header('Content-type: application/json');
        exit(json_encode($output));
    }


}

proxy::run();