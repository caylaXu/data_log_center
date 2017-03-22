<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/2/24
 * Time: 16:35
 */

class Address_event_bll extends My_Bll_Model
{
    public function __construct()
    {
        parent::__construct('dal/Address_event_dal', 'address_event_dal');
    }
}