<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/12
 * Time: 10:54
 */

class System_bll extends My_Bll_Model
{
    public function __construct()
    {
        parent::__construct('dal/System_dal', 'system_dal');
    }

}