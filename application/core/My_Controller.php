<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/11/26
 * Time: 17:38
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class My_Controller
 * @property CI_Input input
 * @property CI_Router router
 * @property Sso sso
 * @property Power_manager power_manager
 */
class My_Controller extends CI_Controller
{
    protected $layout = 'layout/main';

    protected $param = array();
    protected $data = array();

    public function __construct()
    {
        parent::__construct();

        //过滤输入
        array_walk_recursive($_POST, function (&$value)
        {
            $value = trim($value);
        });
        array_walk_recursive($_GET, function (&$value)
        {
            $value = trim($value);
        });

        //设置cookie(system_id)
        if (intval($this->input->get('system_id')) > 0)
        {
            $cookie_system_id = intval($this->input->get('system_id'));
            $this->data['system_id'] = $cookie_system_id;
            $this->input->set_cookie('system_id', $cookie_system_id, 3600);
        }

        //获取cookie(system_id)
        if (intval($this->input->cookie('system_id')) > 0)
        {
            $this->data['system_id'] = intval($this->input->cookie('system_id'));
        }

        //检查登录态
        $this->load->library("sso", array('system_id' => $this->get_system_id()));
        if (!$this->sso->check_login())
        {
            if (!$this->input->is_ajax_request())
            {
                $this->sso->request_login();
            }
            else
            {
                if ($this->input->get('draw'))
                {
                    $this->dt_error_return('请先登录');
                }
                else
                {
                    $this->return_msg(-100, '请先登录');
                }
            }
        }
        $this->data['account'] = $this->sso->account();

        //检查权限
        $controller = $this->router->class;
        $action = $this->router->method;
        $power = $controller . '_' . $action;
        $power_list = array(
            'event_log_index',
            'log_analysis_index',
            'system_index',
            'user_type_index',
            'event_type_index',
            'attr_type_index',
            'addr_type_index',
        );
        if (in_array($power, $power_list))
        {
            $params = array(
                'id' => 1,
                'account' => $this->sso->account(),
                'system_id' => $this->get_system_id(),
            );
            $this->load->library('power_manager', $params);
            $this->power_manager->query_by_scene('datalog_admin');
            $ret = $this->power_manager->check_rights($power);
            if (!$ret)
            {
                if (!$this->input->is_ajax_request())
                {
                    $this->show_msg('你没有该权限');
                }
                else
                {
                    $draw = intval($this->input->get('draw'));
                    if ($draw)
                    {
                        $this->return_error($draw, '你没有该权限');
                    }
                    else
                    {
                        $this->return_msg(-101, '你没有该权限');
                    }

                }
            }
        }

    }

    private function get_system_id()
    {
        $system_id = 0;

        switch (ENVIRONMENT)
        {
            case 'development':
                $system_id = 7;
                break;
            case 'testing':
                $system_id = 7;
                break;
            case 'tproduction':
                $system_id = 8;
                break;
            case 'production':
                $system_id = 7;
                break;
            default:
                break;
        }

        return $system_id;
    }

    /**
     * @function 返回DataTables的信息
     * @param array $data
     */
    protected function dt_return($data = array())
    {
        $result = $data;
        $result['draw'] = intval($this->input->get('draw'));

        $this->ajax_return($result);
    }

    /**
     * @function 返回DataTables的错误信息
     * @param string $error
     */
    protected function dt_error_return($error = '')
    {
        $result['draw'] = intval($this->input->get('draw'));
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $result['data'] = array();
        $result['error'] = $error;

        $this->ajax_return($result);
    }

    /**
     * @function 返回AJAX响应
     * @param $data
     */
    protected function ajax_return($data = array())
    {
        header("Content-Type: application/json");

        die(json_encode($data));
    }

    /**
     * 渲染视图
     * @param string $file
     * @param array $viewData
     * @param array $layoutData
     */
    protected function render($file = NULL, $viewData = array(), $layoutData = array())
    {
        if ($file)
        {
            $data['content'] = $this->load->view($file, $viewData, TRUE);
            $data['layout'] = $layoutData;
            die($this->load->view($this->layout, $data, TRUE));
        }
        else
        {
            die($this->load->view($this->layout, $viewData, TRUE));
        }
    }

}