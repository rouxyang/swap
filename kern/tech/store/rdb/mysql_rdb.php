<?php
/**
 * MySQL 关系数据库连接和结果集抽象
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
if (!extension_loaded('mysqli')) throw new environment_error('cannot use mysql_rdb_conn: mysqli extension does not exist');
// [类型] mysql 关系数据库节点
abstract class mysql_rdb_node extends rdb_node {}
// [类型] mysql 关系数据库主节点
class mysql_master_rdb_node extends mysql_rdb_node {}
// [类型] mysql 关系数据库从节点
class mysql_slave_rdb_node extends mysql_rdb_node {}
// [类型] mysql 关系数据库连接
class /* @kern */ mysql_rdb_conn extends rdb_conn {
    public function __construct($dsn) {
        $url_parts = parse_url($dsn);
        extract($url_parts, EXTR_SKIP);
        if (!isset($pass)) {
            $pass = '';
        }
        $name = substr($path, 1);
        // @todo: On failure, this call will hang.
        $conn = mysqli_connect($host, $user, $pass, $name, $port);
        if ($conn === false) {
            throw new server_except("cannot connect to database: {$dsn}");
        }
        if (!mysqli_set_charset($conn, 'utf8')) {
            throw new server_except('cannot set charset to utf8');
        }
        $this->conn = $conn;
    }
    public function select($sql) {
        $result = mysqli_query($this->conn, $sql);
        if ($result === false) {
            return false;
        }
        return new mysql_rdb_result($result);
    }
    public function execute($sql) {
        return mysqli_query($this->conn, $sql);
    }
    public function insert_id() {
        return (int)mysqli_insert_id($this->conn);
    }
    public function affected_rows() {
        $affected_rows = mysqli_affected_rows($this->conn);
        if ($affected_rows === -1) {
            throw new server_except('last execute statement error, cannot get affected rows');
        }
        return $affected_rows;
    }
    public function begin() {
        if (!mysqli_autocommit($this->conn, false)) {
            throw new server_except('cannot begin transaction');
        }
    }
    public function commit() {
        if (mysqli_commit($this->conn)) {
            mysqli_autocommit($this->conn, true);
        } else {
            throw new server_except('cannot commit transaction');
        }
    }
    public function rollback() {
        if (!mysqli_rollback($this->conn)) {
            throw new server_except('cannot rollback transaction');
        }
    }
    public function last_error() {
        return mysqli_error($this->conn);
    }
    public function escape($value) {
        return mysqli_real_escape_string($this->conn, $value);
    }
    public function get_limit_sql($page_size, $begin_offset) {
        return "LIMIT {$begin_offset}, {$page_size}";
    }
    public function build_table_name($table_name) {
        return '`' . $table_name . '`';
    }
    public function build_field_name($field_name) {
        return '`' . $field_name . '`';
    }
    protected $conn = null;
}
// [类型] mysql 关系数据库结果集
class /* @kern */ mysql_rdb_result extends rdb_result {
    public function __construct($result) {
        $this->result = $result;
    }
    public function fetch_record() {
        $record = mysqli_fetch_assoc($this->result);
        if ($record !== null) {
            $i = 0;
            foreach ($record as $field_name => $value) {
                $field_info = mysqli_fetch_field_direct($this->result, $i);
                switch ($field_info->type) {
                case 1: # tinyint
                case 2: # smallint
                case 9: # mediumint
                case 3: # int
                case 8: # bigint
                    $record[$field_name] = intval($value);
                    break;
                case 4: # float
                case 5: # double
                    $record[$field_name] = floatval($value);
                    break;
                default:
                    break;
                }
                $i++;
            }
        }
        return $record;
    }
    public function num_rows() {
        return mysqli_num_rows($this->result);
    }
    public function free() {
        mysqli_free_result($this->result);
    }
    protected $result = null;
}
