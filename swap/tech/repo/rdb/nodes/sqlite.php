<?php
/**
 * SQLite 关系数据库节点
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
use SQLite3;
// [类型] sqlite 关系数据库节点
abstract class sqlite_rdb_node extends rdb_node {
    public static function build_table_name($table_name) {
        return '`' . $table_name . '`';
    }
    public static function build_field_name($field_name) {
        return '`' . $field_name . '`';
    }
    public static function get_limit_sql($page_size, $begin_offset) {
        return "LIMIT {$page_size} OFFSET {$begin_offset}";
    }
}
// [类型] sqlite 关系数据库主节点
class sqlite_master_rdb_node extends sqlite_rdb_node {
    public function rep($table_name, array $keyvalues) {
        // @todo: implementation
        // return $this->do_add_or_rep('REPLACE', $table_name, $keyvalues);
    }
    public function rep_many($table_name, array $keyvalues_list) {
        // @todo: implementation
        // return $this->do_add_many_or_rep_many('REPLACE', $table_name, $keyvalues_list);
    }
    public function replace($sql, array $args = []) {
        // @todo: implementation
        // $this->execute($this->replace_sql_args($sql, $args));
        // return $this->insert_id();
    }
    protected function do_add_many_or_rep_many($method, $table_name, array $keyvalues_list) {
        // sqlite 不支持，只能模拟
        if ($keyvalues_list === [] || $keyvalues_list === array([])) {
            throw new developer_error("keyvalues_list is [] or array([])");
        }
        $insert_id = null;
        foreach ($keyvalues_list as $keyvalues) {
            $id = $this->do_add_or_rep($method, $table_name, $keyvalues);
            if ($insert_id === null) {
                $insert_id = $id;
            }
        }
        return $insert_id;
    }
}
// [类型] sqlite 关系数据库从节点
class sqlite_slave_rdb_node extends sqlite_rdb_node {}
