<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/2/24
 * Time: 16:35
 */

class Event_attribute_bll extends My_Bll_Model
{
    public function __construct()
    {
        parent::__construct('dal/Event_attribute_dal', 'event_attribute_dal');
    }

}