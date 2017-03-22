<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/7
 * Time: 11:35
 */

class User_bll extends My_Bll_Model
{
    public function __construct()
    {
        parent::__construct('dal/User_dal', 'user_dal');
    }

}