<?php
/**
 * PostgreSQL 关系数据库连接和结果集抽象
 *
 * @copyright Copyright (c) 2009-2014 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
if (!extension_loaded('pgsql')) throw new developer_error('cannot use pgsql_rdb_conn: pgsql extension does not exist');
// [类型] postgresql 关系数据库连接
class /* @swap */ pgsql_rdb_conn extends rdb_conn {
    public function __construct($dsn) {
    }
    public function select($sql) {
    }
    public function execute($sql) {
    }
    public function insert_id() {
    }
    public function affected_rows() {
    }
    public function escape($value) {
    }
    public function begin() {
    }
    public function commit() {
    }
    public function rollback() {
    }
    public function last_error() {
    }
    protected $conn = null;
}
// [类型] postgresql 关系数据库结果集
class /* @swap */ pgsql_rdb_result extends rdb_result {
    public function __construct($result) {
        $this->result = $result;
    }
    public function fetch_record() {
    }
    public function num_rows() {
    }
    public function free() {
    }
    protected $result = null;
}
