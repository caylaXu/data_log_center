<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/22
 * Time: 11:17
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Address extends My_Controller
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

        $this->render('address_index', $this->data);
    }

    public function filter()
    {
        if (!$this->input->is_ajax_request() || !$this->input->get())
        {
            $this->ajax_return(array('error' => '请求失败'));
        }

        //获取输入
        $start = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));
        $system_id = intval($this->input->get('system_id'));

        if ($system_id <= 0 || $start < 0 || $length <= 0)
        {
            $this->dt_error_return('参数错误');
        }

        $this->load->model('bll/Address_bll', 'address_bll');
        $this->load->model('bll/Address_event_bll', 'address_event_bll');
        $this->load->model('bll/Event_bll', 'event_bll');

        $fields = 'Id, Name, Mark';
        $where = array('SystemId' => $system_id);
        $result = $this->address_bll->page($fields, $where, $start, $length);
        $result['data'] = array_column($result['data'], NULL, 'Id');

        $addr_ids = array_column($result, 'Id');
        $address_events = $this->address_event_bll->fetch_by_ids('AddrId', $addr_ids);

        $event_ids = array_column($address_events, 'EventId');
        $events = $this->event_bll->fetch_by_ids('Id', $event_ids);
        $events = array_column($events, NULL, 'Id');

        foreach($result['data'] as &$row)
        {
            $row['Events'] = array();
        }
        foreach ($address_events as $address_event)
        {
            if(!isset($result['data'][$address_event['AddrId']]['Events']))
            {
                $result['data'][$address_event['AddrId']]['Events'] = '';
            }
            $result['data'][$address_event['AddrId']]['Events'][] = $events[$address_event['EventId']];
        }
        $result['data'] = array_values($result['data']);

        $this->dt_return($result);
    }

    public function add()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            $this->ajax_return(array('error' => '请求失败'));
        }

        //获取输入
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

        $this->load->model('bll/System_bll', 'system_bll');
        $this->load->model('bll/Address_bll', 'address_bll');

        $where = array('Id' => $system_id);
        $result = $this->system_bll->exist($where);
        if(!$result)
        {
            $this->ajax_return(array('error' => '该系统不存在'));
        }

        $where['SystemId'] = $system_id;
        $where['Name'] = $name;
        $result = $this->address_bll->exist($where);
        if($result)
        {
            $this->ajax_return(array('error' => '该地点已存在'));
        }

        $data['SystemId'] = $system_id;
        $data['Name'] = $name;
        $data['Mark'] = $mark;
        $result = $this->address_bll->insert($data);
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
            $this->return_msg(-1, '请求失败');
        }

        $system_id = $this->input->post('system_id');
        $addr_types = $this->input->post('addr_types');

        if (!$addr_types)
        {
            $this->return_msg(-1, '请填写地点定义');
        }

        $addr_types = explode("\n", $addr_types);
        $data = array();
        foreach ($addr_types as $addr_type)
        {
            $addr_type = explode(',', trim($addr_type));
            if (count($addr_type) != 2 || !$addr_type[0] || !$addr_type[1])
            {
                $this->return_msg(-1, '请正确填写地点');
            }
            $row['SystemId'] = $system_id;
            $row['Name'] = trim($addr_type[0]);
            $row['Mark'] = trim($addr_type[1]);
            $data[] = $row;
        }

        $this->load->model('bll/Address_bll', 'address_bll');
        $result = $this->address_bll->insert_batch($data);

        if (!$result)
        {
            $this->return_msg(-1, '地点添加失败');
        }

        $this->return_msg(0);
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

        $this->load->model('bll/Address_bll', 'address_bll');

        $fields = 'SystemId';
        $where = array('Id' => $id);
        $result = $this->address_bll->fetch_one($fields, $where);
        if(!$result)
        {
            $this->ajax_return(array('error' => '该地点不存在'));
        }

        $where = array(
            'Id !=' => $id,
            'SystemId' => $result['SystemId'],
            'Name' => $name,
        );
        $result = $this->address_bll->exist($where);
        if($result)
        {
            $this->ajax_return(array('error' => '该地点已存在'));
        }

        $data['Name'] = $name;
        $data['Mark'] = $mark;
        $where = array('Id' => $id);
        $result = $this->address_bll->update($data, $where);
        if($result != 1)
        {
            $this->ajax_return(array('error' => '更新失败'));
        }

        $this->ajax_return();
    }

    public function get_list()
    {
        if ($this->input->is_ajax_request() && $this->input->get())
        {
            $system_id = intval($this->input->get('system_id'));

            $this->load->model('bll/Address_bll', 'address_bll');
            $fields = 'Id, Mark';
            $where = array('SystemId' => $system_id);
            $addr_types = $this->address_bll->fetch($fields, $where);
            $data['data'] = $addr_types;

            $this->ajax_return($data);
        }
    }

    public function bind_events()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            return;
        }

        $id = intval($this->input->post('id'));
        $events = $this->input->post('events') ? $this->input->post('events') : array();

        $this->load->model('bll/Address_event_bll', 'address_event_bll');

        $where = array('AddrId' => $id);
        $this->address_event_bll->delete($where);

        if(!$events)
        {
            $this->return_msg(0);
        }

        $data = array();
        foreach($events as $event)
        {
            $row['AddrId'] = $id;
            $row['EventId'] = $event;
            $data[] = $row;
        }
        $result = $this->address_event_bll->insert_batch($data);

        if($result)
        {
            $this->return_msg(0);
        }
        else
        {
            $this->return_msg(1, '绑定失败，请稍后再试');
        }
    }

    public function unbind_event()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            return;
        }

        $addr_id = intval($this->input->post('addr_id'));
        $event_id = intval($this->input->post('event_id'));

        $this->load->model('bll/Address_event_bll', 'address_event_bll');

        $where = array(
            "AddrId" => $addr_id,
            "EventId" => $event_id,
        );

        $this->address_event_bll->delete($where);

        $this->return_msg(0);
    }
}