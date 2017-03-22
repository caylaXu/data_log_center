<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/7
 * Time: 11:35
 */
class User_dal extends My_Model
{
    public function __construct()
    {
        parent::__construct('UserDef');
    }

    public function add_user_type($system_id, $name, $mark)
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
        );
        $this->db->insert($this->tbl_name, $data);

        return $this->db->affected_rows();
    }

    public function update_user_type($id, $name, $mark)
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
        );

        return $this->update($data, $where);
    }

}