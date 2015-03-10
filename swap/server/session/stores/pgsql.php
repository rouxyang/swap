<?php
/**
 * 基于 PostgreSQL（通过 pgsql 驱动扩展）的会话数据源
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
if (!extension_loaded('pgsql')) throw new environment_error('cannot use pgsql_session_store: pgsql extension does not exist');
/**
 * [类型] postgresql 会话存储源
 */
class /* @swap */ pgsql_session_store extends session_store {
    public function __construct($dsn) {
        $url_parts = parse_url($dsn);
        extract($url_parts, EXTR_SKIP);
        list($name, $table_name) = explode('/', substr($path, 1));
        $port = (int)$port;
        foreach (array('host', 'user', 'pass', 'name') as $key) {
            ${$key} = "'" . addslashes(${$key}) . "'";
        }
        $conn = pg_connect("host={$host} port={$port} user={$user} password={$pass} dbname={$name}");
        if ($conn === false) {
            throw new remote_except("cannot connect to dsn '{$dsn}'");
        }
        if (pg_set_client_encoding($conn, 'UTF8') === -1) {
            throw new remote_except('cannot set charset to utf8');
        }
        $this->conn = $conn;
        $this->table_name = $table_name;
    }
    public function is_role_id_online($role_id) {
        $role_id = (int)$role_id;
        $sql = "SELECT * FROM \"{$this->table_name}\" WHERE \"role_id\" = " . $role_id;
        $result = pg_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new remote_except("except: {$sql}");
        }
        $num_rows = pg_num_rows($result);
        return $num_rows !== -1 && $num_rows !== 0;
    }
    public function online_count() {
        // @todo: implementation
        return 0;
    }
    public function clean() {
        $current_time = clock::get_stamp();
        $sql = "DELETE FROM \"{$this->table_name}\" WHERE \"expire_time\" <= " . $current_time;
        pg_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
    }
    public function fetch($sid) {
        $sid = addslashes($sid);
        $sql = "SELECT * FROM \"{$this->table_name}\" WHERE \"sid\" = '{$sid}'";
        $result = pg_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new remote_except("except: {$sql}");
        }
        $num_rows = pg_num_rows($result);
        if ($num_rows === -1) {
            throw new developer_error('unknown');
        }
        if ($num_rows === 0) {
            return null;
        } else if ($num_rows !== 1) {
            throw new developer_error("fatal error: sid '{$sid}' conflicts!");
        }
        $record = pg_fetch_assoc($result);
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
        $role_vars = addslashes(serialize($role_vars));
        $sid = addslashes($sid);
        $sql = "UPDATE \"{$this->table_name}\" SET \"expire_time\" = {$expire_time}, \"last_active\" = {$last_active}, \"role_id\" = {$role_id}, \"role_secret\" = '{$role_secret}', \"role_vars\" = '{$role_vars}' WHERE \"sid\" = '{$sid}'";
        $this->execute($sql);
    }
    public function create($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
        $role_vars = addslashes(serialize($role_vars));
        $sid = addslashes($sid);
        $sql = "INSERT INTO \"{$this->table_name}\" VALUES (NULL, '{$sid}', {$expire_time}, {$last_active}, {$role_id}, '{$role_secret}', '{$role_vars}')";
        $this->execute($sql);
    }
    public function remove($sid) {
        $sid = addslashes($sid);
        $sql = "DELETE FROM \"{$this->table_name}\" WHERE \"sid\" = '{$sid}'";
        $this->execute($sql);
    }
    protected function execute($sql) {
        $result = pg_query($this->conn, $sql);
        if (framework::is_debug()) {
            debug::save('session', $sql);
        }
        if ($result === false) {
            throw new remote_except("except: {$sql}");
        }
    }
    protected $conn = null;
    protected $table_name = '';
}
