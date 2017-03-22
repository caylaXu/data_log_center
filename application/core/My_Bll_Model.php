<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/6/1
 * Time: 10:12
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class My_Bll_Model extends CI_Model
{
    protected $dal = '';

    public function __construct($dal_model, $alias)
    {
        $this->load->model($dal_model, $alias);
        $this->dal = $alias;
    }

    public function fetch($fields = '*', $where = array())
    {
        return $this->{$this->dal}->order_by('Id', 'DESC')->fetch($fields, $where);
    }

    public function fetch_one($fields = '*', $where = array())
    {
        return $this->{$this->dal}->fetch_one($fields, $where);
    }

    public function fetch_by_ids($reference = 'Id', $ids = array(), $fields = '*', $where = array())
    {
        if (!is_array($ids) || !$ids)
        {
            return array();
        }

        return $this->{$this->dal}->fetch_by_ids($reference, $ids, $fields, $where);
    }

    public function exist($where)
    {
        $fields = 'Id';
        $result = $this->{$this->dal}->fetch_one($fields, $where);

        return $result ? TRUE : FALSE;
    }

    public function page($fields = '*', $where = array(), $offset = 0, $length = 50)
    {
        $result['data'] = $this->{$this->dal}->page($fields, $where, $offset, $length);
        $result['recordsTotal'] = $result['recordsFiltered'] = $this->{$this->dal}->count($where);

        return $result;
    }

    public function count($where = array())
    {
        return $this->{$this->dal}->count($where);
    }

    public function insert($data)
    {
        return $this->{$this->dal}->insert($data);;
    }

    public function insert_batch($data = array())
    {
        return $this->{$this->dal}->insert_batch($data);
    }

    public function update($data, $where)
    {
        return $this->{$this->dal}->update($data, $where);
    }
}