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
        return $this->do_get(mysql_rdb_node_util::build_field_name_list($field_names), $table_name, $keyvalues, $order_limit);
    }
    public function get_one_for_fields($field_names, $table_name, array $keyvalues) {
        return $this->get_first_record($this->get_for_fields($field_names, $table_name, $keyvalues));
    }
    public function get_where_for_fields($field_names, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        return $this->do_get_where(mysql_rdb_node_util::build_field_name_list($field_names), $table_name, $where, $args, $order_limit);
    }
    public function get_by_id_for_fields($field_names, $table_name, $id) {
        return $this->do_get_by_id(mysql_rdb_node_util::build_field_name_list($field_names), $table_name, $id);
    }
    public function get_by_ids_for_fields($field_names, $table_name, array $ids, array $order_limit = array([], 0, 0)) {
        return $this->get_in_for_fields($field_names, $table_name, 'id', $ids, $order_limit);
    }
    public function get_in_for_fields($field_names, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        return $this->do_get_in(mysql_rdb_node_util::build_field_name_list($field_names), $table_name, $field_name, $values, $order_limit);
    }
    public function get_all_for_fields($field_names, $table_name, array $order_limit = array([], 0, 0)) {
        return $this->do_get_all(mysql_rdb_node_util::build_field_name_list($field_names), $table_name, $order_limit);
    }
    public function count($table_name, array $keyvalues) {
        $count_name = mysql_rdb_node_util::build_field_name('count');
        $table_name = $this->get_full_table_name($table_name);
        $conditions = mysql_rdb_node_util::build_equal_list($keyvalues, ' AND ');
        // @todo: optimize using covering index
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name} WHERE {$conditions}");
    }
    public function count_where($table_name, $where, array $args = []) {
        $count_name = mysql_rdb_node_util::build_field_name('count');
        $table_name = $this->get_full_table_name($table_name);
        $conditions = mysql_rdb_node_util::replace_sql_args(mysql_rdb_node_util::localize_where($where), $args);
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name} WHERE {$conditions}");
    }
    public function count_by_ids($table_name, array $ids) {
        $count_name = mysql_rdb_node_util::build_field_name('count');
        $table_name = $this->get_full_table_name($table_name);
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id_list = mysql_rdb_node_util::build_value_list($ids);
        // @todo: optimize using covering index
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name} WHERE {$id_name} IN ({$id_list})");
    }
    public function count_in($table_name, $field_name, array $values) {
        $count_name = mysql_rdb_node_util::build_field_name('count');
        $table_name = $this->get_full_table_name($table_name);
        $field_name = mysql_rdb_node_util::build_field_name($field_name);
        $value_list = mysql_rdb_node_util::build_value_list($values);
        // @todo: optimize using covering index
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name} WHERE {$field_name} IN ({$value_list})");
    }
    public function count_all($table_name) {
        $count_name = mysql_rdb_node_util::build_field_name('count');
        $table_name = $this->get_full_table_name($table_name);
        return $this->do_count("SELECT COUNT(*) AS {$count_name} FROM {$table_name}");
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
    public function select($sql, array $args = []) {
        return $this->query_and_fetch_records(mysql_rdb_node_util::replace_sql_args($sql, $args), false);
    }
    public function select_one($sql, array $args = []) {
        return $this->get_first_record($this->select($sql, $args));
    }
    protected function do_get($field_name_list, $table_name, array $keyvalues, array $order_limit = array([], 0, 0)) {
        $table_name = $this->get_full_table_name($table_name);
        $conditions = mysql_rdb_node_util::build_equal_list($keyvalues, ' AND ');
        return $this->query_and_fetch_records("SELECT {$field_name_list} FROM {$table_name} WHERE {$conditions}" . mysql_rdb_node_util::build_order_limit_sql($order_limit), true);
    }
    protected function do_get_where($field_name_list, $table_name, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        $table_name = $this->get_full_table_name($table_name);
        $conditions = mysql_rdb_node_util::replace_sql_args(mysql_rdb_node_util::localize_where($where), $args);
        return $this->query_and_fetch_records("SELECT {$field_name_list} FROM {$table_name} WHERE {$conditions}" . mysql_rdb_node_util::build_order_limit_sql($order_limit), true);
    }
    protected function do_get_by_id($field_name_list, $table_name, $id) {
        $table_name = $this->get_full_table_name($table_name);
        $id_name = mysql_rdb_node_util::build_field_name('id');
        return $this->select_one("SELECT {$field_name_list} FROM {$table_name} WHERE {$id_name} = ?", array((int)$id));
    }
    protected function do_get_in($field_name_list, $table_name, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        $table_name = $this->get_full_table_name($table_name);
        $field_name = mysql_rdb_node_util::build_field_name($field_name);
        $value_list = mysql_rdb_node_util::build_value_list($values);
        return $this->query_and_fetch_records("SELECT {$field_name_list} FROM {$table_name} WHERE {$field_name} IN ({$value_list})" . mysql_rdb_node_util::build_order_limit_sql($order_limit), true);
    }
    protected function do_get_all($field_name_list, $table_name, array $order_limit = array([], 0, 0)) {
        $table_name = $this->get_full_table_name($table_name);
        return $this->query_and_fetch_records("SELECT {$field_name_list} FROM {$table_name}" . mysql_rdb_node_util::build_order_limit_sql($order_limit), true);
    }
    protected function do_count($sql) {
        $result = $this->conn->select($sql);
        if (framework::is_debug()) {
            debug::save('rdb', $sql);
        }
        if ($result === false) {
            throw new remote_except("select error: " . $this->conn->last_error() . " sql: {$sql}");
        }
        $record = $result->fetch_record();
        if ($record === null) {
            throw new environment_error("select count error");
        }
        $result->free();
        return (int)$record['count'];
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
    protected function query_and_fetch_records($sql, $index_by_id) {
        $result = $this->conn->select($sql);
        if (framework::is_debug()) {
            debug::save('rdb', $sql);
        }
        if ($result === false) {
            throw new remote_except("select error: " . $this->conn->last_error() . " sql: {$sql}");
        }
        $records = [];
        if ($index_by_id) {
            while (($record = $result->fetch_record()) !== null) {
                $records[$record['id']] = $record;
            }
        } else {
            while (($record = $result->fetch_record()) !== null) {
                $records[] = $record;
            }
        }
        $result->free();
        return $records;
    }
    protected function get_full_table_name($table_name) {
        return mysql_rdb_node_util::build_table_name($table_name);
    }
}
// [类型] mysql 关系数据库主节点
class mysql_master_rdb_node extends mysql_rdb_node {
    public function set($table_name, array $keyvalues, array $conditions) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_equal_list($keyvalues, ', ');
        $conditions = mysql_rdb_node_util::build_equal_list($conditions, ' AND ');
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function set_where($table_name, array $keyvalues, $where, array $args = []) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_equal_list($keyvalues, ', ');
        $conditions = mysql_rdb_node_util::replace_sql_args(mysql_rdb_node_util::localize_where($where), $args);
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function set_by_id($table_name, array $keyvalues, $id) {
        $table_name = $this->get_full_table_name($table_name);
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $modifies = mysql_rdb_node_util::build_equal_list($keyvalues, ', ');
        $id = (int)$id;
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} = {$id}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function set_by_ids($table_name, array $keyvalues, array $ids) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_equal_list($keyvalues, ', ');
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id_list = mysql_rdb_node_util::build_value_list($ids);
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} IN ({$id_list})";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function set_all($table_name, array $keyvalues) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_equal_list($keyvalues, ', ');
        $sql = "UPDATE {$table_name} SET {$modifies}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function update($sql, array $args = []) {
        $this->execute(mysql_rdb_node_util::replace_sql_args($sql, $args));
        return $this->affected_rows();
    }
    public function add($table_name, array $keyvalues) {
        return $this->do_add_or_rep('INSERT', $table_name, $keyvalues);
    }
    public function add_many($table_name, array $keyvalues_list) {
        return $this->do_add_many_or_rep_many('INSERT', $table_name, $keyvalues_list);
    }
    public function insert($sql, array $args = []) {
        $this->execute(mysql_rdb_node_util::replace_sql_args($sql, $args));
        return $this->insert_id();
    }
    public function del($table_name, array $keyvalues) {
        $table_name = $this->get_full_table_name($table_name);
        $conditions = mysql_rdb_node_util::build_equal_list($keyvalues, ' AND ');
        $sql = "DELETE FROM {$table_name} WHERE {$conditions}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function del_where($table_name, $where, array $args = []) {
        $table_name = $this->get_full_table_name($table_name);
        $conditions = mysql_rdb_node_util::replace_sql_args(mysql_rdb_node_util::localize_where($where), $args);
        $sql = "DELETE FROM {$table_name} WHERE {$conditions}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function del_by_id($table_name, $id) {
        $table_name = $this->get_full_table_name($table_name);
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id = (int)$id;
        $sql = "DELETE FROM {$table_name} WHERE {$id_name} = {$id}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function del_by_ids($table_name, array $ids) {
        $table_name = $this->get_full_table_name($table_name);
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id_list = mysql_rdb_node_util::build_value_list($ids);
        $sql = "DELETE FROM {$table_name} WHERE {$id_name} IN ({$id_list})";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function delete($sql, array $args = []) {
        $this->execute(mysql_rdb_node_util::replace_sql_args($sql, $args));
        return $this->affected_rows();
    }
    public function rep($table_name, array $keyvalues) {
        return $this->do_add_or_rep('REPLACE', $table_name, $keyvalues);
    }
    public function rep_many($table_name, array $keyvalues_list) {
        return $this->do_add_many_or_rep_many('REPLACE', $table_name, $keyvalues_list);
    }
    public function replace($sql, array $args = []) {
        $this->execute(mysql_rdb_node_util::replace_sql_args($sql, $args));
        return $this->insert_id();
    }
    public function inc($table_name, array $keyvalues, array $conditions) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_inc_dec_list($keyvalues, '+');
        $conditions = mysql_rdb_node_util::build_equal_list($conditions, ' AND ');
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function inc_by_id($table_name, array $keyvalues, $id) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_inc_dec_list($keyvalues, '+');
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id = (int)$id;
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} = {$id}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function inc_by_ids($table_name, array $keyvalues, array $ids) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_inc_dec_list($keyvalues, '+');
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id_list = mysql_rdb_node_util::build_value_list($ids);
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} IN ({$id_list})";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function set_and_inc($table_name, array $sets, array $incs, array $conditions) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_equal_list($sets, ', ') . ', ' . mysql_rdb_node_util::build_inc_dec_list($incs, '+');
        $conditions = mysql_rdb_node_util::build_equal_list($conditions, ' AND ');
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function set_and_inc_by_id($table_name, array $sets, array $incs, $id) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_equal_list($sets, ', ') . ', ' . mysql_rdb_node_util::build_inc_dec_list($incs, '+');
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id = (int)$id;
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} = {$id}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function set_and_inc_by_ids($table_name, array $sets, array $incs, array $ids) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_equal_list($sets, ', ') . ', ' . mysql_rdb_node_util::build_inc_dec_list($incs, '+');
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id_list = mysql_rdb_node_util::build_value_list($ids);
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} IN ({$id_list})";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function dec($table_name, array $keyvalues, array $conditions) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_inc_dec_list($keyvalues, '-');
        $conditions = mysql_rdb_node_util::build_equal_list($conditions, ' AND ');
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$conditions}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function dec_by_id($table_name, array $keyvalues, $id) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_inc_dec_list($keyvalues, '-');
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id = (int)$id;
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} = {$id}";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function dec_by_ids($table_name, array $keyvalues, array $ids) {
        $table_name = $this->get_full_table_name($table_name);
        $modifies = mysql_rdb_node_util::build_inc_dec_list($keyvalues, '-');
        $id_name = mysql_rdb_node_util::build_field_name('id');
        $id_list = mysql_rdb_node_util::build_value_list($ids);
        $sql = "UPDATE {$table_name} SET {$modifies} WHERE {$id_name} IN ({$id_list})";
        $this->execute($sql);
        return $this->affected_rows();
    }
    public function begin() {
        return $this->conn->begin();
    }
    public function commit() {
        return $this->conn->commit();
    }
    public function rollback() {
        return $this->conn->rollback();
    }
    protected function do_add_or_rep($method, $table_name, array $keyvalues) {
        if ($keyvalues === []) {
            throw new developer_error("keyvalues is []");
        }
        $columns = [];
        $values  = [];
        foreach ($keyvalues as $key => $value) {
            $columns[] = mysql_rdb_node_util::build_field_name($key);
            $values[]  = is_int($value) ? $value : ("'" . addslashes((string)$value) . "'");
        }
        $columns = '(' . implode(', ', $columns) . ')';
        $values  = '(' . implode(', ', $values) . ')';
        $table_name = $this->get_full_table_name($table_name);
        $sql = $method . " INTO {$table_name} {$columns} VALUES {$values}";
        $this->execute($sql);
        return $this->insert_id();
    }
    protected function do_add_many_or_rep_many($method, $table_name, array $keyvalues_list) {
        if ($keyvalues_list === [] || $keyvalues_list === array([])) {
            throw new developer_error("keyvalues_list is [] or array([])");
        }
        $columns = [];
        $keyvalues = $keyvalues_list[0];
        foreach ($keyvalues as $key => $value) {
            $columns[] = mysql_rdb_node_util::build_field_name($key);
        }
        $columns = '(' . implode(', ', $columns) . ')';
        $values_list = [];
        foreach ($keyvalues_list as $keyvalues) {
            $values = [];
            foreach ($keyvalues as $key => $value) {
                $values[] = is_int($value) ? $value : ("'" . addslashes((string)$value) . "'");
            }
            $values_list[] = '(' . implode(', ', $values) . ')';
        }
        $values_string = implode(', ', $values_list);
        $table_name = $this->get_full_table_name($table_name);
        $sql = $method . " INTO {$table_name} {$columns} VALUES {$values_string}";
        $this->execute($sql);
        return $this->insert_id();
    }
    protected function execute($sql) {
        $result = $this->conn->execute($sql);
        if (framework::is_debug()) {
            debug::save('rdb', $sql);
        }
        if (!$result) {
            throw new remote_except("execute error: " . $this->conn->last_error() . " sql: {$sql}");
        }
    }
    protected function insert_id() {
        return $this->conn->insert_id();
    }
    protected function affected_rows() {
        return $this->conn->affected_rows();
    }
}
// [类型] mysql 关系数据库从节点
class mysql_slave_rdb_node extends mysql_rdb_node {
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
}
// [实体] mysql 关系数据库节点工具
class mysql_rdb_node_util extends rdb_node_util {
    public static function build_order_limit_sql(array $order_limit) {
        if (count($order_limit) !== 3) {
            throw new developer_error('$order_limit should have three values');
        }
        list($order_by, $page, $page_size) = $order_limit;
        $order_by_sql = '';
        if ($order_by !== []) {
            $orders = [];
            foreach ($order_by as $field_name => $order) {
                $field_name = self::build_field_name($field_name);
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
            $limit_sql .= " LIMIT {$begin_offset}, {$page_size}";
        }
        return $order_by_sql . $limit_sql;
    }
    public static function build_field_name_list(array $field_names) {
        if ($field_names === []) {
            throw new developer_error("field_names is []");
        }
        $field_name_list = array(self::build_field_name('id'));
        foreach ($field_names as $field_name) {
            if (!is_string($field_name)) {
                throw new developer_error('field names should be array of string');
            } else {
                $field_name = strtolower($field_name);
                if ($field_name === 'id') {
                    continue;
                }
            }
            $field_name_list[] = self::build_field_name($field_name);
        }
        return implode(', ', $field_name_list);
    }
    public static function build_value_list(array $values) {
        if ($values === []) {
            throw new developer_error("values is []");
        }
        foreach ($values as $key => $value) {
            if (!is_int($value)) {
                $values[$key] = "'" . addslashes((string)$value) . "'";
            }
        }
        return implode(', ', $values);
    }
    public static function build_equal_list(array $keyvalues, $separator) {
        if ($keyvalues === []) {
            throw new developer_error("keyvalues is []");
        }
        $equal_list = [];
        foreach ($keyvalues as $key => $value) {
            $equal = self::build_field_name($key) . ' = ';
            if (is_int($value)) {
                $equal .= $value;
            } else {
                $equal .= "'" . addslashes((string)$value) . "'";
            }
            $equal_list[] = $equal;
        }
        return implode($separator, $equal_list);
    }
    public static function build_inc_dec_list(array $keyvalues, $operator) {
        if ($keyvalues === []) {
            throw new developer_error("keyvalues is []");
        }
        $list = [];
        foreach ($keyvalues as $key => $value) {
            $value = (int)$value;
            $field_name = self::build_field_name($key);
            $list[] = $field_name . ' = ' . $field_name . " {$operator} {$value}";
        }
        return implode(', ', $list);
    }
    public static function replace_sql_args($sql, array $args) {
        $begin_pos = 0;
        foreach ($args as $arg) {
            if (is_null($arg)) {
                $replace_str = 'NULL';
            } else if (is_string($arg)) {
                $replace_str = '\'' . addslashes($arg) . '\'';
            } else {
                $replace_str = $arg;
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
    public static function build_table_name($table_name) {
        return '`' . $table_name . '`';
    }
    public static function build_field_name($field_name) {
        return '`' . $field_name . '`';
    }
    protected static function join_token_values(array $values) {
        return implode(' ', $values);
    }
}
