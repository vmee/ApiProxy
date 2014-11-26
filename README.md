接口代理程序

功能

支持接口中转，集成；
支持接口调用参数验证，替换，追加初始数据;
支持接口数据返回数据必字段检查，数据替换，追加其他数据。

程序说明

index.php
接口调用入口 接参数a和p
a: 接口标识
p: 接口参数 base64编码的json串

admin.php
添加接口标识和接口参数及数据配置
{
    "api_config":
    {
        "host":"",    // 服务器地址
        "path":"",    // 请求路径
        "request_method":"get",    //请求方式
    }
    "parameters_verify":"",    //参数校验 请求此代理程序的检验
    "parameters_replace":"",   //参数替换
    "parameters_append":"",    //参数追加
    "api_data_require":"",     // api返回数据准确性校验
    "api_data_replace":"",    // api返回数据替换 用于key替换
    "api_data_append":"",    // api返回数据追加其他参数，返回接口调用者
}
以上为基础配置参数，子参数可以任意添加
删除参数用key对应的value写-1
更改参数数据key对应的value里输入想更改的值