<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

header( 'Content-Type:text/html;charset=utf-8 ');

if(!empty($_POST)){
    if(!empty($_POST['action'])){
        $confFile = 'conf/'.trim($_POST['action']).'.conf';
        if(!file_exists($confFile))
            file_put_contents($confFile, '<?php return array(
                "api_config" => array(
                            "host" => "",
                            "path" => "",
                            "request_method" => "get",
                        ),
                "parameters_verify" => "",
                "parameters_replace" => "",
                "parameters_append" => "",
                "api_data_require" => "",
                "api_data_replace" => "",
                "api_data_append" => "",
            );');
    }

    if(!empty($_POST['conf'])){
        $conf = $_POST['conf'];
        $key = key($conf);
        $c = include 'conf/'.$key.'.conf';

        doParam($c, $conf[$key]);

        file_put_contents('conf/'.$key.'.conf', "<?php \r\n return ".var_export($c, true).';');

    }

echo '等待<span id="daojishi">2</span>秒后 正在保存
<script type="text/javascript">

  function daoshu(){

     var djs = document.getElementById("daojishi");

     if(djs.innerHTML == 0){

          window.location.href=\'admin.php\';

          return false;

     }

djs.innerHTML = djs.innerHTML - 1;

}

window.setInterval("daoshu()", 1000);

</script>';

   //header('location:admin.php');
   exit;
}

//读取已经有动作
$actionFiles = glob('conf/*.conf');


foreach($actionFiles as $file){

    $conf = include $file;


    $action = str_replace(array('conf/', '.conf'), '', $file);

    echo '接口',$action;
    echo '<hr>';
    echo "<form method=\"post\">";
    params($conf, 'conf['.$action.']');

}


echo '<hr />';
echo '<form method="post"><input name="action" type="text" placeholder="接口标识" \><input type="submit"></form>';


function doParam(& $conf, $params){
    if(!is_array($params)) return;

    if(isset($params[0])){
        if($params[1] === '-1'){
            unset($conf[$params[0]]);
        }else{
            if(!is_array($conf)) $conf = array();
            $conf[$params[0]] = $params[1];
        }
        return true;
    }
    $key = key($params);
    doParam($conf[$key], $params[$key]);
}

function params($conf, $action){

    if(!is_array($conf)){
        echo "\"$conf\",";
        echo '&nbsp;&nbsp;&nbsp;&nbsp;<input name="'.$action.'[]" type="text" placeholder="key" size="6" \>:<input name="'.$action.'[]" type="text" size="6" placeholder="value" \> <input type="submit" name="提交"></form>';
        return true;
    }else{
        echo '&nbsp;&nbsp;&nbsp;&nbsp;<input name="'.$action.'[]" type="text" placeholder="key" size="6" \>:<input name="'.$action.'[]" type="text" size="6" placeholder="value" \> <input type="submit" name="提交"></form>';
    }

    $count = substr_count($action, '[')-1;
    $space = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',  $count);
    echo "$space{";

    foreach($conf as $k=>$c){
        echo "<form method=\"post\">".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',  $count+1)."\"$k\":";
        params($c, $action."[".$k."]");
    }
    echo "$space}<br>";
}
