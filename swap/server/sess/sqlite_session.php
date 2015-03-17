<?php
/**
 * 基于 SQLite（通过 sqlite3 驱动扩展）的会话数据源
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
use SQLite3;
use SQLite3Result;
use Exception;
if (!extension_loaded('SQLite3')) throw new environment_error('cannot use sqlite_session_store: sqlite3 extension does not exist');
/**
 * [类型] sqlite 会话存储源
 */
class /* @swap */ sqlite_session_store extends session_store {
    public function __construct($dsn) {
        # dsn 格式：sqlite:///path/to/session.db/user_session
        list(, $detail) = explode('://', $dsn);
        $path_parts = explode('/', $detail);
        $table_name = array_pop($path_parts);
        $db_file = implode('/', $path_parts);
        try {
            $conn = new SQLite3($db_file);
        } catch (Exception $e) {
            throw new server_except("cannot connecto to dsn '{$dsn}'");
        }
        $this->conn = $conn;
        $this->table_name = $table_name;
    }
    public function is_role_id_online($role_id) {
        $role_id = (int)$role_id;
        $sql = "SELECT `id` FROM `{$this->table_name}` WHERE `role_id` = " . $role_id;
        $result = $this->conn->query($sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new server_except("except: {$sql}");
        }
        return $this->result_num_rows($result) !== 0;
    }
    public function online_count() {
        $sql = "SELECT COUNT(*) AS `count` FROM `{$this->table_name}`";
        $result = $this->conn->query($sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new server_except("except: {$sql}");
        }
        if ($this->result_num_rows($result) !== 1) {
            throw new developer_error("fatal error: select count(*) error");
        }
        $record = $result->fetchArray(SQLITE3_ASSOC);
        return (int)$record['count'];
    }
    public function clean() {
        $current_time = clock::get_stamp();
        $sql = "DELETE FROM `{$this->table_name}` WHERE `expire_time` <= " . $current_time;
        $this->conn->query($sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
    }
    public function fetch($sid) {
        $sid = $this->conn->escapeString($sid);
        $sql = "SELECT * FROM `{$this->table_name}` WHERE `sid` = '{$sid}'";
        $result = $this->conn->query($sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new server_except("except: {$sql}");
        }
        $num_rows = $this->result_num_rows($result);
        if ($num_rows === 0) {
            return null;
        } else if ($num_rows !== 1) {
            throw new developer_error("fatal error: sid '{$sid}' conflicts!");
        }
        $record = $result->fetchArray(SQLITE3_ASSOC);
        return array(
            'sid' => $sid,
            'expire_time' => (int)$record['expire_time'],
            'last_active' => (int)$record['last_active'],
            'role_id' => (int)$record['role_id'],
            'role_secret' => $record['role_secret'],
            'role_vars' => unserialize($record['role_vars'])
        );
    }
    public function modify($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
        $role_vars = $this->conn->escapeString(serialize($role_vars));
        $sid = $this->conn->escapeString($sid);
        $sql = "UPDATE `{$this->table_name}` SET `expire_time` = {$expire_time}, `last_active` = {$last_active}, `role_id` = {$role_id}, `role_secret` = '{$role_secret}', `role_vars` = '{$role_vars}' WHERE `sid` = '{$sid}'";
        $this->execute($sql);
    }
    public function create($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
        $role_vars = $this->conn->escapeString(serialize($role_vars));
        $sid = $this->conn->escapeString($sid);
        $sql = "INSERT INTO `{$this->table_name}` VALUES (NULL, '{$sid}', {$expire_time}, {$last_active}, {$role_id}, '{$role_secret}', '{$role_vars}')";
        $this->execute($sql);
    }
    public function remove($sid) {
        $sid = $this->conn->escapeString($sid);
        $sql = "DELETE FROM `{$this->table_name}` WHERE `sid` = '{$sid}'";
        $this->execute($sql);
    }
    protected function execute($sql) {
        $result = $this->conn->query($sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new server_except("except: {$sql}");
        }
    }
    protected function result_num_rows(SQLite3Result $result) {
        $rows_count = 0;
        $result->reset();
        // @todo: strict comparison
        while ($result->fetchArray(SQLITE3_ASSOC)) {
            $rows_count++;
        }
        $result->reset();
        return $rows_count;
    }
    protected $conn = null;
    protected $table_name = '';
}
