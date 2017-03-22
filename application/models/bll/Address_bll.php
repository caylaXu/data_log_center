<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/7
 * Time: 11:35
 */

class Address_bll extends My_Bll_Model
{

    public function __construct()
    {
        parent::__construct('dal/Address_dal', 'address_dal');
    }

}