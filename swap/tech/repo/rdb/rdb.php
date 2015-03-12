<?php
/**
 * 关系数据库抽象
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
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
    
    static function rep($table_name, array $keyvalues);
    static function rep_many($table_name, array $keyvalues_list);
    static function change($table_name, $sql, array $args = []);
    static function replace($source_name, $sql, array $args = []);
    
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
    public static function get($table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get($table_name, $keyvalues, $order_limit);
    }
    public static function get_one($table_name, array $keyvalues, $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_one($table_name, $keyvalues);
    }
    public static function get_where($table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_where($table_name, $where, $args, $order_limit);
    }
    public static function get_by_id($table_name, $id, $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_by_id($table_name, $id);
    }
    public static function get_by_ids($table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_by_ids($table_name, $ids, $order_limit);
    }
    public static function get_in($table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_in($table_name, $field_name, $values, $order_limit);
    }
    public static function get_all($table_name, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_all($table_name, $order_limit);
    }
    public static function get_for_fields($field_names, $table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_for_fields($field_names, $table_name, $keyvalues, $order_limit);
    }
    public static function get_one_for_fields($field_names, $table_name, array $keyvalues, $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_one_for_fields($field_names, $table_name, $keyvalues);
    }
    public static function get_where_for_fields($field_names, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_where_for_fields($field_names, $table_name, $where, $args, $order_limit);
    }
    public static function get_by_id_for_fields($field_names, $table_name, $id, $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_by_id_for_fields($field_names, $table_name, $id);
    }
    public static function get_by_ids_for_fields($field_names, $table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_by_ids_for_fields($field_names, $table_name, $ids, $order_limit);
    }
    public static function get_in_for_fields($field_names, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_in_for_fields($field_names, $table_name, $field_name, $values, $order_limit);
    }
    public static function get_all_for_fields($field_names, $table_name, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->get_all_for_fields($field_names, $table_name, $order_limit);
    }
    public static function pager($table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager($table_name, $keyvalues, $order_limit);
    }
    public static function pager_where($table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_where($table_name, $where, $args, $order_limit);
    }
    public static function pager_by_ids($table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_by_ids($table_name, $ids, $order_limit);
    }
    public static function pager_in($table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_in($table_name, $field_name, $values, $order_limit);
    }
    public static function pager_all($table_name, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_all($table_name, $order_limit);
    }
    public static function pager_with_count($record_count, $table_name, array $keyvalues, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_with_count($record_count, $table_name, $keyvalues, $order_limit);
    }
    public static function pager_where_with_count($record_count, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_where_with_count($record_count, $table_name, $where, $args, $order_limit);
    }
    public static function pager_by_ids_with_count($record_count, $table_name, array $ids, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_by_ids_with_count($record_count, $table_name, $ids, $order_limit);
    }
    public static function pager_in_with_count($record_count, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_in_with_count($record_count, $table_name, $field_name, $values, $order_limit);
    }
    public static function pager_all_with_count($record_count, $table_name, array $order_limit = array([], 0, 0), $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->pager_all_with_count($record_count, $table_name, $order_limit);
    }
    public static function count($table_name, array $keyvalues, $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count($table_name, $keyvalues);
    }
    public static function count_where($table_name, $where, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count_where($table_name, $where, $args);
    }
    public static function count_by_ids($table_name, array $ids, $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count_by_ids($table_name, $ids);
    }
    public static function count_in($table_name, $field_name, array $values, $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count_in($table_name, $field_name, $values);
    }
    public static function count_all($table_name, $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->count_all($table_name);
    }
    public static function fetch($table_name, $sql, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->select($sql, $args);
    }
    public static function fetch_one($table_name, $sql, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_table_name($table_name) : self::get_slave_rdb_node_from_table_name($table_name);
        return $rdb_node->select_one($sql, $args);
    }
    public static function select($source_name, $sql, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_source_name($source_name) : self::get_slave_rdb_node_from_source_name($source_name);
        return $rdb_node->select($sql, $args);
    }
    public static function select_one($source_name, $sql, array $args = [], $use_master = false) {
        $rdb_node = $use_master ? self::get_master_rdb_node_from_source_name($source_name) : self::get_slave_rdb_node_from_source_name($source_name);
        return $rdb_node->select_one($sql, $args);
    }
    
    public static function set($table_name, array $keyvalues, array $conditions) {
        return self::get_master_rdb_node_from_table_name($table_name)->set($table_name, $keyvalues, $conditions);
    }
    public static function set_where($table_name, array $keyvalues, $where, array $args = []) {
        return self::get_master_rdb_node_from_table_name($table_name)->set_where($table_name, $keyvalues, $where, $args);
    }
    public static function set_by_id($table_name, array $keyvalues, $id) {
        return self::get_master_rdb_node_from_table_name($table_name)->set_by_id($table_name, $keyvalues, $id);
    }
    public static function set_by_ids($table_name, array $keyvalues, array $ids) {
        return self::get_master_rdb_node_from_table_name($table_name)->set_by_ids($table_name, $keyvalues, $ids);
    }
    public static function set_all($table_name, array $keyvalues) {
        return self::get_master_rdb_node_from_table_name($table_name)->set_all($table_name, $keyvalues);
    }
    public static function modify($table_name, $sql, array $args = []) {
        return self::get_master_rdb_node_from_table_name($table_name)->update($sql, $args);
    }
    public static function update($source_name, $sql, array $args = []) {
        return self::get_master_rdb_node_from_source_name($source_name)->update($sql, $args);
    }
    
    public static function add($table_name, array $keyvalues) {
        return self::get_master_rdb_node_from_table_name($table_name)->add($table_name, $keyvalues);
    }
    public static function add_many($table_name, array $keyvalues_list) {
        return self::get_master_rdb_node_from_table_name($table_name)->add_many($table_name, $keyvalues_list);
    }
    public static function create($table_name, $sql, array $args = []) {
        return self::get_master_rdb_node_from_table_name($table_name)->insert($sql, $args);
    }
    public static function insert($source_name, $sql, array $args = []) {
        return self::get_master_rdb_node_from_source_name($source_name)->insert($sql, $args);
    }
    
    public static function del($table_name, array $keyvalues) {
        return self::get_master_rdb_node_from_table_name($table_name)->del($table_name, $keyvalues);
    }
    public static function del_where($table_name, $where, array $args = []) {
        return self::get_master_rdb_node_from_table_name($table_name)->del_where($table_name, $where, $args);
    }
    public static function del_by_id($table_name, $id) {
        return self::get_master_rdb_node_from_table_name($table_name)->del_by_id($table_name, $id);
    }
    public static function del_by_ids($table_name, array $ids) {
        return self::get_master_rdb_node_from_table_name($table_name)->del_by_ids($table_name, $ids);
    }
    public static function remove($table_name, $sql, array $args = []) {
        return self::get_master_rdb_node_from_table_name($table_name)->delete($sql, $args);
    }
    public static function delete($source_name, $sql, array $args = []) {
        return self::get_master_rdb_node_from_source_name($source_name)->delete($sql, $args);
    }
    
    public static function rep($table_name, array $keyvalues) {
        return self::get_master_rdb_node_from_table_name($table_name)->rep($table_name, $keyvalues);
    }
    public static function rep_many($table_name, array $keyvalues_list) {
        return self::get_master_rdb_node_from_table_name($table_name)->rep_many($table_name, $keyvalues_list);
    }
    public static function change($table_name, $sql, array $args = []) {
        return self::get_master_rdb_node_from_table_name($table_name)->replace($sql, $args);
    }
    public static function replace($source_name, $sql, array $args = []) {
        return self::get_master_rdb_node_from_source_name($source_name)->replace($sql, $args);
    }
    
    public static function inc($table_name, array $keyvalues, array $conditions) {
        return self::get_master_rdb_node_from_table_name($table_name)->inc($table_name, $keyvalues, $conditions);
    }
    public static function inc_by_id($table_name, array $keyvalues, $id) {
        return self::get_master_rdb_node_from_table_name($table_name)->inc_by_id($table_name, $keyvalues, $id);
    }
    public static function inc_by_ids($table_name, array $keyvalues, array $ids) {
        return self::get_master_rdb_node_from_table_name($table_name)->inc_by_ids($table_name, $keyvalues, $ids);
    }
    public static function set_and_inc($table_name, array $sets, array $incs, array $conditions) {
        return self::get_master_rdb_node_from_table_name($table_name)->set_and_inc($table_name, $sets, $incs, $conditions);
    }
    public static function set_and_inc_by_id($table_name, array $sets, array $incs, $id) {
        return self::get_master_rdb_node_from_table_name($table_name)->set_and_inc_by_id($table_name, $sets, $incs, $id);
    }
    public static function set_and_inc_by_ids($table_name, array $sets, array $incs, array $ids) {
        return self::get_master_rdb_node_from_table_name($table_name)->set_and_inc_by_ids($table_name, $sets, $incs, $ids);
    }
    public static function dec($table_name, array $keyvalues, array $conditions) {
        return self::get_master_rdb_node_from_table_name($table_name)->dec($table_name, $keyvalues, $conditions);
    }
    public static function dec_by_id($table_name, array $keyvalues, $id) {
        return self::get_master_rdb_node_from_table_name($table_name)->dec_by_id($table_name, $keyvalues, $id);
    }
    public static function dec_by_ids($table_name, array $keyvalues, array $ids) {
        return self::get_master_rdb_node_from_table_name($table_name)->dec_by_ids($table_name, $keyvalues, $ids);
    }
    
    public static function begin($source_name) {
        return self::get_master_rdb_node_from_source_name($source_name)->begin();
    }
    public static function commit($source_name) {
        return self::get_master_rdb_node_from_source_name($source_name)->commit();
    }
    public static function rollback($source_name) {
        return self::get_master_rdb_node_from_source_name($source_name)->rollback();
    }
    
    protected static function get_master_rdb_node_from_table_name($table_name) {
        static $master_rdb_nodes_by_table_name = [];
        if (!array_key_exists($table_name, $master_rdb_nodes_by_table_name)) {
            $source_name = self::get_source_name_from_table_name($table_name);
            $master_rdb_nodes_by_table_name[$table_name] = self::get_master_rdb_node_from_source_name($source_name);
        }
        return $master_rdb_nodes_by_table_name[$table_name];
    }
    protected static function get_slave_rdb_node_from_table_name($table_name) {
        static $slave_rdb_nodes_by_table_name = [];
        if (!array_key_exists($table_name, $slave_rdb_nodes_by_table_name)) {
            $source_name = self::get_source_name_from_table_name($table_name);
            $slave_rdb_nodes_by_table_name[$table_name] = self::get_slave_rdb_node_from_source_name($source_name);
        }
        return $slave_rdb_nodes_by_table_name[$table_name];
    }
    protected static function get_master_rdb_node_from_source_name($source_name) {
        static $master_rdb_nodes_by_source_name = [];
        if (!array_key_exists($source_name, $master_rdb_nodes_by_source_name)) {
            $dsn = setting::get_logic('rdb.sources.' . $source_name . '.master');
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
            $dsns = setting::get_logic('rdb.sources.' . $source_name . '.slaves', []);
            if ($dsns === []) {
                $dsns = array(setting::get_logic('rdb.sources.' . $source_name . '.master'));
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
        $source_name = setting::get_logic('rdb.tables.' . $table_name, null);
        if ($source_name === null) {
            $source_name = setting::get_logic('rdb.tables.*');
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
    abstract public function get_for_fields($field_names, $table_name, array $keyvalues, array $order_limit = array([], 0, 0));
    public function get_one_for_fields($field_names, $table_name, array $keyvalues) {
        return $this->get_first_record($this->get_for_fields($field_names, $table_name, $keyvalues));
    }
    abstract public function get_where_for_fields($field_names, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0));
    abstract public function get_by_id_for_fields($field_names, $table_name, $id);
    public function get_by_ids_for_fields($field_names, $table_name, array $ids, array $order_limit = array([], 0, 0)) {
        return $this->get_in_for_fields($field_names, $table_name, 'id', $ids, $order_limit);
    }
    abstract public function get_in_for_fields($field_names, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0));
    abstract public function get_all_for_fields($field_names, $table_name, array $order_limit = array([], 0, 0));
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
    abstract public function count($table_name, array $keyvalues);
    abstract public function count_where($table_name, $where, array $args = []);
    abstract public function count_by_ids($table_name, array $ids);
    abstract public function count_in($table_name, $field_name, array $values);
    abstract public function count_all($table_name);
    abstract public function select($sql, array $args = []);
    public function select_one($sql, array $args = []) {
        return $this->get_first_record($this->select($sql, $args));
    }
    
    public function set($table_name, array $keyvalues, array $conditions) {
        throw new developer_error('slave is not allowed to update');
    }
    public function set_where($table_name, array $keyvalues, $where, array $args = []) {
        throw new developer_error('slave is not allowed to update');
    }
    public function set_by_id($table_name, array $keyvalues, $id) {
        throw new developer_error('slave is not allowed to update');
    }
    public function set_by_ids($table_name, array $keyvalues, array $ids) {
        throw new developer_error('slave is not allowed to update');
    }
    public function set_all($table_name, array $keyvalues) {
        throw new developer_error('slave is not allowed to update');
    }
    public function update($sql, array $args = []) {
        throw new developer_error('slave is not allowed to update');
    }
    public function add($table_name, array $keyvalues) {
        throw new developer_error('slave is not allowed to insert');
    }
    public function add_many($table_name, array $keyvalues_list) {
        throw new developer_error('slave is not allowed to insert');
    }
    public function insert($sql, array $args = []) {
        throw new developer_error('slave is not allowed to insert');
    }
    
    public function del($table_name, array $keyvalues) {
        throw new developer_error('slave is not allowed to delete');
    }
    public function del_where($table_name, $where, array $args = []) {
        throw new developer_error('slave is not allowed to delete');
    }
    public function del_by_id($table_name, $id) {
        throw new developer_error('slave is not allowed to delete');
    }
    public function del_by_ids($table_name, array $ids) {
        throw new developer_error('slave is not allowed to delete');
    }
    public function delete($sql, array $args = []) {
        throw new developer_error('slave is not allowed to delete');
    }
    
    public function rep($table_name, array $keyvalues) {
        throw new developer_error('slave is not allowed to replace');
    }
    public function rep_many($table_name, array $keyvalues_list) {
        throw new developer_error('slave is not allowed to replace');
    }
    public function replace($sql, array $args = []) {
        throw new developer_error('slave is not allowed to replace');
    }
    
    public function inc($table_name, array $keyvalues, array $conditions) {
        throw new developer_error('slave is not allowed to inc');
    }
    public function inc_by_id($table_name, array $keyvalues, $id) {
        throw new developer_error('slave is not allowed to inc');
    }
    public function inc_by_ids($table_name, array $keyvalues, array $ids) {
        throw new developer_error('slave is not allowed to inc');
    }
    public function set_and_inc($table_name, array $sets, array $incs, array $conditions) {
        throw new developer_error('slave is not allowed to set_and_inc');
    }
    public function set_and_inc_by_id($table_name, array $sets, array $incs, $id) {
        throw new developer_error('slave is not allowed to set_and_inc');
    }
    public function set_and_inc_by_ids($table_name, array $sets, array $incs, array $ids) {
        throw new developer_error('slave is not allowed to set_and_inc');
    }
    public function dec($table_name, array $keyvalues, array $conditions) {
        throw new developer_error('slave is not allowed to dec');
    }
    public function dec_by_id($table_name, array $keyvalues, $id) {
        throw new developer_error('slave is not allowed to dec');
    }
    public function dec_by_ids($table_name, array $keyvalues, array $ids) {
        throw new developer_error('slave is not allowed to dec');
    }
    
    public function begin() {
        throw new developer_error('slave is not allowed to perform transaction');
    }
    public function commit() {
        throw new developer_error('slave is not allowed to perform transaction');
    }
    public function rollback() {
        throw new developer_error('slave is not allowed to perform transaction');
    }
    
    public function __construct(rdb_conn $conn) {
        $this->conn = $conn;
    }
    protected function do_pager($record_count, $table_name, array $keyvalues, array $order_limit = array([], 0, 0)) {
        rdb_node_util::check_order_limit($order_limit, $record_count);
        return array(rdb_node_util::build_pager_data($record_count, $order_limit), $this->get($table_name, $keyvalues, $order_limit));
    }
    protected function do_pager_where($record_count, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        rdb_node_util::check_order_limit($order_limit, $record_count);
        return array(rdb_node_util::build_pager_data($record_count, $order_limit), $this->get_where($table_name, $where, $args, $order_limit));
    }
    protected function do_pager_by_ids($record_count, $table_name, array $ids, array $order_limit = array([], 0, 0)) {
        rdb_node_util::check_order_limit($order_limit, $record_count);
        return array(rdb_node_util::build_pager_data($record_count, $order_limit), $this->get_by_ids($table_name, $ids, $order_limit));
    }
    protected function do_pager_in($record_count, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        rdb_node_util::check_order_limit($order_limit, $record_count);
        return array(rdb_node_util::build_pager_data($record_count, $order_limit), $this->get_in($table_name, $field_name, $values, $order_limit));
    }
    protected function do_pager_all($record_count, $table_name, array $order_limit = array([], 0, 0)) {
        rdb_node_util::check_order_limit($order_limit, $record_count);
        return array(rdb_node_util::build_pager_data($record_count, $order_limit), $this->get_all($table_name, $order_limit));
    }
    protected function get_first_record($records) {
        if ($records === []) {
            return null;
        }
        return array_shift($records);
    }
    protected $conn = null;
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
            $rdb_node_class = 'swap\\' . $rdb_type . '_' . $node_mode . '_rdb_node';
            $rdb_node = new $rdb_node_class($rdb_conn);
            $rdb_nodes[$node_mode][$dsn] = $rdb_node;
        }
        return $rdb_nodes[$node_mode][$dsn];
    }
}
// [实体] 关系数据库节点工具
abstract class /* @swap */ rdb_node_util {
    public static function check_order_limit(array &$order_limit, $record_count) {
        if ($order_limit[1] < 1) {
            $order_limit[1] = 1;
        }
    }
    public static function build_pager_data($record_count, array $order_limit = array([], 0, 0)) {
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
    public static function localize_where($where) {
        return $where;
    }
}
// [类型] 关系数据库连接
abstract class rdb_conn {
    abstract public function __construct($dsn);
    abstract public function select($sql);
    abstract public function execute($sql);
    abstract public function insert_id();
    abstract public function affected_rows();
    abstract public function escape($value);
    abstract public function begin();
    abstract public function commit();
    abstract public function rollback();
    abstract public function last_error();
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
            $rdb_conn_class = 'swap\\' . $rdb_type . '_rdb_conn';
            $rdb_conn = new $rdb_conn_class($dsn);
            $rdb_conns[$dsn] = array($rdb_type, $rdb_conn);
        }
        return $rdb_conns[$dsn];
    }
}
