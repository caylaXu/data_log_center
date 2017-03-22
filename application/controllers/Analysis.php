<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/18
 * Time: 15:19
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Analysis extends My_Controller
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

        if (isset($this->data['system_id']))
        {
            $this->load->model('bll/Event_bll', 'event_bll');
            $this->load->model('bll/Address_bll', 'address_bll');

            $fields = 'Id, Mark';
            $where = array('SystemId' => $this->data['system_id']);
            $event_types = $this->event_bll->fetch($fields, $where);

            $addr_types = $this->address_bll->fetch($fields, $where);

            $this->data['event_types'] = $event_types;
            $this->data['addr_types'] = $addr_types;
        }

        $this->render('analysis_index', $this->data);
    }

    public function get_event_attrs()
    {
        if (!$this->input->is_ajax_request() || !$this->input->get())
        {
            $this->return_msg(1, '请求失败');
        }

        $input['date1'] = $this->input->get('date1');
        $input['date2'] = $this->input->get('date2');
        $input['system_id'] = intval($this->input->get('system_id'));
        $input['event_id'] = intval($this->input->get('event_id'));
        $input['addr_id'] = intval($this->input->get('addr_id'));
        $input['attr_id'] = intval($this->input->get('attr_id'));

        $input['date1'] = strtotime($input['date1']);
        $input['date2'] = strtotime('+1 day', strtotime($input['date2'])) - 1;
        if (!$input['date1'] || !$input['date2'] || $input['date1'] > $input['date2'])
        {
            $this->return_msg(1, '请正确选择起止日期');
        }

        //获取事件的属性
        $this->load->model('bll/Log_bll', 'log_bll');
        if (date('Ymd', $input['date1']) == date('Ymd', $input['date2']))
        {//获取某一天的事件属性
            $event_attrs = $this->log_bll->get_attrs_today($input);
            $time_step = 3600;
        }
        else
        {
            $event_attrs = $this->log_bll->get_attrs($input);
            $time_step = 86400;
        }

        //属性ID转名称
        $this->load->model('bll/Attribute_bll', 'attribute_bll');
        $fields = 'Id, Mark';
        $where = array('SystemId' => 0);
        $rows = $this->attribute_bll->fetch($fields, $where);
        $attr_defs = array_column($rows, 'Mark', 'Id');
        foreach ($event_attrs as $key => $value)
        {
            if (array_key_exists($key, $attr_defs))
            {
                $event_attrs[$attr_defs[$key]] = $value;
                unset($event_attrs[$key]);
            }
        }

        //生成横坐标
        $categories = $this->log_bll->get_categories($input['date1'], $input['date2'], $time_step);

        $data['status'] = 0;
        $data['categories'] = $categories;
        $data['attrs'] = array_keys($event_attrs);
        $data['event_attrs'] = array_values($event_attrs);

        $this->ajax_return($data);
    }

}