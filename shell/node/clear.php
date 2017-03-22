<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/12/28
 * Time: 11:15
 */

$dir = '/var/ftp/datalog/';
$reg = '/^' . date('ymd', strtotime("-7 day")) . '.+\.log$/';

if (is_dir($dir) && $dh = opendir($dir))
{
    while (($file = readdir($dh)) !== false)
    {
        if ($file != '.' && $file != '..' && preg_match($reg, $file))
        {
//            echo $dir . $file . PHP_EOL;
            unlink($dir . $file);
        }
    }
    closedir($dh);
}

exit(0);