<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/12/28
 * Time: 11:15
 */

//初始化数据库连接
$dsn = 'mysql:host=localhost;dbname=DataLog';
$user = 'datalog';
$passwd = '59%x1W7^';
$conn = new PDO($dsn, $user, $passwd, array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
    PDO::MYSQL_ATTR_LOCAL_INFILE => 1,
));

//获取数据节点
$servers = array();
$sql = 'SELECT INET_NTOA(Host) Host FROM DataNode';
$state = $conn->prepare($sql);
if (!$state->execute())
{
    exit('SQL执行失败');
}
$servers = $state->fetchAll(PDO::FETCH_ASSOC);
if (count($servers) === 0)
{
    exit('请添加数据节点');
}

$data_path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
$reg = '/^' . date('ymd', strtotime("-7 day")) . '.+\.log$/';
foreach ($servers as $server)
{
    $dir = $data_path . $server['Host'] . DIRECTORY_SEPARATOR;

    if (is_dir($dir) && $dh = opendir($dir))
    {
        while (($file = readdir($dh)) !== false)
        {
            if ($file != '.' && $file != '..' && preg_match($reg, $file))
            {
//                echo $dir . $file . PHP_EOL;
                unlink($dir . $file);
            }
        }
        closedir($dh);
    }
}

exit(0);