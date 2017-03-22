<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/11/26
 * Time: 17:38
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'My_Bll_Model.php';

class My_Model extends CI_Model
{
    protected $tbl_name = '';

    public function __construct($tbl_name = '')
    {
        parent::__construct();
        $this->load->database();
        $this->tbl_name = $tbl_name;
    }

    /**
     * @function 查找
     * @param string $fields
     * @param array $where
     * @return bool
     */
    public function fetch($fields = '*', $where = array())
    {
        $query = $this->db->select($fields)
            ->where($where)
            ->get($this->tbl_name);

        return $query ? $query->result_array() : FALSE;
    }

    /**
     * @function 查找单条记录
     * @param string $fields
     * @param array $where
     * @return bool
     */
    public function fetch_one($fields = '*', $where = array())
    {
        $query = $this->db->select($fields)
            ->where($where)
            ->get($this->tbl_name);

        return $query ? $query->row_array() : FALSE;
    }

    /**
     * @function 分页查找
     * @param string $fields
     * @param array $where
     * @param int $offset
     * @param int $length
     * @return mixed
     */
    public function page($fields = '*', $where = array(), $offset = 0, $length = 50)
    {
        $result = $this
            ->order_by('Id', 'DESC')
            ->limit($length, $offset)
            ->fetch($fields, $where);

        return $result;
    }

    /**
     * @function 根据IDs查找
     * @param string $reference
     * @param array $ids
     * @param string $fields
     * @param array $where
     * @return bool
     */
    public function fetch_by_ids($reference = 'Id', $ids = array(), $fields = '*', $where = array())
    {
        $query = $this->db->select($fields)
            ->where($where)
            ->where_in($reference, $ids)
            ->get($this->tbl_name);

        return $query ? $query->result_array() : FALSE;
    }

    /**
     * @function 插入单条记录
     * @param array $data
     * @return int ID of the row inserted or FALSE on failure
     */
    public function insert($data)
    {
        $query = $this->db->insert($this->tbl_name, $data);
        return $query ? $this->db->insert_id() : FALSE;
    }

    /**
     * @function 批量插入
     * @param array $data
     * @return int Number of rows inserted or FALSE on failure
     */
    public function insert_batch($data)
    {
        $query = $this->db->insert_batch($this->tbl_name, $data);
        return $query;
    }

    public function delete($where)
    {
        return $this->db->delete($this->tbl_name, $where);
    }

    public function update($data, $where)
    {
        $this->db->update($this->tbl_name, $data, $where);
        return $this->db->affected_rows();
    }

    public function select($fields = '*')
    {
        $this->db->select($fields);
        return $this;
    }

    public function count($where)
    {
        $this->where($where);
        $count = $this->db->count_all_results($this->tbl_name);
        return $count;
    }

    public function limit($length, $offset = 0)
    {
        $this->db->limit($length, $offset);
        return $this;
    }

    public function order_by($field, $order = 'ASC')
    {
        $this->db->order_by($field, $order);
        return $this;
    }

    public function where($key, $value = NULL, $escape = NULL)
    {
        $this->db->where($key, $value, $escape);
        return $this;
    }

    public function where_in($field, $range = array())
    {
        $this->db->where_in($field, $range);
        return $this;
    }

    public function like($field, $match = '', $side = 'both', $escape = NULL)
    {
        $this->db->like($field, $match, $side, $escape);
        return $this;
    }

}