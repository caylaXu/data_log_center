<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/7
 * Time: 11:35
 */
class Event_define_model extends My_Model
{
    public function __construct()
    {
        parent::__construct('EventDef');
    }

    public function add_event_type($system_id, $name, $mark, $show)
    {
        if(!$name || !$mark || $name === '' || $mark === '')
        {
            return -1;
        }
        if(strlen($name) > 32 || strlen($mark) > 32)
        {
            return -2;
        }

        $data = array(
            'SystemId' => $system_id,
            'Name' => $name,
            'Mark' => $mark,
            'Show' => $show,
        );
        $this->db->insert($this->tbl_name, $data);

        return $this->db->affected_rows();
    }

    public function update_event_type($id, $name, $mark, $show)
    {
        if(!$name || !$mark || $name === '' || $mark === '')
        {
            return -1;
        }
        if(strlen($name) > 32 || strlen($mark) > 32)
        {
            return -2;
        }

        $where = array('Id' => $id);
        $data = array(
            'Name' => $name,
            'Mark' => $mark,
            'Show' => $show,
        );

        return $this->update($data, $where);
    }

}