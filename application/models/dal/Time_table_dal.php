<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/4/28
 * Time: 14:47
 */

class Time_table_dal extends My_Model
{
    public function __construct()
    {
        parent::__construct('RltTimeTable');
    }

    public function get_rlt_tables($begin, $end)
    {
        $sql = "SELECT TableName FROM RltTimeTable WHERE " .
            "(MinTime <= {$begin} AND MaxTime >= {$begin}) OR " .
            "(MinTime <= {$end} AND MaxTime >= {$end}) OR " .
            "(MinTime >= {$begin} AND MaxTime <= {$end}) OR " .
            "(MinTime <= {$begin} AND MaxTime >= {$end})";
        $query = $this->db->query($sql);
        $tables = $query->result_array();

        return array_column($tables, 'TableName');
    }
}