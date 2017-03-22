<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/14
 * Time: 15:55
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class System extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->render('system_index', $this->data);
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

        if ($start < 0 || $length <= 0)
        {
            $this->dt_error_return('参数错误');
        }

        $this->load->model('bll/System_bll', 'system_bll');
        $fields = 'Id, Name';
        $where = array();
        $result = $this->system_bll->page($fields, $where, $start, $length);

        $this->dt_return($result);

    }

    public function add()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            $this->ajax_return(array('error' => '请求失败'));
        }

        $name = $this->input->post('name');

        if (!is_string($name) || strlen($name) <= 0 || strlen($name) > 32)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        $this->load->model('bll/System_bll', 'system_bll');

        $where = array('Name' => $name);
        $result = $this->system_bll->exist($where);
        if($result)
        {
            $this->ajax_return(array('error' => '该系统已存在'));
        }

        $data = array('Name' => $name);
        $result = $this->system_bll->insert($data);
        if (!$result)
        {
            $this->ajax_return(array('error' => '添加失败，请稍后再试'));
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

        if ($id <= 0 ||
            !is_string($name) || strlen($name) <= 0 || strlen($name) > 32)
        {
            $this->ajax_return(array('error' => '参数错误'));
        }

        $this->load->model('bll/System_bll', 'system_bll');
        $where = array('Id' => $id);
        $result = $this->system_bll->exist($where);
        if(!$result)
        {
            $this->ajax_return(array('error' => '该系统不存在'));
        }

        $fields = 'Id';
        $where = array('Name' => $name, 'Id !=' => $id);
        $result = $this->system_bll->fetch_one($fields, $where);
        if($result)
        {
            $this->ajax_return(array('error' => '该名称已存在'));
        }

        $data = array('Name' => $name);
        $where = array('Id' => $id);
        $result = $this->system_bll->update($data, $where);
        if($result != 1)
        {
            $this->ajax_return(array('error' => '更新失败'));
        }

        $this->ajax_return();
    }

    public function download()
    {
        $this->load->model('bll/System_bll', 'system_bll');
        $fields = 'Id, Name';
        $this->data['systems'] = $this->system_bll->fetch($fields);

        $this->render('download', $this->data);
    }

    public function do_download()
    {
        if($this->input->get())
        {
            $system_id = intval($this->input->get('system_id'));

            if ($system_id === 0)
            {
                $system_id = intval($this->input->cookie('system_id'));
            }

            if ($system_id <= 0)
            {
                return ;
            }

            $content = '<?php' . PHP_EOL;
            $content .= '/**' . PHP_EOL;
            $content .= ' * Created by DataLog system.' . PHP_EOL;
            $content .= ' * Date: ' . date('Y/m/d') . PHP_EOL;
            $content .= ' * Time: ' . date('H:i') . PHP_EOL;
            $content .= ' */' . PHP_EOL . PHP_EOL;

            //用户类型
            $this->load->model('bll/User_bll', 'user_bll');
            $fields = 'Id, Name, Mark';
            $where = array('SystemId' => $system_id);
            $result = $this->user_bll->fetch($fields, $where);
            $content .= 'class DL_User_type ' . PHP_EOL;
            $content .= '{' . PHP_EOL;
            foreach($result as $row)
            {
                $content .= sprintf("\tconst\t%-32s = %-3s;\t//%s" . PHP_EOL,
                    strtoupper($row['Name']), $row['Id'], $row['Mark']);
            }
            $content .= '}' . PHP_EOL . PHP_EOL;

            //事件类型
            $this->load->model('bll/Event_bll', 'event_bll');
            $fields = 'Id, Name, Mark';
            $where = array('SystemId' => $system_id);
            $result = $this->event_bll->fetch($fields, $where);
            $content .= 'class DL_Event_type ' . PHP_EOL;
            $content .= '{' . PHP_EOL;
            foreach($result as $row)
            {
                $content .= sprintf("\tconst\t%-32s = %-3s;\t//%s" . PHP_EOL
                    , strtoupper($row['Name']), $row['Id'], $row['Mark']);
            }
            $content .= '}' . PHP_EOL . PHP_EOL;

            //事件属性类型
            $this->load->model('bll/Attribute_bll', 'attribute_bll');
            /*$where = array('SystemId' => $system_id);*/
            $fields = 'Id, Name, Mark';
            $where = array('SystemId' => 0);
            $result = $this->attribute_bll->fetch($fields, $where);
            $content .= 'class DL_Attr_type ' . PHP_EOL;
            $content .= '{' . PHP_EOL;
            foreach($result as $row)
            {
                $content .= sprintf("\tconst\t%-32s = %-3s;\t//%s" . PHP_EOL
                    , strtoupper($row['Name']), $row['Id'], $row['Mark']);
            }
            $content .= '}' . PHP_EOL . PHP_EOL;

            //地点
            $this->load->model('bll/Address_bll', 'address_bll');
            $fields = 'Id, Name, Mark';
            $where = array('SystemId' => $system_id);
            $result = $this->address_bll->fetch($fields, $where);
            $content .= 'class DL_Addr_type ' . PHP_EOL;
            $content .= '{' . PHP_EOL;
            foreach($result as $row)
            {
                $content .= sprintf("\tconst\t%-32s = %-3s;\t//%s" . PHP_EOL
                    , strtoupper($row['Name']), $row['Id'], $row['Mark']);
            }
            $content .= '}' . PHP_EOL . PHP_EOL;

            $this->load->helper('download');
            force_download('Data_log_def.php', $content);
        }
    }

}