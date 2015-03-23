<?php
/**
 * 基于 mysql（通过 mysqli 驱动扩展）的会话存储源
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
if (!extension_loaded('mysqli')) throw new environment_error('cannot use mysql_session_store: mysqli extension does not exist');
// [类型] mysql 会话存储源
class /* @kern */ mysql_session_store extends session_store {
    public function __construct($dsn) {
        $url_parts = parse_url($dsn);
        extract($url_parts, EXTR_SKIP);
        list($name, $table_name) = explode('/', substr($path, 1));
        $conn = mysqli_connect($host, $user, $pass, $name, $port);
        if ($conn === false) {
            throw new server_except("cannot connect to dsn '{$dsn}'");
        }
        if (!mysqli_set_charset($conn, 'utf8')) {
            throw new server_except('cannot set charset to utf8');
        }
        $this->conn = $conn;
        $this->table_name = $table_name;
    }
    public function is_role_id_online($role_id) {
        $role_id = (int)$role_id;
        $sql = "SELECT `id` FROM `{$this->table_name}` WHERE `role_id` = " . $role_id;
        $result = mysqli_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new server_except("except: {$sql}");
        }
        return mysqli_num_rows($result) !== 0;
    }
    public function online_count() {
        // 存储引擎是 InnoDB 的话，SELECT COUNT(*) FROM `table_name` 巨慢，只能用以下语句替代，代价是不准确
        $sql = "SHOW TABLE STATUS LIKE '{$this->table_name}'";
        $result = mysqli_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new server_except("except: {$sql}");
        }
        $num_rows = mysqli_num_rows($result);
        if ($num_rows !== 1) {
            throw new developer_error("show info error: {$sql}");
        }
        $record = mysqli_fetch_assoc($result);
        return (int)$record['Rows'];
    }
    public function clean() {
        $current_time = clock::get_stamp();
        $sql = "DELETE FROM `{$this->table_name}` WHERE `expire_time` <= " . $current_time;
        mysqli_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
    }
    public function fetch($sid) {
        $sid = mysqli_real_escape_string($this->conn, $sid);
        $sql = "SELECT * FROM `{$this->table_name}` WHERE `sid` = '{$sid}'";
        $result = mysqli_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new server_except("except: {$sql}");
        }
        $num_rows = mysqli_num_rows($result);
        if ($num_rows === 0) {
            return null;
        } else if ($num_rows !== 1) {
            throw new developer_error("fatal error: sid '{$sid}' conflicts!");
        }
        $record = mysqli_fetch_assoc($result);
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
        $role_vars = mysqli_real_escape_string($this->conn, serialize($role_vars));
        $sid = mysqli_real_escape_string($this->conn, $sid);
        $sql = "UPDATE `{$this->table_name}` SET `expire_time` = {$expire_time}, `last_active` = {$last_active}, `role_id` = {$role_id}, `role_secret` = '{$role_secret}', `role_vars` = '{$role_vars}' WHERE `sid` = '{$sid}'";
        $this->execute($sql);
    }
    public function create($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
        $role_vars = mysqli_real_escape_string($this->conn, serialize($role_vars));
        $sid = mysqli_real_escape_string($this->conn, $sid);
        $sql = "INSERT INTO `{$this->table_name}` VALUES (NULL, '{$sid}', {$expire_time}, {$last_active}, {$role_id}, '{$role_secret}', '{$role_vars}')";
        $this->execute($sql);
    }
    public function remove($sid) {
        $sid = mysqli_real_escape_string($this->conn, $sid);
        $sql = "DELETE FROM `{$this->table_name}` WHERE `sid` = '{$sid}'";
        $this->execute($sql);
    }
    protected function execute($sql) {
        $result = mysqli_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new server_except("except: {$sql}");
        }
    }
    protected $conn = null;
    protected $table_name = '';
}
