<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/12
 * Time: 10:54
 */
class System_model extends My_Model
{
    public function __construct()
    {
        parent::__construct('System');
    }

    /**
     * @param $name
     * @return int
     */
    public function add_system($name)
    {
        if(!$name || $name === '')
        {
            return -1;
        }
        if(strlen($name) > 32)
        {
            return -2;
        }

        $data = array('Name' => $name);

        return $this->insert($data);
    }

    /**
     * @param $id
     * @param $name
     * @return int
     */
    public function update_system($id, $name)
    {
        if(!$name || $name === '')
        {
            return -1;
        }
        if(strlen($name) > 32)
        {
            return -2;
        }

        $where = array('Id' => $id);
        $data = array('Name' => $name);

        return $this->update($data, $where);
    }

}