<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/12
 * Time: 10:03
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends My_Controller
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

        $this->render('event_index', $this->data);
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

        $this->load->model('bll/Event_bll', 'event_bll');
        $this->load->model('bll/Event_attribute_bll', 'event_attribute_bll');
        $this->load->model('bll/Attribute_bll', 'attribute_bll');

        $fields = 'Id, Name, Mark, Show';
        $where = array('SystemId' => $system_id);
        $result = $this->event_bll->page($fields, $where, $start, $length);

        $event_ids = array_column($result['data'], 'Id');
        $event_attrs = $this->event_attribute_bll->fetch_by_ids('EventId', $event_ids);

        $attr_ids = array_column($event_attrs, 'AttrId');
        $attrs = $this->attribute_bll->fetch_by_ids('Id', $attr_ids);
        $attrs = array_column($attrs, NULL, 'Id');

        foreach($result['data'] as &$row)
        {
            $row['Attrs'] = array();
        }
        foreach($event_attrs as $event_attr)
        {
            $result['data'][$event_attr['EventId']]['Attrs'][] = $attrs[$event_attr['AttrId']];
        }
        $result['data'] = array_values($result['data']);

        $this->dt_return($result);
    }

    public function fetch()
    {
        if(!$this->input->is_ajax_request() || !$this->input->get())
        {
            return ;
        }

        $id = intval($this->input->get('id'));

        $this->load->model('bll/Address_bll', 'address_bll');
        $this->load->model('bll/Event_bll', 'event_bll');

        $where = array('AddrId' => $id);
        $fields = 'EventId';
        $result = $this->addr_event->fetch($where, $fields);
        $event_ids = array();
        foreach($result as $row)
        {
            $event_ids[] = $row['EventId'];
        }

        $where = array();
        $fields = 'Id, Mark';
        $events = $this->event_bll->fetch($where, $fields);

        foreach($events as &$event)
        {
            if(in_array($event['Id'], $event_ids))
            {
                $event['checked'] = 'checked';
            }
        }

        $result['data'] = $events;

        $this->return_data($result);
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
        $show = intval($this->input->post('show'));

        if ($system_id <= 0 ||
            !is_string($name) || strlen($name) <= 0 || strlen($name) > 32 ||
            !is_string($mark) || strlen($mark) <= 0 || strlen($mark) > 32)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        $this->load->model('bll/System_bll', 'system_bll');
        $this->load->model('bll/Event_bll', 'event_bll');

        $where = array('Id' => $system_id);
        $result = $this->system_bll->exist($where);
        if(!$result)
        {
            $this->ajax_return(array('error' => '该系统不存在'));
        }

        $where = array(
            'SystemId' => $system_id,
            'Name' => $name,
        );
        $result = $this->event_bll->exist($where);
        if ($result)
        {
            $this->ajax_return(array('error' => '该事件已存在'));
        }

        $data = array(
            'SystemId' => $system_id,
            'Name' => $name,
            'Mark' => $mark,
            'Show' => $show
        );
        $result = $this->event_bll->insert($data);
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
        $event_types = $this->input->post('event_types');

        if (!$event_types)
        {
            $this->return_msg(-1, '请填写事件定义');
        }

        $event_types = explode("\n", $event_types);
        $data = array();
        foreach ($event_types as $event_type)
        {
            $event_type = explode(',', trim($event_type));
            if (!$event_type[0] || !$event_type[1])
            {
                $this->return_msg(-1, '请正确填写事件定义');
            }
            $row['SystemId'] = $system_id;
            $row['Name'] = trim($event_type[0]);
            $row['Mark'] = trim($event_type[1]);
            $data[] = $row;
        }

        $this->load->model('bll/Event_bll', 'event_bll');
        $result = $this->event_bll->insert_batch($data);

        if (!$result)
        {
            $this->return_msg(-1, '事件添加失败');
        }

        $this->return_msg(0);
    }

    public function edit()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            $this->return_msg(-1, '请求失败');
        }

        $id = intval($this->input->post('id'));
        $name = $this->input->post('name');
        $mark = $this->input->post('mark');
        $show = intval($this->input->post('show'));

        if ($id <= 0 ||
            !is_string($name) || strlen($name) <= 0 || strlen($name) > 32 ||
            !is_string($mark) || strlen($mark) <= 0 || strlen($mark) > 32)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        $this->load->model('bll/Event_bll', 'event_bll');

        $fields = 'SystemId';
        $where = array('Id' => $id);
        $result = $this->event_bll->fetch_one($fields, $where);
        if (!$result)
        {
            $this->ajax_return(array('error' => '该事件不存在'));
        }

        $where = array(
            'Id !=' => $id,
            'SystemId' => $result['SystemId'],
            'Name' => $name
        );
        $result = $this->event_bll->exist($where);
        if($result)
        {
            $this->ajax_return(array('error' => '该事件已存在'));
        }

        $data['Name'] = $name;
        $data['Mark'] = $mark;
        $data['Show'] = $show;
        $where = array('Id' => $id);
        $result = $this->event_bll->update($data, $where);
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

            $this->load->model('bll/Event_bll', 'event_bll');
            $fields = 'Id, Mark';
            $where = array('SystemId' => $system_id);
            $event_types = $this->event_bll->fetch($fields, $where);
            $data['data'] = $event_types;

            $this->ajax_return($data);
        }
    }

    public function bind_attrs()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            return;
        }

        $id = intval($this->input->post('id'));
        $attrs = $this->input->post('attrs') ? $this->input->post('attrs') : array();

        $this->load->model('bll/Event_attribute_bll', 'event_attribute_bll');

        $where = array('EventId' => $id);
        $this->event_attribute_bll->delete($where);

        if (!$attrs)
        {
            $this->return_msg(0);
        }

        $data = array();
        foreach ($attrs as $attr)
        {
            $row['EventId'] = $id;
            $row['AttrId'] = $attr;
            $data[] = $row;
        }
        $result = $this->event_attribute_bll->insert_batch($data);

        if ($result)
        {
            $this->return_msg(0);
        }
        else
        {
            $this->return_msg(1, '绑定失败，请稍后再试');
        }

    }

    public function unbind_attr()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            return;
        }

        $event_id = intval($this->input->post('event_id'));
        $attr_id = intval($this->input->post('attr_id'));

        $this->load->model('bll/Event_attribute_bll', 'event_attribute_bll');

        $where = array(
            'EventId' => $event_id,
            'AttrId' => $attr_id,
        );
        $this->event_attr->delete($where);

        $this->return_msg(0);
    }

}