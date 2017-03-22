<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/5/23
 * Time: 18:25
 */

class Statistic_bll extends My_Bll_Model
{
    public function __construct()
    {
        parent::__construct('dal/Statistic_dal', 'statistic_dal');
    }

}