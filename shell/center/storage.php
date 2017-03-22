<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/5/4
 * Time: 10:02
 * Program: 日志入库脚本（每10分钟执行一次）
 */

//初始化环境
date_default_timezone_set('PRC');
set_time_limit(180);    //可根据日志量调整（入库100W条日志约需90s）

//定义常量
define("LINES", 10 * 1000);    //每次读取的行数(不能设置过大，否则超过内存限制)
define("RECORDS", 1000000);    //每个日志表存的记录数

class Storage
{
    private $mysql;         //MySQL连接

    private $redis;         //Redis连接
    private $hash_key;      //Hash key

    private $data_nodes;    //数据节点

    private $cur_table;     //当前表

    private $cur_suffix;    //当前表后缀

    private $surplus;       //剩余可插入的条数

    public function __construct()
    {
        //初始化数据库连接
        $this->_init_mysql();
        //初始化Redis连接
        $this->_init_redis();
        //获取数据节点
        $this->_get_data_nodes();
        //获取当前表信息
        $this->_get_cur_table_info();
        //入库
        $this->_store();
        //清除旧缓存（30天前）
        $this->_clear_cache();
    }

    //初始化数据库连接
    private function _init_mysql()
    {
        $dsn = 'mysql:host=localhost;dbname=DataLog';
        $user = 'datalog';
        $passwd = '59%x1W7^';
        $this->mysql = new PDO($dsn, $user, $passwd, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            PDO::MYSQL_ATTR_LOCAL_INFILE => 1,
			PDO::ATTR_PERSISTENT => TRUE,
        ));
        if (!$this->mysql)
        {
            $this->_exit('连接MySQL失败');
        }
    }

    //初始化Redis连接
    private function _init_redis()
    {
        $host = '127.0.0.1';
        $port = 6379;
        $passwd = 'motouch@2015';
        $this->redis = new Redis();
        if (!$this->redis->pconnect($host, $port))
        {
            $this->redis = NULL;
            return ;
        }
        $this->hash_key = 'datalog';
        $this->redis->auth($passwd);
    }

    //获取数据节点
    private function _get_data_nodes()
    {
        $sql = 'SELECT INET_NTOA(Host) Host FROM DataNode';
        $stmt = $this->_mysql_exec($sql, '获取数据节点失败');
        $this->data_nodes = array_column($stmt->fetchAll(), 0);
        if (count($this->data_nodes) === 0)
        {
            $this->_exit('请添加数据节点');
        }
    }

    //获取当前表信息
    private function _get_cur_table_info()
    {
        $this->cur_table = 'EventLog1';
        $this->cur_suffix = 1;
        $this->surplus = RECORDS;
        $sql = "SHOW TABLES LIKE 'EventLog%'";
        $stmt = $this->_mysql_exec($sql, '获取当前表失败');
        $rows = array_column($stmt->fetchAll(), 0);
        if (!$rows)
        {
            $sql = "CREATE TABLE EventLog1 LIKE EventLog_new";
            $this->_mysql_exec($sql, '新建EventLog1失败');
            $this->cur_table = 'EventLog1';
        }
        else
        {
            foreach ($rows as $table)
            {
                $suffix = intval(substr($table, 8));
                if ($suffix < 1 || $suffix > 2000)
                {
                    continue;
                }
                if ($suffix > $this->cur_suffix)
                {
                    $this->cur_suffix = $suffix;
                    $this->cur_table = $table;
                }
            }
            //查询可插入的条数
            $sql = "SELECT COUNT(*) FROM {$this->cur_table}";
            $stmt = $this->_mysql_exec($sql, '获取当前表可插入记录数失败');
            $count = $stmt->fetch()[0];
            if ($count >= RECORDS)
            {
                $this->_create_table();
            }
            else
            {
                $this->surplus -= $count;
            }
        }
    }

    //入库
    private function _store()
    {
        //将文件中的日志入库
        $data_path = dirname(__FILE__) . '/data/';
        $file_name = substr(date('ymdHi', strtotime("-10 minute")), 0, -1) . '0.log';

        $count = 0;    //入库记录计数
        foreach ($this->data_nodes as $node)
        {
            $file = $data_path . $node . '/' . $file_name;
            if (!($fp = @fopen($file, "r")))
            {
                continue;
            }

            while (!feof($fp))
            {
                $lines = array();
                //每LINES行入库；文件末尾入库；当前表满入库
                for ($i = 0; $i < LINES && !feof($fp) && $count < $this->surplus; $i++, $count++)
                {
                    $row = fgets($fp);
                    if (!$row)
                    {
                        continue;
                    }
                    $line = $this->_parse_line($row);
                    if (!$line)
                    {//格式错误，丢弃
                        //丢弃日志
                        parse_str($row, $ary_row);
                        $content = '';
                        foreach ($ary_row as $key => $val)
                        {
                            $content .= $key . ':' . $val . '  ';
                        }
                        $file = __DIR__ . '/drop' . date('Ymd') .'.txt';
                        $content .= date('Y-m-d H:i:s') . "\t" . $content;
                        file_put_contents($file, $content, FILE_APPEND);
                        continue;
                    }
                    $lines[] = $line;
                }
                //执行统计
                $this->_do_count($lines);
                //执行入库
                $this->_do_store($lines);
                //当前表已满
                if ($count >= $this->surplus)
                {
                    $this->_create_table();
                    $count = 0;
                }
            }
        }
    }

    //清除旧缓存
    private function _clear_cache()
    {
        if (!$this->redis)
        {
            return ;
        }

        $reference = strtotime(date('Ymd', strtotime('-30 days')));
        $keys = $this->redis->hKeys($this->hash_key);
        foreach ($keys as $key)
        {
            $key_arr = explode('_', $key);
            if (count($key_arr) != 6 || $key_arr[2] < $reference)
            {
                $this->redis->hDel($this->hash_key, $key);
            }
        }
    }

    //创建新表
    private function _create_table()
    {
        $this->cur_suffix += 1;
        $this->cur_table = 'EventLog' . $this->cur_suffix;
        $sql = "CREATE TABLE {$this->cur_table} LIKE EventLog_new";
        $this->_mysql_exec($sql, '新建日志表失败');
        $this->surplus = RECORDS;
        //写入映射表
        $sql = "SELECT Id FROM RltTimeTable WHERE TABLENAME='{$this->cur_table}'";
        $stmt = $this->_mysql_exec($sql, '获取映射表信息失败');
        if ($stmt->fetch())
        {
            return ;
        }
        $sql = "INSERT INTO RltTimeTable(MinTime, MaxTime, TableName)" .
            " VALUES (0, 0, '{$this->cur_table}')";
        $this->_mysql_exec($sql, '写入映射表失败');
    }

    //解析日志中的一行
    private function _parse_line($line)
    {
        $indexes = array(
            'Guid' => 'id',
            'SystemId' => 'a',
            'UserId' => 'b',
            'UserType' => 'c',
            'EventId' => 'd',
            'EventAttr' => 'e',
            'EventDesc' => 'f',
            'EventTime' => 'g',
            'EventAddr' => 'h',
            'HttpRequest' => 'i',
            'HttpParams' => 'j',
            'HttpResponse' => 'k',
            'BindUser' => 'l',
            'Address' => 'm',
            'IP' => 'n',
        );
        $default = array(
            'Guid' => '',
            'SystemId' => '0',
            'UserId' => '',
            'UserType' => '',
            'EventId' => '0',
            'EventAttr' => '',
            'EventDesc' => '',
            'EventTime' => time(),
            'EventAddr' => '0',
            'HttpRequest' => '',
            'HttpParams' => '',
            'HttpResponse' => '',
            'BindUser' => '[]',
            'Address' => '',
            'IP' => '0',
        );

        parse_str($line, $ary_line);
        //数据校验
        if (!isset($ary_line['id']) ||
            intval($ary_line['a']) <= 0 ||
            intval($ary_line['d']) <= 0)
        {//校验SystemId, EventId
            return FALSE;
        }
        $attr_keys = array_keys(json_decode($ary_line['e'], TRUE));
        foreach ($attr_keys as $key)
        {//校验EventAttr
            if (intval($key) <= 0)
            {
                return FALSE;
            }
        }

        foreach ($indexes as $field => $key)
        {
            if ($field === 'BindUser')
            {
                $ary_line[$key] = isset($ary_line[$key]) ? json_decode($ary_line[$key], true) : array();
                if (!is_array($ary_line[$key]))
                {
                    $ary_line[$key] = array();
                }
                for ($i = 0; $i < 3; $i++)
                {
                    $ary_tmp['BindUserId' . $i] =
                        isset($ary_line[$key][$i]) && isset($ary_line[$key][$i][0]) ?
                            (string)$ary_line[$key][$i][0] : '';
                    $ary_tmp['BindUserType' . $i] =
                        isset($ary_line[$key][$i]) && isset($ary_line[$key][$i][1]) ?
                            (string)$ary_line[$key][$i][1] : 0;
                }
            }
            else if ($field === 'IP')
            {
                if (!isset($ary_line[$key]))
                {
                    $ary_line[$key] = $default[$field];
                }
                $ary_tmp[$field] = sprintf("%u", ip2long($ary_line[$key]));
            }
            else if ($field === 'EventTime')
            {
                $time = strtotime($ary_line[$key]);
                $ary_tmp[$key] = $time ? $time : $default[$field];
            }
			else if ($field === 'EventAddr')
            {
                $addr_id = intval($ary_line[$key]);
                $ary_tmp[$key] = $addr_id > 0 ? $addr_id : $default[$field];
            }
            else
            {
                $ary_tmp[$field] = isset($ary_line[$key]) && $ary_line[$key] ? $ary_line[$key] : $default[$field];
            }
        }

        return implode("\t", array_values($ary_tmp));
    }

    //执行统计
    private function _do_count($lines)
    {
        //统计信息
        $hash_day = array();
        $hash_hour = array();
        foreach ($lines as $line)
        {
            $ary_line = explode("\t", $line);
            $system_id = $ary_line[1];
            $event_id = $ary_line[4];
            $event_attrs = json_decode($ary_line[5], TRUE);
            $ary_time = getdate($ary_line[7]);
            $event_time_day = mktime(0, 0, 0, $ary_time['mon'], $ary_time['mday'], $ary_time['year']);  //统计周期为一天
            $event_time_hour = mktime($ary_time['hours'], 0, 0, $ary_time['mon'], $ary_time['mday'], $ary_time['year']);  //统计周期为一天
            $event_addr = $ary_line[8];
            foreach ($event_attrs as $attr => $value)
            {
                $hash_field_day = $system_id . '_' . $event_id . '_' . $event_time_day . '_' . $event_addr . '_' . $attr . '_' . $value;
                $hash_field_hour = $system_id . '_' . $event_id . '_' . $event_time_hour . '_' . $event_addr . '_' . $attr . '_' . $value;

                //按天统计
                if (isset($hash_day[$hash_field_day]))
                {
                    $hash_day[$hash_field_day]++;
                }
                else
                {
                    $sql = "SELECT Count FROM Statistic WHERE " .
                        "SystemId='{$system_id}' AND EventId='{$event_id}' AND EventTime='{$event_time_day}' AND " .
                        "AddrId='{$event_addr}' AND AttrId='{$attr}' AND VALUE='{$value}'";
                    $stmt = $this->_mysql_exec($sql, '获取MySQL统计信息失败1');
                    $statistic_row = $stmt->fetch();
                    $hash_day[$hash_field_day] = $statistic_row ? ($statistic_row[0] + 1) : 1;
                }

                //按小时统计当天数据
                if (!$this->redis)
                {
                    continue;
                }
                if (isset($hash_hour[$hash_field_hour]))
                {
                    $hash_hour[$hash_field_hour]++;
                }
                else
                {
                    $statistic_row = $this->redis->hGet($this->hash_key, $hash_field_hour);
                    $hash_hour[$hash_field_hour] = $statistic_row ? ($statistic_row[0] + 1) : 1;
                }
            }
        }

        //写入数据库
        foreach ($hash_day as $field => $count)
        {
            $ary_tmp = explode('_', $field);
            $sql = "SELECT Id FROM Statistic WHERE " .
                "SystemId='{$ary_tmp[0]}' AND EventId='{$ary_tmp[1]}' AND EventTime='{$ary_tmp[2]}' AND " .
                "AddrId='{$ary_tmp[3]}' AND AttrId='{$ary_tmp[4]}' AND VALUE='{$ary_tmp[5]}'";
            $stmt = $this->_mysql_exec($sql, '获取MySQL统计信息失败2');
            $statistic_row = $stmt->fetch();
            if (!$statistic_row)
            {
                foreach ($ary_tmp as &$column)
                {
                    $column = '\'' . $column . '\'';
                }
                $values = '(' . implode(',', $ary_tmp) . ", {$count})";
                $sql = "INSERT INTO Statistic" .
                    "(SystemId, EventId, EventTime, AddrId, AttrId, Value, Count)" .
                    " VALUES {$values}";
                $errInfo = "插入MySQL统计信息失败";
            }
            else
            {
                $sql = "UPDATE Statistic SET Count = {$count} WHERE Id = {$statistic_row['Id']}";
                $errInfo = "更新MySQL统计信息失败";
            }
            $this->_mysql_exec($sql, $errInfo);
        }
        //写入Redis
        if ($this->redis)
        {
            $this->redis->hMSet($this->hash_key, $hash_hour);
        }
    }

    //执行入库
    private function _do_store($lines)
    {
        //按格式写入临时文件
        $tmp_file = __DIR__ . '/data/tmp.txt';
        $storage = implode("\n", $lines);
        file_put_contents($tmp_file, $storage);

        //加载到数据库
        $sql = "LOAD DATA LOCAL INFILE '{$tmp_file}' " .
            "IGNORE INTO TABLE {$this->cur_table} FIELDS TERMINATED BY '\t'" .
            "(Guid, SystemId, UserId, UserType, " .
            "EventId, EventAttr, EventDesc, EventTime, EventAddr, " .
            "HttpRequest, HttpParams, HttpResponse, " .
            "BindUserId1, BindUserType1, BindUserId2, BindUserType2, " .
            "BindUserId3, BindUserType3, Address, IP)";
        $this->_mysql_exec($sql, 'LOAD DATA INFILE失败');
        unlink($tmp_file);

        //更新映射表
        $sql = "SELECT MIN(EventTime), MAX(EventTime) FROM {$this->cur_table}";
        $stmt = $this->_mysql_exec($sql, '查询时间跨度失败');
        $eventTime = $stmt->fetch();
        if (!$eventTime[0] || !$eventTime[1])
        {
            return ;
        }
        $sql = "UPDATE RltTimeTable SET " .
            "MinTime = {$eventTime[0]}, MaxTime = {$eventTime[1]} " .
            "WHERE TableName = '{$this->cur_table}'";
        $this->_mysql_exec($sql, '更新映射表失败');
    }

    //执行SQL语句
    private function _mysql_exec($sql, $errInfo)
    {
        $stmt = $this->mysql->prepare($sql);
        if ($stmt->execute() === FALSE)
        {
            $this->_exit($errInfo . "\t" . $sql);
        }
        return $stmt;
    }

    //带时间戳终止
    private function _exit($info)
    {
        die(date('Y-m-d H:i:s') . "\t" . $info . PHP_EOL);
    }
}

$storage = new Storage();