<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/25
 * Time: 16:06
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Attribute extends My_Controller
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

        $this->render('attribute_index', $this->data);
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
        /*$system_id = intval($this->input->get('system_id'));*/
        $system_id = 0;

        if (/*$system_id <= 0 || */$start < 0 || $length <= 0)
        {
            $this->dt_error_return('参数错误');
        }

        $this->load->model('bll/Attribute_bll', 'attribute_bll');
        $fields = 'Id, Name, Mark';
        $where = array('SystemId' => $system_id);
        $result = $this->attribute_bll->page($fields, $where, $start, $length);

        $this->dt_return($result);

    }

    public function fetch()
    {
        if(!$this->input->is_ajax_request() || !$this->input->get())
        {
            return ;
        }

        $id = intval($this->input->get('id'));

        $this->load->model('bll/Event_attribute_bll', 'event_attribute_bll');
        $this->load->model('bll/Attribute_bll', 'attribute_bll');

        $where = array('EventId' => $id);
        $fields = 'AttrId';
        $result = $this->event_attribute_bll->fetch($where, $fields);
        $attr_ids = array();
        foreach($result as $row)
        {
            $attr_ids[] = $row['AttrId'];
        }

        $where = array();
        $fields = 'Id, Mark';
        $attrs = $this->attribute_bll->fetch($where, $fields);

        foreach($attrs as &$attr)
        {
            if(in_array($attr['Id'], $attr_ids))
            {
                $attr['checked'] = 'checked';
            }
        }

        $result['data'] = $attrs;

        $this->return_data($result);
    }

    public function add()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            $this->ajax_return(array('error' => '请求失败'));
        }

        //获取输入
        $name = $this->input->post('name');
        $mark = $this->input->post('mark');
        /*$system_id = $this->input->post('system_id');*/
        $system_id = 0;

        if (/*$system_id <= 0 ||*/
            !is_string($name) || strlen($name) <= 0 || strlen($name) > 32 ||
            !is_string($mark) || strlen($mark) <= 0 || strlen($mark) > 32)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        $this->load->model('bll/System_bll', 'system_bll');
        $this->load->model('bll/Attribute_bll', 'attribute_bll');

        /*$where = array('Id' => $system_id);
        $result = $this->system_bll->exist($where);
        if(!$result)
        {
            $this->ajax_return(array('error' => '该系统不存在'));
        }*/

        $where = array(
            'SystemId' => $system_id,
            'Name' => $name,
        );
        $result = $this->attribute_bll->exist($where);
        if($result)
        {
            $this->ajax_return(array('error' => '该属性已存在'));
        }

        $data = array(
            'SystemId' => $system_id,
            'Name' => $name,
            'Mark' => $mark
        );
        $result = $this->attribute_bll->insert($data);
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

//        $system_id = $this->input->post('system_id');
        $system_id = 0;
        $attr_types = $this->input->post('attr_types');

        if (!$attr_types)
        {
            $this->return_msg(-1, '请填写地点定义');
        }

        $attr_types = explode("\n", $attr_types);
        $data = array();
        foreach ($attr_types as $attr_type)
        {
            $attr_type = explode(',', trim($attr_type));
            if (count($attr_type) != 2 || !$attr_type[0] || !$attr_type[1])
            {
                $this->return_msg(-1, '请正确填写地点');
            }
            $row['SystemId'] = $system_id;
            $row['Name'] = trim($attr_type[0]);
            $row['Mark'] = trim($attr_type[1]);
            $data[] = $row;
        }

        $this->load->model('bll/Attribute_bll', 'attribute_bll');
        $result = $this->attribute_bll->insert_batch($data);

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

        $this->load->model('bll/Attribute_bll', 'attribute_bll');

        $fields = 'SystemId';
        $where = array('Id' => $id);
        $result = $this->attribute_bll->fetch_one($fields, $where);
        if(!$result)
        {
            $this->ajax_return(array('error' => '该地点不存在'));
        }

        $where = array(
            'Id !=' => $id,
            'SystemId' => $result['SystemId'],
            'Name' => $name
        );
        $result = $this->attribute_bll->exist($where);
        if($result)
        {
            $this->ajax_return(array('error' => '该地点名称已存在'));
        }

        $data['Name'] = $name;
        $data['Mark'] = $mark;
        $where = array('Id' => $id);
        $result = $this->attribute_bll->update($data, $where);
        if($result != 1)
        {
            $this->ajax_return(array('error' => '更新失败'));
        }

        $this->ajax_return();
    }

    public function get_attributes()
    {
        if(!$this->input->is_ajax_request() || !$this->input->get())
        {
            $this->ajax_return();
        }

        $system_id = intval($this->input->get('system_id'));
        $event_id = intval($this->input->get('event_id'));
        $addr_id = intval($this->input->get('addr_id'));
        $start = strtotime($this->input->get('start'));
        $end = strtotime($this->input->get('end'));

        if (!$start || !$end || $end < $start)
        {
            $this->ajax_return();
        }

        $this->load->model('bll/Statistic_bll', 'statistic_bll');
        $fields = 'AttrId';
        $where = array(
            'SystemId' => $system_id,
            'EventId' => $event_id,
            'AddrId' => $addr_id,
            'EventTime >=' => $start,
            'EventTime <=' => $end,
        );
        $attr_ids = array_unique(array_column($this->statistic_bll->fetch($fields, $where), 'AttrId'));
        if (!$attr_ids)
        {
            $this->ajax_return();
        }

        $this->load->model('bll/Attribute_bll', 'attribute_bll');
        $reference = 'Id';
        $fields = 'Id, Mark';
        $attributes = $this->attribute_bll->fetch_by_ids($reference, $attr_ids, $fields);

        $result['data'] = $attributes;

        $this->ajax_return($result);
    }

}