<?php
/**
 * PostgreSQL 关系数据库节点
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
// [类型] postgresql 关系数据库节点
abstract class pgsql_rdb_node extends rdb_node {
    public static function build_table_name($table_name) {
        return '"' . $table_name . '"';
    }
    public static function build_field_name($field_name) {
        return '"' . $field_name . '"';
    }
    public static function get_limit_sql($page_size, $begin_offset) {
        return "LIMIT {$page_size} OFFSET {$begin_offset}";
    }
}
// [类型] postgresql 关系数据库主节点
class pgsql_master_rdb_node extends pgsql_rdb_node {
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
    // @todo: postgresql 支持吗？
    protected function do_add_many_or_rep_many($method, $table_name, array $keyvalues_list) {
        // @todo: implementation
    }
}
// [类型] postgresql 关系数据库从节点
class pgsql_slave_rdb_node extends pgsql_rdb_node {}
