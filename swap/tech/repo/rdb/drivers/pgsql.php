<?php
/**
 * PostgreSQL 关系数据库连接和结果集抽象
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
if (!extension_loaded('pgsql')) throw new developer_error('cannot use pgsql_rdb_conn: pgsql extension does not exist');
// [类型] postgresql 关系数据库连接
class /* @swap */ pgsql_rdb_conn extends rdb_conn {
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
            throw new remote_except("cannot connect to database: {$dsn}");
        }
        if (pg_set_client_encoding($conn, 'UTF8') !== 0) {
            throw new remote_except('cannot set charset to utf8');
        }
        $this->conn = conn;
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
    public function escape($value) {
        return pg_escape_string($this->conn, $value);
    }
    public function begin() {
        $result = pg_query($this->conn, 'BEGIN');
        if ($result === false) {
            return false;
        }
        return pg_result_status($result) === PGSQL_COMMAND_OK;
    }
    public function commit() {
        $result = pg_query($this->conn, 'COMMIT');
        if ($result === false) {
            return false;
        }
        return pg_result_status($result) === PGSQL_COMMAND_OK;
    }
    public function rollback() {
        $result = pg_query($this->conn, 'ROLLBACK');
        if ($result === false) {
            return false;
        }
        return pg_result_status($result) === PGSQL_COMMAND_OK;
    }
    public function last_error() {
        $error = pg_last_error($this->conn);
        if ($error === false) {
            $error = '';
        }
        return $error;
    }
    protected $conn = null;
    protected $last_execute_result = null;
}
// [类型] postgresql 关系数据库结果集
class /* @swap */ pgsql_rdb_result extends rdb_result {
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
