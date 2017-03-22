<?php

/**
 * 权限管理器
 * Created by PhpStorm.
 * User: Jentely
 * Date: 2015/10/29
 * Time: 16:51
 */
class Power_manager
{
    private $id = 0; //用户ID
    private $account = ''; //用户账号
    private $system_id = 0; //系统编号

    private $secret_key = 'motouch_2015'; //签名秘钥

    private $api_init_scene = 'api/init_scenes'; //场景初始化接口
    private $api_init_right = 'api/init_rights'; //权限初始化接口
    private $api_query_right = 'api/query_rights'; //权限查询接口

    private $api_uri = array(
		'dev'  => 'http://pms.dev', //开发环境
		'test' => 'http://pms.gdog.com.cn', //测试环境
		'pre'  => 'http://pms.pre.motouch.cn', //预发布环境
		'pro'  => 'http://pms.motouch.cn' //生产环境
	);

    private $rights = array(); //权限列表(本地缓存)

    public function __construct($params)
    {
        $this->id = $params['id'];
        $this->account = $params['account'];
        $this->system_id = $params['system_id'];
    }

    /**
     * 判断有没有权限(本地数据)
     * 调用此接口，须先调用query_by_scene
     * @param $right string 权限全称
     * @return bool
     */
    public function check_rights($right)
    {
        if (!$right) return false;
        if (!isset($this->rights[$right])) return false;
        return true;
    }

    /**
     * 查询用户的某项权限(发起请求)
     * @param $right string 权限全称
     * @return bool
     */
    public function query_rights($right)
    {
        if ($this->check_rights($right)) return true;

        if (!$right) return false;

        $result = $this->exec_query_rights(array('right' => $right));

        if (!$result || $result['ret']) return false;
        if (!isset($result['data']) || !$result['data']) return false;

        $this->rights[$right] = 1;

        return true;
    }

    /**
     * 查询用户在某个场景下的权限列表
     * @param $scene string 场景名称
     */
    public function query_by_scene($scene)
    {
        $result = $this->exec_query_rights(array('scene' => $scene));

        if (!$result || $result['ret']) return;
        if (!isset($result['data']) || !$result['data']) return;

        foreach ($result['data'] as $right) {
            $this->rights[$right] = 1;
        }
    }

    /**
     * 向权限管理中心查询权限数据
     * @param $params
     * @return array
     */
    private function exec_query_rights($params)
    {
        return $this->request($this->api_query_right, $params);
    }

    /**
     * 向权限管理中心初始化场景
     * @param $params
     * @return array
     */
    public function init_scenes($params)
    {
        return $this->request($this->api_init_scene, $params);
    }

    /**
     * 向权限管理中心初始化权限
     * @param $params
     * @return array
     */
    public function init_rights($params)
    {
        return $this->request($this->api_init_right, $params);
    }

    /**
     * 向权限管理中心请求数据
     * @param $api string
     * @param $params array
     * @return bool|array
     */
    private function request($api, $params)
    {
        if (!$this->id || !$this->account) return array();

        $url = $this->get_uri() . '/' . $api;

        $params['system_id'] = $this->system_id;
        $params['session_id'] = $this->make_session_id();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($ch);
        
        if ($result == null) return false;

        $result = json_decode($result, true);

        if (!$result) return false;

        return $result;
    }

    private function get_env()
    {
        $current_host = $_SERVER['HTTP_HOST'];
		
		if (strpos($current_host, '.dev') !== FALSE)
		{
			return 'dev';
		}
		elseif (strpos($current_host, '.gdog.com.cn') !== FALSE)
		{
			return 'test';
		}
		elseif (strpos($current_host, '.pre.motouch.cn') !== FALSE)
		{
			return 'pre';
		}
		elseif (strpos($current_host, '.motouch.cn') !== FALSE)
		{
			return 'pro';
		}
		else
		{
			return 'pro';
		}
    }

    private function get_uri()
    {
        $env = $this->get_env();
		
		return $this->api_uri[$env];
    }

    /**
     * 生成请求验证串
     * @return string
     */
    private function make_session_id()
    {
        $data = array();
        $data[] = time() + 60;
        $data[] = $this->id;
        $data[] = base64_encode($this->account);
        $data[] = $this->sign($data);

        $session_id = base64_encode(implode('_', $data));

        return $session_id;
    }

    /**
     * 验证签名
     * @param $data array
     * @return string
     */
    private function sign($data)
    {
        return md5(implode('|', $data) . '_' . $this->secret_key);
    }
}