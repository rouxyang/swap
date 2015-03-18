<?php
/**
 * PostgreSQL 关系数据库连接和结果集抽象
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
if (!extension_loaded('pgsql')) throw new developer_error('cannot use pgsql_rdb_conn: pgsql extension does not exist');
// [类型] postgresql 关系数据库节点
abstract class pgsql_rdb_node extends rdb_node {}
// [类型] postgresql 关系数据库主节点
class pgsql_master_rdb_node extends pgsql_rdb_node {}
// [类型] postgresql 关系数据库从节点
class pgsql_slave_rdb_node extends pgsql_rdb_node {}
// [类型] postgresql 关系数据库连接
class /* @kern */ pgsql_rdb_conn extends rdb_conn {
    public function __construct($dsn) {
        $url_parts = parse_url($dsn);
        extract($url_parts, EXTR_SKIP);
        $name = substr($path, 1);
        $conn_str = "host={$host} port={$port} dbname={$name} user={$user} connect_timeout=8";
        if (isset($pass)) {
            $conn_str .= " password={$pass}";
        }
        $conn = pg_connect($conn_str);
        if ($conn === false) {
            throw new server_except("cannot connect to database: {$dsn}");
        }
        if (pg_set_client_encoding($conn, 'UTF8') !== 0) {
            throw new server_except('cannot set charset to utf8');
        }
        $this->conn = $conn;
    }
    public function select($sql) {
        $result = pg_query($this->conn, $sql);
        if ($result === false) {
            return false;
        }
        return new pgsql_rdb_result($result);
    }
    public function execute($sql) {
        $result = pg_query($this->conn, $sql);
        if ($result === false) {
            return false;
        }
        $this->last_execute_result = $result;
        return pg_result_status($result) === PGSQL_COMMAND_OK;
    }
    public function insert_id() {
        $sql = 'SELECT LASTVAL() AS id';
        $result = pg_query($this->conn, $sql);
        if ($result === false) {
            return 0;
        }
        $row = pg_fetch_assoc($result);
        if ($row === false) {
            return 0;
        }
        return (int)$row['id'];
    }
    public function affected_rows() {
        if ($this->last_execute_result === null) {
            return 0;
        }
        return pg_affected_rows($this->last_execute_result);
    }
    public function begin() {
        return $this->run_command('BEGIN');
    }
    public function commit() {
        return $this->run_command('COMMIT');
    }
    public function rollback() {
        return $this->run_command('ROLLBACK');
    }
    public function last_error() {
        $error = pg_last_error($this->conn);
        if ($error === false) {
            $error = '';
        }
        return $error;
    }
    public function escape($value) {
        return pg_escape_string($this->conn, $value);
    }
    public function get_limit_sql($page_size, $begin_offset) {
        return "LIMIT {$page_size} OFFSET {$begin_offset}";
    }
    public function build_table_name($table_name) {
        return '"' . $table_name . '"';
    }
    public function build_field_name($field_name) {
        return '"' . $field_name . '"';
    }
    protected function run_command($command) {
        $result = pg_query($this->conn, $command);
        if ($result === false) {
            return false;
        }
        return pg_result_status($result) === PGSQL_COMMAND_OK;
    }
    protected $conn = null;
    protected $last_execute_result = null;
}
// [类型] postgresql 关系数据库结果集
class /* @kern */ pgsql_rdb_result extends rdb_result {
    public function __construct($result) {
        $this->result = $result;
    }
    public function fetch_record() {
        $record = pg_fetch_assoc($this->result);
        if ($record === false) {
            $record = null;
        } else {
            $i = 0;
            foreach ($record as $field_name => $value) {
                $field_type = pg_field_type($this->result, $i);
                if ($field_type === false) {
                    continue;
                }
                switch ($field_type) {
                case 'int1':
                case 'int2':
                case 'int4':
                case 'int8':
                case 'serial':
                    $record[$field_name] = intval($value);
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
        return pg_num_rows($this->result);
    }
    public function free() {
        pg_free_result($this->result);
    }
    protected $result = null;
}
