<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/12/22
 * Time: 16:28
 */

defined('APPPATH') or define('APPPATH', dirname(__DIR__));
defined('ENVIRONMENT') or define('ENVIRONMENT', 'production');

$def_file = APPPATH . '/config/' . ENVIRONMENT . '/Data_log_def.php';

if (file_exists($def_file))
{
    require_once($def_file);
}
else
{
    $def_file = APPPATH . '/config/Data_log_def.php';

    if (file_exists($def_file))
    {
        require_once($def_file);
    }
    else
    {
        throw new Exception("Data_log_def.php not exist in config dir", 1000);
    }
}

class Data_Log
{
    private $sock = NULL;

    private $system_id = 0;     //系统标识

    private $local_log = '';   //发送失败，日志存本地

    private $data_nodes = array();  //数据节点列表

    private $secret_key = '5tgb^YHN';  //签名私钥

    public function __construct($param)
    {
        $this->system_id = $param['system_id'];
		$this->data_nodes = $param['data_nodes'];

		if (defined('APPPATH'))
		{
			$this->local_log = APPPATH . '/logs/event_data.log';
		}
        else
		{
			$this->local_log = '/home/wwwlogs/event_data.log';
		}
    }

    /**
     * 发送日志到数据节点
     * @param array $param
     * 元素说明：
     * @elem string   UserId       //用户标识
     * @elem int      UserType     //用户类型
     * @elem int      EventId      //事件标识(必填)
     * @elem array    EventAttr    //事件属性
     * @elem array    BindUser     //关联用户（最多3个）[[UserId1, UserType1], [UserId2, UserType2], [UserId3, UserType2]]
     * @elem string   EventDesc    //事件描述
     * @elem string   HttpRequest  //事件请求的路径
     * @elem array    HttpParams   //事件请求的参数
     * @elem array    HttpResponse //事件请求的响应
     * @elem string   Address      //物理地址
     * @return bool
     */
    public function log($param)
    {
        $data = $this->check_data($param);

        if (!$data) return FALSE;

        //数据前面，防止伪造
        $data['sign'] = $this->sign($data);

        return $this->send_data($data);
    }

    /**
     * 校正上报数据
     * @param $param array
     * @return array|bool
     */
    private function check_data($param)
    {
        if (!is_array($param)) return FALSE;
        if (!isset($param['EventId'])) return FALSE;
        if (!is_numeric($param['EventId'])) return FALSE;
        if ($param['EventId'] <= 0) return FALSE;
        $data = array();
        $data['a'] = (int)$this->system_id;
        $data['b'] = isset($param['UserId']) ? (string)$param['UserId'] : '';
        $data['c'] = isset($param['UserType']) ? (int)$param['UserType'] : 0;
        $data['d'] = (int)$param['EventId'];
        $data['e'] = isset($param['EventAttr']) && is_array($param['EventAttr']) ? json_encode($param['EventAttr'], JSON_UNESCAPED_UNICODE) : '';
        $data['f'] = isset($param['EventDesc']) ? (string)$param['EventDesc'] : '';
        $data['g'] = date('Y-m-d H:i:s');
        $data['h'] = isset($param['EventAddr']) ? (string)$param['EventAddr'] : '';
        $data['i'] = isset($param['HttpRequest']) ? (string)$param['HttpRequest'] : '';
        $data['j'] = isset($param['HttpParams']) && is_array($param['HttpParams']) ? http_build_query($param['HttpParams']) : '';
        $data['k'] = isset($param['HttpResponse']) && is_array($param['HttpResponse']) ? json_encode($param['HttpResponse'], JSON_UNESCAPED_UNICODE) : '';
        $data['l'] = isset($param['BindUser']) && is_array($param['BindUser']) ? json_encode($param['BindUser'], JSON_UNESCAPED_UNICODE) : '';
        $data['m'] = isset($param['Address']) ? (string)$param['Address'] : '';
        $data['n'] = $this->get_client_ip();
        return $data;
    }

    /**
     * 向远程UDP数据节点发送数据
     * @param array $data
     * @return bool
     */
    private function send_data($data)
    {
        $msg = http_build_query($data);

        $node = $this->get_data_node(); //随机一个数据节点

        $this->create_socket();

        socket_sendto($this->sock, $msg, strlen($msg), 0, $node['host'], $node['port']);

        if(!@socket_recv($this->sock, $ret, 1024, 0))
        {
            $success = FALSE;

            foreach($this->data_nodes as $node)
            {
                $this->create_socket();

                socket_sendto($this->sock, $msg, strlen($msg), 0, $node['host'], $node['port']);

                if(@socket_recv($this->sock, $ret, 1024, 0))
                {
                    $success = TRUE;
                    break;
                }
            }
            if(!$success)
            {
                file_put_contents($this->local_log, $msg . "\n", FILE_APPEND);
            }
        }
        else
        {
            $success = TRUE;
        }

        socket_close($this->sock);

        $this->sock = NULL;

        return $success;
    }

    /**
     * 创建套接字
     */
    private function create_socket()
    {
		if ($this->sock)
		{
			socket_close($this->sock);
            $this->sock = NULL;
		}
        $this->sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, array('sec'=> 0, 'usec'=>100 * 1000));
    }

    /**
     * 随机获取数据节点
     * @return mixed
     */
    private function get_data_node()
    {
        $index = rand(0, count($this->data_nodes) - 1);
        return $this->data_nodes[$index];
    }

    /**
     * 获取客户端IP地址
     * @return string
     */
    private function get_client_ip()
    {
        $ip = "";

        if (isset($_SERVER['REMOTE_ADDR']))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * 签名
     * @param array $data
     * @return string
     */
    private function sign($data)
    {
        ksort($data);
        return md5(implode('|', $data) . $this->secret_key);
    }
}
