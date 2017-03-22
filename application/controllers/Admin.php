<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/12/31
 * Time: 9:21
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function logout()
    {
        $this->sso->log_out();
    }

}