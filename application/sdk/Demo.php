<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/12/22
 * Time: 17:29
 */

//important
//首先登录业务日志系统(与ENVIRONMENT对应)，在用户定义页下载 Data_log_def.php 到本地
//将 Data_log_def.php 放到 ENVIRONMENT 对应的配置目下

require('Data_log.php');

//系统ID，在业务日志中心申请
$param['system_id'] = 1;

//上报数据的服务器节点配置(测试环境)
$param['data_nodes'] = array(
    array(
        'host' => '123.56.102.104',
        'port' => 22222,
    ),
);

$logger = new Data_Log($param);

$data['EventId'] = DL_Event_type::PAYMENT;      //事件标识，必填
$data['EventAttr'] = array(DL_Attr_type::SOURCE => 'ZFB');   //事件属性，可选
$data['EventDesc'] = 'Pay success';             //事件描述，可选
$data['EventAddr'] = 'test.com';                //事件发生地点
$data['UserId'] = '192.168.1.1';                //用户标识，可选
$data['UserType'] = DL_User_type::USER;         //用户类型，可选
$data['BindUser'] = array(                      //关联用户（最多3个），可选
    array(
        'g123456',                              //用户标识
        DL_User_type::USER,                     //用户类型
    ),
);
$data['HttpRequest'] = 'test.com/cart';         //事件请求的路径，可选
$data['HttpParams'] = array();                  //事件请求的参数，可选
$data['HttpResponse'] = array();                //事件请求的响应，可选
$data['Address'] = '113.951374,22.546429';      //物理地址，可选

$begin = microtime(TRUE) * 1000;

$logger->log($data);    //记录日志

$end = microtime(TRUE) * 1000;
echo ($end - $begin) . PHP_EOL;