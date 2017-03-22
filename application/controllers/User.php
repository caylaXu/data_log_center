<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/12
 * Time: 10:03
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('bll/System_bll', 'system_bll');
        $fields = 'Id, Name';
        $this->data['systems'] = $this->system_bll->fetch($fields);

        $this->render('user_index', $this->data);
    }

    public function filter()
    {
        if (!$this->input->is_ajax_request() || !$this->input->get())
        {
            $this->dt_error_return('请求失败');
        }

        //获取输入
        $start = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));
        $system_id = intval($this->input->get('system_id'));

        if ($system_id <= 0 || $start < 0 || $length <= 0)
        {
            $this->dt_error_return('参数错误');
        }

        $this->load->model('bll/User_bll', 'user_bll');
        $fields = 'Id, Name, Mark';
        $where = array('SystemId' => $system_id);
        $result = $this->user_bll->page($fields, $where, $start, $length);

        $this->dt_return($result);
    }

    public function add()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            $this->ajax_return(array('error' => '请求失败'));
        }

        //获取输入并过滤
        $system_id = intval($this->input->post('system_id'));
        $name = $this->input->post('name');
        $mark = $this->input->post('mark');

        //校验输入
        if ($system_id <= 0 ||
            !is_string($name) || strlen($name) <= 0 || strlen($name) > 32 ||
            !is_string($mark) || strlen($mark) <= 0 || strlen($mark) > 32)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        //加载model
        $this->load->model('bll/System_bll', 'system_bll');
        $this->load->model('bll/User_bll', 'user_bll');

        //判断系统是否存在
        $where = array('Id' => $system_id);
        $result = $this->system_bll->exist($where);
        if(!$result)
        {
            $this->ajax_return(array('error' => '该系统不存在'));
        }

        //判断用户是否已定义
        $where = array(
            'SystemId' => $system_id,
            'Name' => $name
        );
        $result = $this->user_bll->exist($where);
        if($result)
        {
            $this->ajax_return(array('error' => '该用户已存在'));
        }

        //添加用户
        $data = array(
            'SystemId' => $system_id,
            'Name' => $name,
            'Mark' => $mark
        );
        $result = $this->user_bll->insert($data);
        if (!$result)
        {
            $this->ajax_return(array('error' => '添加失败，请稍后再试'));
        }

        $this->ajax_return();
    }

    public function add_batch()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            $this->ajax_return(array('error' => '请求失败'));
        }

        $system_id = intval($this->input->post('system_id'));
        $user_types = $this->input->post('user_types');

        if ($system_id <= 0 || !is_string($user_types) || !$user_types)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        $this->load->model('bll/User_bll', 'user_bll');

        $user_types = explode("\n", $user_types);
        $data = array();
        foreach ($user_types as $user_type)
        {
            $user_type = explode(',', trim($user_type));
            if (count($user_type) != 2 || !$user_type[0] || !$user_type[1])
            {
                $this->ajax_return(array('error' => '请正确填写用户定义'));
            }
            $row['SystemId'] = $system_id;
            $row['Name'] = trim($user_type[0]);
            $row['Mark'] = trim($user_type[1]);
            $data[] = $row;
        }
        $result = $this->user_bll->insert_batch($data);
        if (!$result)
        {
            $this->ajax_return(array('error' => '用户添加失败'));
        }

        $this->ajax_return();
    }

    public function edit()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            $this->ajax_return(array('error' => '请求失败'));
        }

        $id = intval($this->input->post('id'));
        $name = $this->input->post('name');
        $mark = $this->input->post('mark');

        if ($id <= 0 ||
            !is_string($name) || strlen($name) <= 0 || strlen($name) > 32 ||
            !is_string($mark) || strlen($mark) <= 0 || strlen($mark) > 32)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        $this->load->model('bll/User_bll', 'user_bll');
        $this->load->model('bll/Event_bll', 'event_bll');

        $fields = 'SystemId';
        $where = array('Id' => $id);
        $result = $this->user_bll->fetch_one($fields, $where);
        if(!$result)
        {
            $this->ajax_return(array('error' => '该用户不存在'));
        }

        $where = array(
            'Id !=' => $id,
            'SystemId' => $result['SystemId'],
            'Name' => $name
        );
        $result = $this->user_bll->exist($where);
        if($result)
        {
            $this->ajax_return(array('error' => '该用户已存在'));
        }

        $data['Name'] = $name;
        $data['Mark'] = $mark;
        $where = array('Id' => $id);
        $result = $this->user_bll->update($data, $where);
        if($result != 1)
        {
            $this->ajax_return(array('error' => '更新失败'));
        }

        $this->ajax_return();
    }

    public function get_list()
    {
        if (!$this->input->is_ajax_request() || !$this->input->get())
        {
            $this->ajax_return(array('error' => '请求失败'));
        }

        $system_id = intval($this->input->get('system_id'));

        if ($system_id <= 0)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        $this->load->model('bll/User_bll', 'user_bll');

        $fields = 'Id, Name, Mark';
        $where = array('SystemId' => $system_id);
        $user_types = $this->user_bll->fetch($fields, $where);
        $data['user_types'] = $user_types;

        $this->ajax_return($data);
    }
}