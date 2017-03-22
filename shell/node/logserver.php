<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/12/22
 * Time: 17:29
 * Program: 数据节点服务器
 */

class Log_Server
{
	private $node_id;
	
	private $serv;
	
	private $log_path = "/var/ftp/datalog/";
	
	private $host = '0.0.0.0';
	
	private $port = 22222;
	
	private $secret_key = '5tgb^YHN';  //签名私钥
	
	public function __construct($node_id)
	{
		$this->node_id = $node_id;
		
		$this->serv = new swoole_server($this->host, $this->port, SWOOLE_BASE, SWOOLE_SOCK_UDP);
		$this->serv->set(array(
			'daemon' => TRUE,
			'task_worker_num' => 4,
			'max_request' => 1000,
			'debug_mode' => 1,
		));
		
		$this->serv->on('Receive', array($this, 'onReceive'));
		$this->serv->on('Task', array($this, 'onTask'));
		$this->serv->on('Finish', array($this, 'onFinish'));
		
		$this->serv->start();
	}
	
	public function onReceive(swoole_server $serv, $fd, $from_id, $data)
	{
		$this->serv->send($fd, 'success', $from_id);
		$this->serv->task($data);
	}
	
	public function onTask($serv, $task_id, $from_id, $data)
	{
		//验证数据报
		if(!$this->validate($data))
		{
			return ;
		}
		
		//记录日志
		$file_name = substr(date('ymdHi'), 0, -1) . '0.log';
		$file = $this->log_path . $file_name;
		$data = "id=" . uniqid($this->node_id . '.', TRUE) . '&' . $data;
		file_put_contents($file, $data . "\n", FILE_APPEND);
	}
	
	public function onFinish($serv,$task_id, $data)
	{
		
	}
	
	/**
     * 验证数据报
     */
	private function validate($data)
	{
		parse_str($data, $tmp);
		if(!$tmp || !isset($tmp['sign']))
		{
			return FALSE;
		}
		$sign = $this->sign($tmp);
		if($sign !== $tmp['sign'])
		{
			return FALSE;
		}
		return TRUE;
	}
	
	/**
     * 签名
     */
    private function sign($data)
    {
		unset($data['sign']);
        ksort($data);
        return md5(implode('|', $data) . $this->secret_key);
    }

}

if($argc != 2)
{
	$usage = sprintf("Usage: php %s NodeID\n", $argv[0]);;
	exit($usage);
}

$node_id = $argv[1];
$server = new Log_Server($node_id);

