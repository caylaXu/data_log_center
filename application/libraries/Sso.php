<?php

/**
 * 单点登录SDK
 * Created by PhpStorm.
 * User: Jentely
 * Date: 2015/11/17
 * Time: 16:20
 */
class Sso
{
    private $id = 0; //用户ID
    private $account = ''; //用户账号
    private $system_id = 0; //系统编号
    private $session_key = 'sso_session_id';
    private $secret_key = 'motouch_2015';

	private $sso_hosts = array(
		'dev'  => 'http://sso.dev', //开发环境
		'test' => 'http://sso.gdog.com.cn', //测试环境
		'pre'  => 'http://sso.pre.motouch.cn', //预发布环境
		'pro'  => 'http://sso.motouch.cn' //生产环境
	);
	
    private $sso_api_login = '/view/login'; //sso登录
    private $sso_api_reset_pwd = '/view/password'; //sso密码重置
    private $sso_api_logout = '/user/log_out'; //sso登出

    public function __construct($params)
    {
        $this->system_id = $params['system_id'];
        $this->session_key .= '_' . $this->get_env();
        $this->session_key .= '_' . $this->system_id;
    }

    /**
     * 获取用户ID
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * 获取用户账号
     * @return string
     */
    public function account()
    {
        return $this->account;
    }

    /**
     * 获取当前URL
     * @return string
     */
    public function get_curr_url()
    {
        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://');
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80')
        {
            $current_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        }
        else
        {
            $current_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }
        return $current_url;
    }
	
	/**
     * 获取环境
     * @return string
     */
	public function get_env()
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
	
	public function get_sso_host()
	{
		$env = $this->get_env();
		return $this->sso_hosts[$env];
	}

    /**
     * 前往统一登录页面
     */
    public function request_login($redirect_url = '')
    {
        $redirect_url = $redirect_url ? $redirect_url : $this->get_curr_url();
        $param = '?system_id=' . $this->system_id . '&redirect_url=' . urlencode($redirect_url);
        header("Location: " . $this->get_sso_host() . $this->sso_api_login . $param);
        exit();
    }

    /**
     * 检查登录态
     * @return bool
     */
    public function check_login()
    {
        $session_id = $this->get_session_id();
        if (!$session_id)
        {
            return false;
        }
        if (!($session = $this->parse($session_id)))
        {
            setcookie($this->session_key, '', time() - 1, '/', null, null, true);
            return false;
        }

        $this->id = $session['Id'];
        $this->account = $session['Account'];

        return true;
    }

    /**
     * 获取SESSION ID
     * @return mixed
     */
    private function get_session_id()
    {
        $param = $_POST ? $_POST : $_GET;

        $session_id = isset($_COOKIE[$this->session_key]) ? $_COOKIE[$this->session_key] : null;

        if (!$session_id && isset($param['session_id']))
        {
            $session_id = $param['session_id'];
            setcookie($this->session_key, $session_id, time() + 86400, '/', null, null, true);
        }

        return $session_id;
    }

    /**
     * 解析SESSION ID
     * @param $session_id
     * @return array|null
     */
    private function parse($session_id)
    {
        $session = array();

        $data = explode('_', base64_decode($session_id));
        if (count($data) < 4)
        {
            return null;
        }
        if ($data[0] < time())
        {
            return null;
        }

        $sign = array_pop($data);
        if ($sign != $this->sign($data))
        {
            return null;
        }

        $session['Id'] = $data[1];
        $session['Account'] = base64_decode($data[2]);

        return $session;
    }

    /**
     * 数字签名
     * @param $data
     * @return string
     */
    private function sign($data)
    {
        return md5(implode('|', $data) . '_' . $this->secret_key);
    }

    /**
     * 密码重置
     */
    public function reset_pwd()
    {
        header("Location: " . $this->get_sso_host() . $this->sso_api_reset_pwd);
        exit();
    }

    /**
     * 登出
     */
    public function log_out()
    {
        setcookie($this->session_key, '', time() - 1, '/', null, null, true);

        header("Location: " . $this->get_sso_host() . $this->sso_api_logout);
        exit();
    }
}