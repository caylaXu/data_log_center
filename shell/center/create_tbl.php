<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/12/28
 * Time: 9:45
 */

//初始化环境
date_default_timezone_set('PRC');

//初始化数据库连接
$dsn = 'mysql:host=localhost;dbname=DataLog';
$user = 'datalog';
$passwd = '59%x1W7^';
$conn = new PDO($dsn, $user, $passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));

$tbl_name = 'EventLog' . date('Ym', strtotime("+1 month"));
$sql = "CREATE TABLE IF NOT EXISTS `{$tbl_name}` LIKE `EventLog`";

if($conn->exec($sql) === FALSE)
{
  echo $conn->errorInfo()[2];
  exit(-1);
}

exit(0);