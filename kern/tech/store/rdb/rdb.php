<?php
/**
 * 关系数据库抽象
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
// [实体] 关系数据库 API
interface rdb_api {
    static function get($table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false);
    static function get_one($table_name, array $keyvalues, $use_master = false);
    static function get_where($table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false);
    static function get_by_id($table_name, $id, $use_master = false);
    static function get_by_ids($table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false);
    static function get_in($table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false);
    static function get_all($table_name, array $order_limit = array([], 0, 0), $use_master = false);
    static function get_for_fields($field_names, $table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false);
    static function get_one_for_fields($field_names, $table_name, array $keyvalues, $use_master = false);
    static function get_where_for_fields($field_names, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false);
    static function get_by_id_for_fields($field_names, $table_name, $id, $use_master = false);
    static function get_by_ids_for_fields($field_names, $table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false);
    static function get_in_for_fields($field_names, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false);
    static function get_all_for_fields($field_names, $table_name, array $order_limit = array([], 0, 0), $use_master = false);
    static function pager($table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_where($table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_by_ids($table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_in($table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_all($table_name, array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_with_count($record_count, $table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_where_with_count($record_count, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_by_ids_with_count($record_count, $table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_in_with_count($record_count, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false);
    static function pager_all_with_count($record_count, $table_name, array $order_limit = array([], 0, 0), $use_master = false);
    static function count($table_name, array $keyvalues, $use_master = false);
    static function count_where($table_name, $where, array $args = [], $use_master = false);
    static function count_by_ids($table_name, array $ids, $use_master = false);
    static function count_in($table_name, $field_name, array $values, $use_master = false);
    static function count_all($table_name, $use_master = false);
    static function fetch($table_name, $sql, array $args = [], $use_master = false);
    static function fetch_one($table_name, $sql, array $args = [], $use_master = false);
    static function select($source_name, $sql, array $args = [], $use_master = false);
    static function select_one($source_name, $sql, array $args = [], $use_master = false);
    
    static function set($table_name, array $keyvalues, array $conditions);
    static function set_where($table_name, array $keyvalues, $where, array $args = []);
    static function set_by_id($table_name, array $keyvalues, $id);
    static function set_by_ids($table_name, array $keyvalues, array $ids);
    static function set_all($table_name, array $keyvalues);
    static function modify($table_name, $sql, array $args = []);
    static function update($source_name, $sql, array $args = []);
    
    static function add($table_name, array $keyvalues);
    static function add_many($table_name, array $keyvalues_list);
    static function create($table_name, $sql, array $args = []);
    static function insert($source_name, $sql, array $args = []);
    
    static function del($table_name, array $keyvalues);
    static function del_where($table_name, $where, array $args = []);
    static function del_by_id($table_name, $id);
    static function del_by_ids($table_name, array $ids);
    static function remove($table_name, $sql, array $args = []);
    static function delete($source_name, $sql, array $args = []);
    
    static function inc($table_name, array $keyvalues, array $conditions);
    static function inc_by_id($table_name, array $keyvalues, $id);
    static function inc_by_ids($table_name, array $keyvalues, array $ids);
    static function set_and_inc($table_name, array $sets, array $incs, array $conditions);
    static function set_and_inc_by_id($table_name, array $sets, array $incs, $id);
    static function set_and_inc_by_ids($table_name, array $sets, array $incs, array $ids);
    static function dec($table_name, array $keyvalues, array $conditions);
    static function dec_by_id($table_name, array $keyvalues, $id);
    static function dec_by_ids($table_name, array $keyvalues, array $ids);
    
    static function begin($source_name);
    static function commit($source_name);
    static function rollback($source_name);
}
// [实体] 关系数据库
class rdb implements rdb_api {
    // 检索
    public static function get($table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get($table_name, $keyvalues, $order_limit);
    }
    public static function get_one($table_name, array $keyvalues, $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_one($table_name, $keyvalues);
    }
    public static function get_where($table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_where($table_name, $where, $args, $order_limit);
    }
    public static function get_by_id($table_name, $id, $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_by_id($table_name, $id);
    }
    public static function get_by_ids($table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_by_ids($table_name, $ids, $order_limit);
    }
    public static function get_in($table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_in($table_name, $field_name, $values, $order_limit);
    }
    public static function get_all($table_name, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_all($table_name, $order_limit);
    }
    public static function get_for_fields($field_names, $table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_for_fields($field_names, $table_name, $keyvalues, $order_limit);
    }
    public static function get_one_for_fields($field_names, $table_name, array $keyvalues, $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_one_for_fields($field_names, $table_name, $keyvalues);
    }
    public static function get_where_for_fields($field_names, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_where_for_fields($field_names, $table_name, $where, $args, $order_limit);
    }
    public static function get_by_id_for_fields($field_names, $table_name, $id, $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_by_id_for_fields($field_names, $table_name, $id);
    }
    public static function get_by_ids_for_fields($field_names, $table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_by_ids_for_fields($field_names, $table_name, $ids, $order_limit);
    }
    public static function get_in_for_fields($field_names, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_in_for_fields($field_names, $table_name, $field_name, $values, $order_limit);
    }
    public static function get_all_for_fields($field_names, $table_name, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_all_for_fields($field_names, $table_name, $order_limit);
    }
    public static function pager($table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager($table_name, $keyvalues, $order_limit);
    }
    public static function pager_where($table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_where($table_name, $where, $args, $order_limit);
    }
    public static function pager_by_ids($table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_by_ids($table_name, $ids, $order_limit);
    }
    public static function pager_in($table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_in($table_name, $field_name, $values, $order_limit);
    }
    public static function pager_all($table_name, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_all($table_name, $order_limit);
    }
    public static function pager_with_count($record_count, $table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_with_count($record_count, $table_name, $keyvalues, $order_limit);
    }
    public static function pager_where_with_count($record_count, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_where_with_count($record_count, $table_name, $where, $args, $order_limit);
    }
    public static function pager_by_ids_with_count($record_count, $table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_by_ids_with_count($record_count, $table_name, $ids, $order_limit);
    }
    public static function pager_in_with_count($record_count, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_in_with_count($record_count, $table_name, $field_name, $values, $order_limit);
    }
    public static function pager_all_with_count($record_count, $table_name, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_all_with_count($record_count, $table_name, $order_limit);
    }
    public static function count($table_name, array $keyvalues, $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count($table_name, $keyvalues);
    }
    public static function count_where($table_name, $where, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count_where($table_name, $where, $args);
    }
    public static function count_by_ids($table_name, array $ids, $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count_by_ids($table_name, $ids);
    }
    public static function count_in($table_name, $field_name, array $values, $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count_in($table_name, $field_name, $values);
    }
    public static function count_all($table_name, $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count_all($table_name);
    }
    public static function fetch($table_name, $sql, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->select($sql, $args);
    }
    public static function fetch_one($table_name, $sql, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_table_name($table_name) : static::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->select_one($sql, $args);
    }
    public static function select($source_name, $sql, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_source_name($source_name) : static::get_slave_rdb_node_from_source_name($source_name);
        return $rdb_node->select($sql, $args);
    }
    public static function select_one($source_name, $sql, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? static::get_master_rdb_node_from_source_name($source_name) : static::get_slave_rdb_node_from_source_name($source_name);
        return $rdb_node->select_one($sql, $args);
    }
    
    // 修改
    public static function set($table_name, array $keyvalues, array $conditions) {
        return static::get_master_rdb_node_from_table_name($table_name)->set($table_name, $keyvalues, $conditions);
    }
    public static function set_where($table_name, array $keyvalues, $where, array $args = []) {
        return static::get_master_rdb_node_from_table_name($table_name)->set_where($table_name, $keyvalues, $where, $args);
    }
    public static function set_by_id($table_name, array $keyvalues, $id) {
        return static::get_master_rdb_node_from_table_name($table_name)->set_by_id($table_name, $keyvalues, $id);
    }
    public static function set_by_ids($table_name, array $keyvalues, array $ids) {
        return static::get_master_rdb_node_from_table_name($table_name)->set_by_ids($table_name, $keyvalues, $ids);
    }
    public static function set_all($table_name, array $keyvalues) {
        return static::get_master_rdb_node_from_table_name($table_name)->set_all($table_name, $keyvalues);
    }
    public static function modify($table_name, $sql, array $args = []) {
        return static::get_master_rdb_node_from_table_name($table_name)->update($sql, $args);
    }
    public static function update($source_name, $sql, array $args = []) {
        return static::get_master_rdb_node_from_source_name($source_name)->update($sql, $args);
    }
    
    // 添加
    public static function add($table_name, array $keyvalues) {
        return static::get_master_rdb_node_from_table_name($table_name)->add($table_name, $keyvalues);
    }
    public static function add_many($table_name, array $keyvalues_list) {
        return static::get_master_rdb_node_from_table_name($table_name)->add_many($table_name, $keyvalues_list);
    }
    public static function create($table_name, $sql, array $args = []) {
        return static::get_master_rdb_node_from_table_name($table_name)->insert($sql, $args);
    }
    public static function insert($source_name, $sql, array $args = []) {
        return static::get_master_rdb_node_from_source_name($source_name)->insert($sql, $args);
    }
    
    // 删除
    public static function del($table_name, array $keyvalues) {
        return static::get_master_rdb_node_from_table_name($table_name)->del($table_name, $keyvalues);
    }
    public static function del_where($table_name, $where, array $args = []) {
        return static::get_master_rdb_node_from_table_name($table_name)->del_where($table_name, $where, $args);
    }
    public static function del_by_id($table_name, $id) {
        return static::get_master_rdb_node_from_table_name($table_name)->del_by_id($table_name, $id);
    }
    public static function del_by_ids($table_name, array $ids) {
        return static::get_master_rdb_node_from_table_name($table_name)->del_by_ids($table_name, $ids);
    }
    public static function remove($table_name, $sql, array $args = []) {
        return static::get_master_rdb_node_from_table_name($table_name)->delete($sql, $args);
    }
    public static function delete($source_name, $sql, array $args = []) {
        return static::get_master_rdb_node_from_source_name($source_name)->delete($sql, $args);
    }
    
    // 加减
    public static function inc($table_name, array $keyvalues, array $conditions) {
        return static::get_master_rdb_node_from_table_name($table_name)->inc($table_name, $keyvalues, $conditions);
    }
    public static function inc_by_id($table_name, array $keyvalues, $id) {
        return static::get_master_rdb_node_from_table_name($table_name)->inc_by_id($table_name, $keyvalues, $id);
    }
    public static function inc_by_ids($table_name, array $keyvalues, array $ids) {
        return static::get_master_rdb_node_from_table_name($table_name)->inc_by_ids($table_name, $keyvalues, $ids);
    }
    public static function set_and_inc($table_name, array $sets, array $incs, array $conditions) {
        return static::get_master_rdb_node_from_table_name($table_name)->set_and_inc($table_name, $sets, $incs, $conditions);
    }
    public static function set_and_inc_by_id($table_name, array $sets, array $incs, $id) {
        return static::get_master_rdb_node_from_table_name($table_name)->set_and_inc_by_id($table_name, $sets, $incs, $id);
    }
    public static function set_and_inc_by_ids($table_name, array $sets, array $incs, array $ids) {
        return static::get_master_rdb_node_from_table_name($table_name)->set_and_inc_by_ids($table_name, $sets, $incs, $ids);
    }
    public static function dec($table_name, array $keyvalues, array $conditions) {
        return static::get_master_rdb_node_from_table_name($table_name)->dec($table_name, $keyvalues, $conditions);
    }
    public static function dec_by_id($table_name, array $keyvalues, $id) {
        return static::get_master_rdb_node_from_table_name($table_name)->dec_by_id($table_name, $keyvalues, $id);
    }
    public static function dec_by_ids($table_name, array $keyvalues, array $ids) {
        return static::get_master_rdb_node_from_table_name($table_name)->dec_by_ids($table_name, $keyvalues, $ids);
    }
    
    // 事务
    public static function begin($source_name) {
        return static::get_master_rdb_node_from_source_name($source_name)->begin();
    }
    public static function commit($source_name) {
        return static::get_master_rdb_node_from_source_name($source_name)->commit();
    }
    public static function rollback($source_name) {
        return static::get_master_rdb_node_from_source_name($source_name)->rollback();
    }
    
    protected static function get_master_rdb_node_from_table_name($table_name) {
        static $master_rdb_nodes_by_table_name = [];
        if (!array_key_exists($table_name, $master_rdb_nodes_by_table_name)) {
            $source_name = static::get_source_name_from_table_name($table_name);
            $master_rdb_nodes_by_table_name[$table_name] = static::get_master_rdb_node_from_source_name($source_name);
        }
        return $master_rdb_nodes_by_table_name[$table_name];
    }
    protected static function get_slave_rdb_node_from_table_name($table_name) {
        static $slave_rdb_nodes_by_table_name = [];
        if (!array_key_exists($table_name, $slave_rdb_nodes_by_table_name)) {
            $source_name = static::get_source_name_from_table_name($table_name);
            $slave_rdb_nodes_by_table_name[$table_name] = static::get_slave_rdb_node_from_source_name($source_name);
        }
        return $slave_rdb_nodes_by_table_name[$table_name];
    }
    protected static function get_master_rdb_node_from_source_name($source_name) {
        static $master_rdb_nodes_by_source_name = [];
        if (!array_key_exists($source_name, $master_rdb_nodes_by_source_name)) {
            $dsn = config::get_logic('rdb.sources.' . $source_name . '.master');
            try {
                $master_rdb_nodes_by_source_name[$source_name] = rdb_node_pool::get_rdb_node('master', $dsn);
            } catch (server_except $except) {
                throw new server_except("cannot connect to dsn: '{$dsn}'");
            }
        }
        return $master_rdb_nodes_by_source_name[$source_name];
    }
    protected static function get_slave_rdb_node_from_source_name($source_name) {
        static $slave_rdb_nodes_by_source_name = [];
        if (!array_key_exists($source_name, $slave_rdb_nodes_by_source_name)) {
            $dsns = config::get_logic('rdb.sources.' . $source_name . '.slaves', []);
            if ($dsns === []) {
                $dsns = array(config::get_logic('rdb.sources.' . $source_name . '.master'));
            }
            shuffle($dsns);
            $all_attempts_failed = true;
            foreach ($dsns as $dsn) {
                try {
                    $slave_rdb_nodes_by_source_name[$source_name] = rdb_node_pool::get_rdb_node('slave', $dsn);
                    $all_attempts_failed = false;
                    break;
                } catch (server_except $except) {
                    logger::log_error("cannot connect to dsn: '{$dsn}', maybe failed?");
                }
            }
            if ($all_attempts_failed) {
                throw new server_except("cannot connect to all slave dsns of source '{$source_name}'");
            }
        }
        return $slave_rdb_nodes_by_source_name[$source_name];
    }
    protected static function get_source_name_from_table_name($table_name) {
        $source_name = config::get_logic('rdb.tables.' . $table_name, null);
        if ($source_name === null) {
            $source_name = config::get_logic('rdb.tables.*');
        }
        return $source_name;
    }
}
// [类型] 关系数据库节点
abstract class rdb_node {
    public function get($table_name, array $keyvalues, array $order_limit = array([], 0, 0)) {
        return $this->do_get('*', $table_name, $keyvalues, $order_limit);
    }
    public function get_one($table_name, array $keyvalues) {
        return $this->get_first_record($this->get($table_name, $keyvalues));
    }
    public function get_where($table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        return $this->do_get_where('*', $table_name, $where, $args, $order_limit);
    }
    public function get_by_id($table_name, $id) {
        return $this->do_get_by_id('*', $table_name, $id);
    }
    public function get_by_ids($table_name, array $ids, array $order_limit = array([], 0, 0)) {
        return $this->get_in($table_name, 'id', $ids, $order_limit);
    }
    public function get_in($table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        return $this->do_get_in('*', $table_name, $field_name, $values, $order_limit);
    }
    public function get_all($table_name, array $order_limit = array([], 0, 0)) {
        return $this->do_get_all('*', $table_name, $order_limit);
    }
    public function get_for_fields($field_names, $table_name, array $keyvalues, array $order_limit = array([], 0, 0)) {
        return $this->do_get($this->build_field_name_list($field_names), $table_name, $keyvalues, $order_limit);
    }
    public function get_one_for_fields($field_names, $table_name, array $keyvalues) {
        return $this->get_first_record($this->get_for_fields($field_names, $table_name, $keyvalues));
    }
    public function get_where_for_fields($field_names, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        return $this->do_get_where($this->build_field_name_list($field_names), $table_name, $where, $args, $order_limit);
    }
    public function get_by_id_for_fields($field_names, $table_name, $id) {
        return $this->do_get_by_id($this->build_field_name_list($field_names), $table_name, $id);
    }
    public function get_by_ids_for_fields($field_names, $table_name, array $ids, array $order_limit = array([], 0, 0)) {
        return $this->get_in_for_fields($field_names, $table_name, 'id', $ids, $order_limit);
    }
    public function get_in_for_fields($field_names, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        return $this->do_get_in($this->build_field_name_list($field_names), $table_name, $field_name, $values, $order_limit);
    }
    public function get_all_for_fields($field_names, $table_name, array $order_limit = array([], 0, 0)) {
        return $this->do_get_all($this->build_field_name_list($field_names), $table_name, $order_limit);
    }
    public function pager($table_name, array $keyvalues, array $order_limit = array([], 0, 0)) {
        return $this->do_pager($this->count($table_name, $keyvalues), $table_name, $keyvalues, $order_limit);
    }
    public function pager_where($table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        return $this->do_pager_where($this->count_where($table_name, $where, $args), $table_name, $where, $args, $order_limit);
    }
    public function pager_by_ids($table_name, array $ids, array $order_limit = array([], 0, 0)) {
        return $this->do_pager_by_ids($this->count_by_ids($table_name, $ids), $table_name, $ids, $order_limit);
    }
    public function pager_in($table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        return $this->do_pager_in($this->count_in($table_name, $field_name, $values), $table_name, $field_name, $values, $order_limit);
    }
    public function pager_all($table_name, array $order_limit = array([], 0, 0)) {
        return $this->do_pager_all($this->count_all($table_name), $table_name, $order_limit);
    }
    public function pager_with_count($record_count, $table_name, array $keyvalues, array $order_limit = array([], 0, 0)) {
        return $this->do_pager($record_count, $table_name, $keyvalues, $order_limit);
    }
    public function pager_where_with_count($record_count, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        return $this->do_pager_where($record_count, $table_name, $where, $args, $order_limit);
    }
    public function pager_by_ids_with_count($record_count, $table_name, array $ids, array $order_limit = array([], 0, 0)) {
        return $this->do_pager_by_ids($record_count, $table_name, $ids, $order_limit);
    }
    public function pager_in_with_count($record_count, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        return $this->do_pager_in($record_count, $table_name, $field_name, $values, $order_limit);
    }
    public function pager_all_with_count($record_count, $table_name, array $order_limit = array([], 0, 0)) {
        return $this->do_pager_all($record_count, $table_name, $order_limit);
    }
    public function count($table_name, array $keyvalues) {
        $count_name = $this->build_field_name('count');
        $table_name = $this->build_table_name($table_name);
        $conditions = $this->build_equal_list($keyvalues, ' AND ');
        // @todo: optimize using covering index
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name} WHERE {$conditions}");
    }
    public function count_where($table_name, $where, array $args = []) {
        $count_name = $this->build_field_name('count');
        $table_name = $this->build_table_name($table_name);
        $conditions = $this->replace_sql_args($where, $args);
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name} WHERE {$conditions}");
    }
    public function count_by_ids($table_name, array $ids) {
        $count_name = $this->build_field_name('count');
        $table_name = $this->build_table_name($table_name);
        $id_name = $this->build_field_name('id');
        $id_list = $this->build_value_list($ids);
        // @todo: optimize using covering index
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name} WHERE {$id_name} IN ({$id_list})");
    }
    public function count_in($table_name, $field_name, array $values) {
        $count_name = $this->build_field_name('count');
        $table_name = $this->build_table_name($table_name);
        $field_name = $this->build_field_name($field_name);
        $value_list = $this->build_value_list($values);
        // @todo: optimize using covering index
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name} WHERE {$field_name} IN ({$value_list})");
    }
    public function count_all($table_name) {
        $count_name = $this->build_field_name('count');
        $table_name = $this->build_table_name($table_name);
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name}");
    }
    public function select($sql, array $args = []) {
        return $this->query_and_fetch_records($this->replace_sql_args($sql, $args), false);
    }
    public function select_one($sql, array $args = []) {
        return $this->get_first_record($this->select($sql, $args));
    }
    
    public function set($table_name, array $keyvalues, array $conditions) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_equal_list($keyvalues, ', ');
            $conditions = $this->build_equal_list($conditions, ' AND ');
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to update');
        }
    }
    public function set_where($table_name, array $keyvalues, $where, array $args = []) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_equal_list($keyvalues, ', ');
            $conditions = $this->replace_sql_args($where, $args);
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to update');
        }
    }
    public function set_by_id($table_name, array $keyvalues, $id) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $id_name = $this->build_field_name('id');
            $modifies = $this->build_equal_list($keyvalues, ', ');
            $id = (int)$id;
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} = {$id}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to update');
        }
    }
    public function set_by_ids($table_name, array $keyvalues, array $ids) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_equal_list($keyvalues, ', ');
            $id_name = $this->build_field_name('id');
            $id_list = $this->build_value_list($ids);
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} IN ({$id_list})";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to update');
        }
    }
    public function set_all($table_name, array $keyvalues) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_equal_list($keyvalues, ', ');
            $sql = "UPDATE {$table_name} SET {$modifies}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to update');
        }
    }
    public function update($sql, array $args = []) {
        if ($this->is_master) {
            $this->execute($this->replace_sql_args($sql, $args));
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to update');
        }
    }
    public function add($table_name, array $keyvalues) {
        if ($this->is_master) {
            return $this->do_add($table_name, $keyvalues);
        } else {
            throw new developer_error('slave is not allowed to insert');
        }
    }
    public function add_many($table_name, array $keyvalues_list) {
        if ($this->is_master) {
            return $this->do_add_many($table_name, $keyvalues_list);
        } else {
            throw new developer_error('slave is not allowed to insert');
        }
    }
    public function insert($sql, array $args = []) {
        if ($this->is_master) {
            $this->execute($this->replace_sql_args($sql, $args));
            return $this->insert_id();
        } else {
            throw new developer_error('slave is not allowed to insert');
        }
    }
    
    public function del($table_name, array $keyvalues) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $conditions = $this->build_equal_list($keyvalues, ' AND ');
            $sql = "DELETE FROM {$table_name} WHERE {$conditions}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to delete');
        }
    }
    public function del_where($table_name, $where, array $args = []) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $conditions = $this->replace_sql_args($where, $args);
            $sql = "DELETE FROM {$table_name} WHERE {$conditions}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to delete');
        }
    }
    public function del_by_id($table_name, $id) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $id_name = $this->build_field_name('id');
            $id = (int)$id;
            $sql = "DELETE FROM {$table_name} WHERE {$id_name} = {$id}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to delete');
        }
    }
    public function del_by_ids($table_name, array $ids) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $id_name = $this->build_field_name('id');
            $id_list = $this->build_value_list($ids);
            $sql = "DELETE FROM {$table_name} WHERE {$id_name} IN ({$id_list})";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to delete');
        }
    }
    public function delete($sql, array $args = []) {
        if ($this->is_master) {
            $this->execute($this->replace_sql_args($sql, $args));
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to delete');
        }
    }
    
    public function inc($table_name, array $keyvalues, array $conditions) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_inc_dec_list($keyvalues, '+');
            $conditions = $this->build_equal_list($conditions, ' AND ');
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to inc');
        }
    }
    public function inc_by_id($table_name, array $keyvalues, $id) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_inc_dec_list($keyvalues, '+');
            $id_name = $this->build_field_name('id');
            $id = (int)$id;
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} = {$id}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to inc');
        }
    }
    public function inc_by_ids($table_name, array $keyvalues, array $ids) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_inc_dec_list($keyvalues, '+');
            $id_name = $this->build_field_name('id');
            $id_list = $this->build_value_list($ids);
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} IN ({$id_list})";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to inc');
        }
    }
    public function set_and_inc($table_name, array $sets, array $incs, array $conditions) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_equal_list($sets, ', ') . ', ' . $this->build_inc_dec_list($incs, '+');
            $conditions = $this->build_equal_list($conditions, ' AND ');
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to set_and_inc');
        }
    }
    public function set_and_inc_by_id($table_name, array $sets, array $incs, $id) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_equal_list($sets, ', ') . ', ' . $this->build_inc_dec_list($incs, '+');
            $id_name = $this->build_field_name('id');
            $id = (int)$id;
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} = {$id}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to set_and_inc');
        }
    }
    public function set_and_inc_by_ids($table_name, array $sets, array $incs, array $ids) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_equal_list($sets, ', ') . ', ' . $this->build_inc_dec_list($incs, '+');
            $id_name = $this->build_field_name('id');
            $id_list = $this->build_value_list($ids);
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} IN ({$id_list})";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to set_and_inc');
        }
    }
    public function dec($table_name, array $keyvalues, array $conditions) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_inc_dec_list($keyvalues, '-');
            $conditions = $this->build_equal_list($conditions, ' AND ');
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to dec');
        }
    }
    public function dec_by_id($table_name, array $keyvalues, $id) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_inc_dec_list($keyvalues, '-');
            $id_name = $this->build_field_name('id');
            $id = (int)$id;
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} = {$id}";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to dec');
        }
    }
    public function dec_by_ids($table_name, array $keyvalues, array $ids) {
        if ($this->is_master) {
            $table_name = $this->build_table_name($table_name);
            $modifies = $this->build_inc_dec_list($keyvalues, '-');
            $id_name = $this->build_field_name('id');
            $id_list = $this->build_value_list($ids);
            $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} IN ({$id_list})";
            $this->execute($sql);
            return $this->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to dec');
        }
    }
    
    public function begin() {
        if ($this->is_master) {
            return $this->conn->begin();
        } else {
            throw new developer_error('slave is not allowed to perform transaction');
        }
    }
    public function commit() {
        if ($this->is_master) {
            return $this->conn->commit();
        } else {
            throw new developer_error('slave is not allowed to perform transaction');
        }
    }
    public function rollback() {
        if ($this->is_master) {
            return $this->conn->rollback();
        } else {
            throw new developer_error('slave is not allowed to perform transaction');
        }
    }
    
    public function __construct(rdb_conn $conn, $is_master) {
        $this->conn = $conn;
        $this->is_master = $is_master;
    }
    
    protected function do_get($field_name_list, $table_name, array $keyvalues, array $order_limit = array([], 0, 0)) {
        $table_name = $this->build_table_name($table_name);
        $conditions = $this->build_equal_list($keyvalues, ' AND ');
        return $this->query_and_fetch_records("SELECT {$field_name_list} FROM {$table_name} WHERE {$conditions}" . $this->build_order_limit_sql($order_limit), true);
    }
    protected function do_get_where($field_name_list, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        $table_name = $this->build_table_name($table_name);
        $conditions = $this->replace_sql_args($where, $args);
        return $this->query_and_fetch_records("SELECT {$field_name_list} FROM {$table_name} WHERE {$conditions}" . $this->build_order_limit_sql($order_limit), true);
    }
    protected function do_get_by_id($field_name_list, $table_name, $id) {
        $table_name = $this->build_table_name($table_name);
        $id_name = $this->build_field_name('id');
        return $this->select_one("SELECT {$field_name_list} FROM {$table_name} WHERE {$id_name} = ?", array((int)$id));
    }
    protected function do_get_in($field_name_list, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        $table_name = $this->build_table_name($table_name);
        $field_name = $this->build_field_name($field_name);
        $value_list = $this->build_value_list($values);
        return $this->query_and_fetch_records("SELECT {$field_name_list} FROM {$table_name} WHERE {$field_name} IN ({$value_list})" . $this->build_order_limit_sql($order_limit), true);
    }
    protected function do_get_all($field_name_list, $table_name, array $order_limit = array([], 0, 0)) {
        $table_name = $this->build_table_name($table_name);
        return $this->query_and_fetch_records("SELECT {$field_name_list} FROM {$table_name}" . $this->build_order_limit_sql($order_limit), true);
    }
    protected function do_pager($record_count, $table_name, array $keyvalues, array $order_limit = array([], 0, 0)) {
        return array($this->build_pager_data($record_count, $order_limit), $this->get($table_name, $keyvalues, $order_limit));
    }
    protected function do_pager_where($record_count, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        return array($this->build_pager_data($record_count, $order_limit), $this->get_where($table_name, $where, $args, $order_limit));
    }
    protected function do_pager_by_ids($record_count, $table_name, array $ids, array $order_limit = array([], 0, 0)) {
        return array($this->build_pager_data($record_count, $order_limit), $this->get_by_ids($table_name, $ids, $order_limit));
    }
    protected function do_pager_in($record_count, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        return array($this->build_pager_data($record_count, $order_limit), $this->get_in($table_name, $field_name, $values, $order_limit));
    }
    protected function do_pager_all($record_count, $table_name, array $order_limit = array([], 0, 0)) {
        return array($this->build_pager_data($record_count, $order_limit), $this->get_all($table_name, $order_limit));
    }
    protected function do_count($sql) {
        $result = $this->conn->select($sql);
        if (kernel::is_debug()) {
            debug::save('rdb', $sql);
        }
        if ($result === false) {
            throw new server_except("select error: " . $this->conn->last_error() . " sql: {$sql}");
        }
        $record = $result->fetch_record();
        if ($record === null) {
            throw new environment_error("select count error");
        }
        $result->free();
        return (int)$record['count'];
    }
    protected function do_add($table_name, array $keyvalues) {
        if ($this->is_master) {
            if ($keyvalues === []) {
                throw new developer_error("keyvalues is []");
            }
            $columns = [];
            $values  = [];
            foreach ($keyvalues as $key => $value) {
                $columns[] = $this->build_field_name($key);
                $values[]  = is_int($value) || is_float($value) ? $value : ("'" . $this->escape((string)$value) . "'");
            }
            $columns = '(' . implode(', ', $columns) . ')';
            $values  = '(' . implode(', ', $values) . ')';
            $table_name = $this->build_table_name($table_name);
            $sql = "INSERT INTO {$table_name} {$columns} VALUES {$values}";
            $this->execute($sql);
            return $this->insert_id();
        } else {
            throw new developer_error('slave is not allowed to insert');
        }
    }
    protected function do_add_many($table_name, array $keyvalues_list) {
        if ($this->is_master) {
            if ($keyvalues_list === [] || $keyvalues_list === array([])) {
                throw new developer_error("keyvalues_list is [] or array([])");
            }
            $columns = [];
            $keyvalues = $keyvalues_list[0];
            foreach ($keyvalues as $key => $value) {
                $columns[] = $this->build_field_name($key);
            }
            $columns = '(' . implode(', ', $columns) . ')';
            $values_list = [];
            foreach ($keyvalues_list as $keyvalues) {
                $values = [];
                foreach ($keyvalues as $key => $value) {
                    $values[] = is_int($value) ? $value : ("'" . $this->escape((string)$value) . "'");
                }
                $values_list[] = '(' . implode(', ', $values) . ')';
            }
            $values_string = implode(', ', $values_list);
            $table_name = $this->build_table_name($table_name);
            $sql = "INSERT INTO {$table_name} {$columns} VALUES {$values_string}";
            $this->execute($sql);
            return $this->insert_id();
        } else {
            throw new developer_error('slave is not allowed to insert');
        }
    }
    protected function execute($sql) {
        if ($this->is_master) {
            $result = $this->conn->execute($sql);
            if (kernel::is_debug()) {
                debug::save('rdb', $sql);
            }
            if (!$result) {
                throw new server_except("execute error: " . $this->conn->last_error() . " sql: {$sql}");
            }
        } else {
            throw new developer_error('slave is not allowed to execute');
        }
    }
    protected function insert_id() {
        if ($this->is_master) {
            return $this->conn->insert_id();
        } else {
            throw new developer_error('slave is not allowed to insert');
        }
    }
    protected function affected_rows() {
        if ($this->is_master) {
            return $this->conn->affected_rows();
        } else {
            throw new developer_error('slave is not allowed to execute');
        }
    }
    protected function query_and_fetch_records($sql, $index_by_id) {
        $result = $this->conn->select($sql);
        if (kernel::is_debug()) {
            debug::save('rdb', $sql);
        }
        if ($result === false) {
            throw new server_except("select error: " . $this->conn->last_error() . " sql: {$sql}");
        }
        $records = [];
        if ($index_by_id) {
            while (($record = $result->fetch_record()) !== null) {
                $records[(int)$record['id']] = $record;
            }
        } else {
            while (($record = $result->fetch_record()) !== null) {
                $records[] = $record;
            }
        }
        $result->free();
        return $records;
    }
    protected function get_first_record($records) {
        if ($records === []) {
            return null;
        }
        return array_shift($records);
    }
    protected function escape($value) {
        return $this->conn->escape($value);
    }
    protected function get_limit_sql($page_size, $begin_offset) {
        return $this->conn->get_limit_sql($page_size, $begin_offset);
    }
    protected function build_table_name($table_name) {
        return $this->conn->build_table_name($table_name);
    }
    protected function build_field_name($field_name) {
        return $this->conn->build_field_name($field_name);
    }
    protected function build_value_list(array $values) {
        if ($values === []) {
            throw new developer_error("values is []");
        }
        foreach ($values as $key => $value) {
            if (!is_int($value) && !is_float($value)) {
                $values[$key] = "'" . $this->escape((string)$value) . "'";
            }
        }
        return implode(', ', $values);
    }
    protected function build_equal_list(array $keyvalues, $separator) {
        if ($keyvalues === []) {
            throw new developer_error("keyvalues is []");
        }
        $equal_list = [];
        foreach ($keyvalues as $key => $value) {
            $equal = $this->build_field_name($key) . ' = ';
            if (is_int($value) || is_float($value)) {
                $equal .= (string)$value;
            } else if ($value === null) {
                $equal .= 'NULL';
            } else {
                $equal .= "'" . $this->escape((string)$value) . "'";
            }
            $equal_list[] = $equal;
        }
        return implode($separator, $equal_list);
    }
    protected function build_field_name_list(array $field_names) {
        if ($field_names === []) {
            throw new developer_error("field_names is []");
        }
        $field_name_list = array($this->build_field_name('id'));
        foreach ($field_names as $field_name) {
            if (!is_string($field_name)) {
                throw new developer_error('field names should be array of string');
            } else {
                $field_name = strtolower($field_name);
                if ($field_name === 'id') {
                    continue;
                }
            }
            $field_name_list[] = $this->build_field_name($field_name);
        }
        return implode(', ', $field_name_list);
    }
    protected function build_inc_dec_list(array $keyvalues, $operator) {
        if ($keyvalues === []) {
            throw new developer_error("keyvalues is []");
        }
        $list = [];
        foreach ($keyvalues as $key => $value) {
            $value = (int)$value;
            $field_name = $this->build_field_name($key);
            $list[] = $field_name . ' = ' . $field_name . " {$operator} {$value}";
        }
        return implode(', ', $list);
    }
    protected function build_order_limit_sql(array $order_limit) {
        if (count($order_limit) !== 3) {
            throw new developer_error('$order_limit should have three values');
        }
        list($order_by, $page, $page_size) = $order_limit;
        $order_by_sql = '';
        if ($order_by !== []) {
            $orders = [];
            foreach ($order_by as $field_name => $order) {
                $field_name = $this->build_field_name($field_name);
                $orders[] = "{$field_name} " . strtoupper($order);
            }
            $order_by_sql .= ' ORDER BY ' . implode(', ', $orders);
        }
        $limit_sql = '';
        if ($page_size !== 0) {
            if ($page < 1) {
                $page = 1;
            }
            $begin_offset = ($page - 1) * $page_size;
            $limit_sql .= ' ' . $this->get_limit_sql($page_size, $begin_offset);
        }
        return $order_by_sql . $limit_sql;
    }
    protected function build_pager_data($record_count, array $order_limit = array([], 0, 0)) {
        $page_size = $order_limit[2];
        if ($page_size === 0) {
            throw new developer_error('page_size cannot be zero');
        }
        if ($record_count <= 0) {
            $page_count = 1;
            $current_page = 1;
        } else {
            $page_count = ceil($record_count / $page_size);
            $current_page = $order_limit[1];
        }
        return array('record_count' => $record_count, 'page_count' => $page_count, 'current_page' => $current_page, 'page_size' => $page_size);
    }
    protected function replace_sql_args($sql, array $args) {
        $begin_pos = 0;
        foreach ($args as $arg) {
            if (is_null($arg)) {
                $replace_str = 'NULL';
            } else if (is_string($arg)) {
                $replace_str = "'" . $this->escape($arg) . "'";
            } else {
                $replace_str = (string)$arg;
            }
            $pos_step = strlen($replace_str);
            $replace_pos = strpos($sql, '?', $begin_pos);
            if ($replace_pos === false) {
                throw new developer_error("the number of args is not equal to the number of '?' in sql: {$sql}");
            }
            $sql = substr_replace($sql, $replace_str, $replace_pos, 1);
            $begin_pos = $replace_pos + $pos_step;
        }
        return $sql;
    }
    
    protected $conn = null;
    protected $is_master = true;
}
// [实体] 关系数据库节点池
class rdb_node_pool {
    public static function get_rdb_node($node_mode, $dsn) {
        static $rdb_nodes = array(
            'master' => [],
            'slave' => [],
        );
        if (!isset($rdb_nodes[$node_mode][$dsn])) {
            list($rdb_type, $rdb_conn) = rdb_conn_pool::get_rdb_type_and_conn_from_dsn($dsn);
            $rdb_node_class = 'kern\\' . $rdb_type . '_' . $node_mode . '_rdb_node';
            $rdb_node = new $rdb_node_class($rdb_conn, $node_mode === 'master');
            $rdb_nodes[$node_mode][$dsn] = $rdb_node;
        }
        return $rdb_nodes[$node_mode][$dsn];
    }
}
// [类型] 关系数据库连接
abstract class rdb_conn {
    abstract public function __construct($dsn);
    abstract public function select($sql);
    abstract public function execute($sql);
    abstract public function insert_id();
    abstract public function affected_rows();
    abstract public function begin();
    abstract public function commit();
    abstract public function rollback();
    abstract public function last_error();
    abstract public function escape($value);
    abstract public function get_limit_sql($page_size, $begin_offset);
    abstract public function build_table_name($table_name);
    abstract public function build_field_name($field_name);
}
// [类型] 关系数据库结果集
abstract class rdb_result {
    abstract public function fetch_record();
    abstract public function num_rows();
    abstract public function free();
}
// [实体] 关系数据库连接池
class rdb_conn_pool {
    public static function get_rdb_type_and_conn_from_dsn($dsn) {
        static $rdb_conns = [];
        if (!isset($rdb_conns[$dsn])) {
            list($rdb_type, ) = explode('://', $dsn, 2);
            $rdb_conn_class = 'kern\\' . $rdb_type . '_rdb_conn';
            $rdb_conn = new $rdb_conn_class($dsn);
            $rdb_conns[$dsn] = array($rdb_type, $rdb_conn);
        }
        return $rdb_conns[$dsn];
    }
}
