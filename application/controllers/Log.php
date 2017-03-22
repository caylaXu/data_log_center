<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/12/31
 * Time: 9:21
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends My_Controller
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

        if(isset($this->data['system_id']))
        {
            $this->load->model('bll/User_bll', 'user_bll');
            $fields = 'Id, Mark';
            $where = array('SystemId' => $this->data['system_id']);
            $user_types = $this->user_bll->fetch($fields, $where);
            $this->data['user_types'] = $user_types;
        }

        $this->render('log_index', $this->data);
    }

    public function filter()
    {
        if ($this->input->is_ajax_request() && $this->input->get())
        {//异步GET请求
            //获取输入
            $draw = intval($this->input->get('draw'));
            $input['start'] = intval($this->input->get('start'));
            $input['length'] = intval($this->input->get('length'));
            $input['date1'] = $this->input->get('date1');
            $input['date2'] = $this->input->get('date2');
            $input['system_id'] = intval($this->input->get('system_id'));
            $input['user_id'] = $this->input->get('user_id');
            $input['user_type'] = intval($this->input->get('user_type'));
            $input['related'] = $this->input->get('related');
            $input['event_id'] = '';
            $event = $this->input->get('event');

            //检验输入
            if ($input['system_id'] <= 0)
            {
                $res['recordsFiltered'] = $res['recordsTotal'] = 0;
                $res['data'] = array();
                $this->dt_return($res);
            }
            $input['date1'] = strtotime($input['date1']);
            $input['date2'] = strtotime('+1 minutes', strtotime($input['date2'])) - 1;
            if (!$input['date1'] || !$input['date2'] || $input['date1'] > $input['date2'])
            {
                $res['recordsFiltered'] = $res['recordsTotal'] = 0;
                $res['data'] = array();
                $this->dt_return($res);
            }

            $this->load->model('bll/Log_bll', 'log_bll');
            $this->load->model('bll/User_bll', 'user_bll');
            $this->load->model('bll/Event_bll', 'event_bll');

            if ($event)
            {
                $fields = 'Id';
                $where = array(
                    'SystemId' => $input['system_id'],
                    'Mark' => $event
                );
                $row = $this->event_bll->fetch_one($fields, $where);
                $input['event_id'] = $row ? $row['Id'] : intval($event);
            }

            //查出日志
            $res = array();
            $logs = $this->log_bll->get_logs($input);
            if (!$logs)
            {
                $res['recordsFiltered'] = $res['recordsTotal'] = 0;
                $res['data'] = array();
                $this->ajax_return($res);
            }

            $res['data'] = $logs;
            $res['draw'] = $draw;
            $res['recordsFiltered'] = $res['recordsTotal'] = $this->log_bll->get_cnt($input);

            $this->ajax_return($res);
        }
    }
}