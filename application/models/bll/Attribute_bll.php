<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/7
 * Time: 11:35
 */

class Attribute_bll extends My_Bll_Model
{

    public function __construct()
    {
        parent::__construct('dal/Attribute_dal', 'attribute_dal');
    }

    public function fetch($fields = '*', $where = array())
    {
        return $this->attribute_dal->order_by('Id', 'DESC')->fetch($fields, $where);
    }

    public function fetch_one($fields = '*', $where = array())
    {
        return $this->attribute_dal->fetch_one($fields, $where);
    }

    public function exist($where)
    {
        $fields = 'Id';
        $result = $this->attribute_dal->fetch_one($fields, $where);

        return $result ? TRUE : FALSE;
    }

    public function page($fields = '*', $where = array(), $offset = 0, $length = 50)
    {
        $result['data'] = $this->attribute_dal->page($fields, $where, $offset, $length);
        $result['recordsTotal'] = $result['recordsFiltered'] = $this->attribute_dal->count($where);

        return $result;
    }

    public function count($where = array())
    {
        return $this->attribute_dal->count($where);
    }

    public function insert($data)
    {
        return $this->attribute_dal->insert($data);;
    }

    public function insert_batch($data = array())
    {
        return $this->attribute_dal->insert_batch($data);
    }

    public function update($data, $where)
    {
        return $this->attribute_dal->update($data, $where);
    }

}