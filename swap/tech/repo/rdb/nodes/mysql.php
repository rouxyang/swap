<?php
/**
 * MySQL 关系数据库节点
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
// [类型] mysql 关系数据库节点
abstract class mysql_rdb_node extends rdb_node {
    public static function build_table_name($table_name) {
        return '`' . $table_name . '`';
    }
    public static function build_field_name($field_name) {
        return '`' . $field_name . '`';
    }
    public static function get_limit_sql($page_size, $begin_offset) {
        return "LIMIT {$begin_offset}, {$page_size}";
    }
}
// [类型] mysql 关系数据库主节点
class mysql_master_rdb_node extends mysql_rdb_node {
    public function rep($table_name, array $keyvalues) {
        return $this->do_add_or_rep('REPLACE', $table_name, $keyvalues);
    }
    public function rep_many($table_name, array $keyvalues_list) {
        return $this->do_add_many_or_rep_many('REPLACE', $table_name, $keyvalues_list);
    }
    public function replace($sql, array $args = []) {
        $this->execute($this->replace_sql_args($sql, $args));
        return $this->insert_id();
    }
}
// [类型] mysql 关系数据库从节点
class mysql_slave_rdb_node extends mysql_rdb_node {}
