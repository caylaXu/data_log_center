<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/1/4
 * Time: 16:37
 */

/**
 * Class Event_log_model
 * @property Event_define_model event_define
 */
class Event_log_model extends My_Model
{

    private $fields = '';

    public function __construct()
    {
        $this->fields = 'SystemId, UserId, UserType, EventId, EventAttr, EventDesc, EventTime, EventAddr, '.
            'BindUserId1, BindUserType1, BindUserId2, BindUserType2, BindUserId3, BindUserType3';

        parent::__construct('EventLog');
        $this->load->model('Rlt_time_table_model', 'rlt_time_table');
    }

    /**
     * 获取日志记录
     * @param array $input
     * @return array
     */
    public function get_logs($input = array())
    {
        $rlt_tables = $this->rlt_time_table->get_rlt_tables($input['date1'], $input['date2']);
        $condition = $this->_build_condition($input);
        $start = isset($input['start']) && $input['start'] > 0 ? $input['start'] : 0;
        $length = isset($input['length']) && $input['length'] > 0 ? $input['length'] : 50;

        if (!$rlt_tables)
        {
            return array();
        }

        //查出符合条件的GUIDs
        $union = $this->_build_union($rlt_tables, $condition, $start, $length);
        $sql = "SELECT {$this->fields} " .
            "FROM {$union} " .
            "WHERE {$condition['where']} " .
            "ORDER BY EventTime DESC LIMIT {$start}, {$length}";
        $query = $this->db->query($sql, $condition['bind']);
        $result = $query->result_array();

        return $result;
    }

    /**
     * 获取符合条件的总记录数
     * @param array $input
     * @return mixed
     */
    public function get_cnt($input = array())
    {
        $rlt_tables = $this->rlt_time_table->get_rlt_tables($input['date1'], $input['date2']);
        $condition = $this->_build_condition($input);

        if (!$rlt_tables)
        {
            return array();
        }
        $count = 0;
        foreach ($rlt_tables as $table)
        {
            $sql = "SELECT COUNT(*) as count " .
                "FROM {$table} " .
                "WHERE {$condition['where']} " .
                "ORDER BY EventTime DESC";
            $query = $this->db->query($sql, $condition['bind']);
            $count += intval($query->row_array()['count']);
        }

        return $count;
    }

    /**
     * @function 获取事件属性列表
     * @author Peter
     * @param array $input
     * @return array
     */
    public function get_attrs($input = array())
    {
        $start = $input['date1'];
        $end = $input['date2'];
        $system_id = $input['system_id'];
        $event_id = $input['event_id'];
        $event_addr = $input['addr_id'];

        /*if ($input['event_id'] === 0 && $input['addr_id'] === 0)
        {//显示默认事件类型
            $this->load->model('Event_define_model', 'event_define');

            $fields = 'Id';
            $where = array(
                'SystemId' => $input['system_id'],
                'Show' => 1,
            );
            $event_types = $this->event_define->fetch($where, $fields);
            $event_ids = array();
            foreach($event_types as $event_type)
            {
                $event_ids[] = $event_type['Id'];
            }

            if($event_ids)
            {
                $condition['where'] .= " AND EventId IN ? ";
                $condition['bind'][] = $event_ids;
            }
			else
			{
				return array();
			}
        }*/
        $sql = "SELECT EventTime, AttrId, Value, Count FROM Statistic " .
            "WHERE SystemId=? AND EventId=? AND EventTime>=? AND EventTime<=? AND AddrId=?";
        $query = $this->db->query($sql, array($system_id, $event_id, $start, $end, $event_addr));
        $rows = $query->result_array();
        if (!$rows)
        {
            return array();
        }
        $attr_ids = array_unique(array_column($rows, 'AttrId'));
        $attr_values = array();
        foreach ($rows as $row)
        {
            if (!isset($attr_values[$row['AttrId']]))
            {
                $attr_values[$row['AttrId']] = array();
            }
            if (in_array($row['Value'], $attr_values[$row['AttrId']]))
            {
                continue;
            }
            $attr_values[$row['AttrId']][] = $row['Value'];
        }
        foreach ($attr_ids as $attr_id)
        {
            foreach ($attr_values[$attr_id] as $value)
            {
                for ($event_time = $start; $event_time < $end; $event_time += 86400)
                {
                    $flag = 0;
                    foreach ($rows as $row)
                    {
                        if ($row['EventTime'] == $event_time && $row['AttrId'] == $attr_id && $row['Value'] == $value)
                        {
                            $result[$attr_id][$value][] = intval($row['Count']);
                            $flag = 1;
                        }
                    }
                    if (!$flag)
                    {
                        $result[$attr_id][$value][] = 0;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @function 获取事件属性列表
     * @param $input
     * @return array
     */
    public function get_attrs_today($input)
    {
        $start = $input['date1'];
        $end = $input['date2'];
        $system_id = $input['system_id'];
        $event_id = $input['event_id'];
        $addr_id = $input['addr_id'];

        $this->config->load('redis', TRUE);
        $redis_config = $this->config->item('redis');
        $redis = new Redis();
        $redis->pconnect($redis_config['host'], $redis_config['port']);
        $redis->auth($redis_config['passwd']);

        $sql = "SELECT EventTime, AttrId, Value, Count FROM Statistic " .
            "WHERE SystemId=? AND EventId=? AND EventTime>=? AND EventTime<=? AND AddrId=?";
        $query = $this->db->query($sql, array($system_id, $event_id, $start, $end, $addr_id));
        $rows = $query->result_array();
        $attr_ids = $attr_ids = array_unique(array_column($rows, 'AttrId'));
        $attr_values = array();
        foreach ($rows as $row)
        {
            if (!isset($attr_values[$row['AttrId']]))
            {
                $attr_values[$row['AttrId']] = array();
            }
            if (in_array($row['Value'], $attr_values[$row['AttrId']]))
            {
                continue;
            }
            $attr_values[$row['AttrId']][] = $row['Value'];
        }

        $result = array();
        foreach ($attr_ids as $attr_id)
        {
            foreach ($attr_values[$attr_id] as $value)
            {
                $hash_fields = array();
                for ($event_time = $start; $event_time < $end; $event_time += 3600)
                {
                    $hash_fields[] = $system_id . '_' . $event_id . '_' . $event_time . '_' . $addr_id . '_' . $attr_id . '_' . $value;
                }
                $counts = $redis->hMGet($redis_config['hash_key'], $hash_fields);
                foreach ($counts as $count)
                {
                    $result[$attr_id][$value][] = intval($count);
                }
            }
        }

        return $result;
    }

    /**
     * @function 生成横坐标
     * @param $start
     * @param $end
     * @param $step
     * @return array
     */
    public function get_categories($start, $end, $step)
    {
        $categories = array();
        for ($event_time = $start; $event_time < $end; $event_time += $step)
        {
            if ($step == 3600)
            {
                $categories[] = date('H', $event_time);
            }
            else if ($step == 86400)
            {
                $categories[] = date('m-d', $event_time);
            }
        }

        return $categories;
    }

    /**
     * 构建union子句
     * @param $tables
     * @param $condition
     * @param $start
     * @param $length
     * @return string
     */
    private function _build_union($tables, &$condition, $start = 0, $length = 0)
    {
        if (!is_array($tables) || !$tables)
        {
            return FALSE;
        }

        $union = "( ";
        $bind = $condition['bind'];
        foreach ($tables as $table)
        {
            $union .= "( SELECT {$this->fields} FROM {$table} WHERE {$condition['where']} " .
                "ORDER BY EventTime DESC LIMIT " . ($start+$length) . ") UNION ALL ";
            $condition['bind'] = array_merge($condition['bind'], $bind);
        }
        $union = substr($union, 0, -10);
        $union .= ") t";

        return $union;
    }

    /**
     * 构建查询条件
     * @param array $input
     * @return array
     */
    private function _build_condition($input = array())
    {
        static $condition = array();

        if ($condition)
        {
            return $condition;
        }

        $condition['where'] = ' ';
        $condition['bind'] = array();

        $date1 = $input['date1'];
        $date2 = $input['date2'];
        $system_id = isset($input['system_id']) ? $input['system_id'] : null;
        $user_id = isset($input['user_id']) ? $input['user_id'] : null;
        $user_type = isset($input['user_type']) ? $input['user_type'] : null;
        $related = isset($input['related']) ? $input['related'] : null;
        $event_id = isset($input['event_id']) ? $input['event_id'] : null;
        $addr_id = isset($input['addr_id']) ? $input['addr_id'] : null;

        if ($date1)
        {
            $condition['where'] .= 'EventTime >= ? AND ';
            $condition['bind'][] = $date1;
        }
        if ($date2)
        {
            $condition['where'] .= 'EventTime <= ? AND ';
            $condition['bind'][] = $date2;
        }
        if ($system_id)
        {
            $condition['where'] .= 'SystemId = ? AND ';
            $condition['bind'][] = $system_id;
        }
        if ($related === '1')
        {
            if ($user_id && $user_type)
            {
                $condition['where'] .= "((UserId = ? AND UserType = ?) " .
                    "OR (BindUserId1 = ? AND BindUserType1 = ?) " .
                    "OR (BindUserId2 = ? AND BindUserType2 = ?) " .
                    "OR (BindUserId3 = ? AND BindUserType3 = ?)) AND ";
                for ($i = 0; $i < 4; ++$i)
                {
                    $condition['bind'][] = $user_id;
                    $condition['bind'][] = $user_type;
                }
            }
        }
        else
        {
            if ($user_id)
            {
                $condition['where'] .= "UserId = ? AND ";
                $condition['bind'][] = $user_id;
            }
            if ($user_type)
            {
                $condition['where'] .= "UserType = ? AND ";
                $condition['bind'][] = $user_type;
            }
        }
        if ($event_id)
        {
            $condition['where'] .= 'EventId = ? AND ';
            $condition['bind'][] = $event_id;
        }
        if ($addr_id)
        {
            $condition['where'] .= 'EventAddr = ? AND ';
            $condition['bind'][] = $addr_id;
        }

        $condition['where'] .= '1=1';

        return $condition;
    }
}