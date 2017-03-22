<?php
/**
 * Created by PhpStorm.
 * User: Jentely
 * Date: 2015/11/2
 * Time: 15:43
 */
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'production');
}

include('Power_manager.php');

//场景定义，'场景标识' => array('name'=>'场景别名', 'parent'=>'父场景标识')
$scenes = array(
    'datalog_admin' => array('name'=>'日志管理', 'parent'=>''),
);

//权限定义，'权限标识' => array('name'=>'权限别名', 'scene'=>'所属场景标识')
$rights = array(
    'event_log_index'       => array('name'=>'日志查询', 'scene'=>'datalog_admin'),
    'log_analysis_index'    => array('name'=>'日志分析', 'scene'=>'datalog_admin'),
    'system_index'          => array('name'=>'系统管理', 'scene'=>'datalog_admin'),
    'user_type_index'       => array('name'=>'用户管理', 'scene'=>'datalog_admin'),
    'event_type_index'      => array('name'=>'事件管理', 'scene'=>'datalog_admin'),
    'attr_type_index'       => array('name'=>'事件属性管理', 'scene'=>'datalog_admin'),
    'addr_type_index'       => array('name'=>'地点管理', 'scene'=>'datalog_admin'),
);

$params = array(
    'id' => 1, //暂时用不到，请保持默认值
    'account' => 'datalog_admin', //系统管理子账号，请向权限管理系统负责人索取
    'system_id' => 7, //系统编号，在权限管理系统里创建系统后生成
);

$pm = new Power_manager($params);

$result1 = $pm->init_scenes(array('scenes' => json_encode($scenes)));
echo json_encode($result1);
echo "\n";

$result2 = $pm->init_rights(array('rights' => json_encode($rights)));
echo json_encode($result2);
echo "\n";