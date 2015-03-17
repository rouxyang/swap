<?php
/**
 * 会话及其管理器
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
session_manager::__init__();
/**
 * [类型] 会话
 */
class session {
    public function has($key) {
        return array_key_exists($key, $this->role_vars);
    }
    public function get($key, $default_value = null) {
        return $this->has($key) ? $this->role_vars[$key] : $default_value;
    }
    public function get_all() {
        return $this->role_vars;
    }
    public function set($key, $value) {
        $this->role_vars[$key] = $value;
        $this->needs_update = true;
    }
    public function set_many(array $keyvalues) {
        $this->role_vars = array_merge($this->role_vars, $keyvalues);
        $this->needs_update = true;
    }
    public function del($key) {
        unset($this->role_vars[$key]);
        $this->needs_update = true;
    }
    public function del_all() {
        $this->role_vars = [];
        $this->needs_update = true;
    }
    public function get_sid() {
        return $this->sid;
    }
    public function get_last_active() {
        return $this->last_active;
    }
    public function get_role_id() {
        return $this->role_id;
    }
    public function get_role_secret() {
        return $this->role_secret;
    }
    public function /* @swap */ __construct($role_name, session_store $session_store, $sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
        $this->role_name = $role_name;
        $this->session_store = $session_store;
        $this->sid = $sid;
        $this->expire_time = $expire_time;
        $this->last_active = $last_active;
        $this->role_id = $role_id;
        $this->role_secret = $role_secret;
        $this->role_vars = $role_vars;
    }
    public function /* @swap */ needs_update($true_or_false = null) {
        if ($true_or_false === null) {
            return $this->needs_update;
        } else if (!is_bool($true_or_false)) {
            $this->needs_update = false;
        } else {
            $this->needs_update = $true_or_false;
        }
    }
    public function /* @swap */ persist() {
        if ($this->needs_update) {
            $current_time = clock::get_stamp();
            $expire_time = $this->expire_time + ($current_time - $this->last_active);
            $last_active = $current_time;
            $role_id = $this->role_id;
            $role_secret = $this->role_secret;
            $this->session_store->modify($this->sid, $expire_time, $last_active, $role_id, $role_secret, $this->role_vars);
        }
    }
    protected $role_name = '';
    protected $session_store = null;
    protected $sid = '';
    protected $expire_time = 0;
    protected $last_active = 0;
    protected $role_id = 0;
    protected $role_secret = '';
    protected $role_vars = [];
    protected $needs_update = false;
}
/**
 * [类型] 会话存储源
 */
abstract class /* @swap */ session_store {
    abstract public function __construct($dsn);
    abstract public function is_role_id_online($role_id);
    abstract public function online_count();
    abstract public function clean();
    abstract public function fetch($sid);
    abstract public function modify($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars);
    abstract public function create($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars);
    abstract public function remove($sid);
}
/**
 * [实体] 会话存储源池
 */
class /* @swap */ session_store_pool {
    public static function get_session_store($dsn) {
        static $session_stores = [];
        $dsn_is_array = is_array($dsn);
        $dsn_as_key = $dsn_is_array ? implode('', $dsn) : $dsn;
        if (!array_key_exists($dsn_as_key, $session_stores)) {
            list($session_store_type, ) = explode('://', $dsn_is_array ? current($dsn) : $dsn, 2);
            $session_store_class = __NAMESPACE__ . '\\' . $session_store_type . '_session_store';
            $session_store = new $session_store_class($dsn);
            $session_stores[$dsn_as_key] = $session_store;
        }
        return $session_stores[$dsn_as_key];
    }
}
/**
 * [实体] 会话管理器
 */
class session_manager {
    public static function new_sid() {
        return random_sha1();
    }
    public static function is_role_id_online($role_name, $role_id) {
        return self::get_session_store($role_name)->is_role_id_online($role_id);
    }
    public static function online_count($role_name) {
        return self::get_session_store($role_name)->online_count();
    }
    public static function clean($role_name) {
        return self::get_session_store($role_name)->clean();
    }
    public static function /* @swap */ create_session($role_name, $role_id, $sid, $login_seconds, array $role_vars) {
        $current_time = clock::get_stamp();
        $last_active = $expire_time = $current_time;
        $login_seconds = (int)$login_seconds;
        $expire_time += ($login_seconds === 0 ? self::$role_settings[$role_name]['default_alive_seconds'] : $login_seconds);
        $role_id = (int)$role_id;
        $role_secret = self::new_role_secret();
        $session_store = self::get_session_store($role_name);
        $session_store->create($sid, $expire_time, $last_active, $role_id, $role_secret, $role_vars);
        return new session($role_name, $session_store, $sid, $expire_time, $last_active, $role_id, $role_secret, $role_vars);
    }
    public static function /* @swap */ fetch_session($role_name, $sid) {
        $session_store = self::get_session_store($role_name);
        $record = $session_store->fetch($sid);
        if ($record === null) {
            return null;
        } else {
            $session = new session($role_name, $session_store, $sid, $record['expire_time'], $record['last_active'], $record['role_id'], $record['role_secret'], $record['role_vars']);
            if (self::$role_settings[$role_name]['trace_last_active']) {
                $session->needs_update(true);
            }
            return $session;
        }
    }
    public static function /* @swap */ remove_session($role_name, $sid) {
        self::get_session_store($role_name)->remove($sid);
    }
    public static function /* @swap */ __init__() {
        self::$role_settings = setting::get_module('visitor.roles', []);
    }
    protected static function new_role_secret() {
        return sha1(setting::get_swap('secret_key', '') . random_sha1());
    }
    protected static function get_session_store($role_name) {
        return session_store_pool::get_session_store(self::$role_settings[$role_name]['session_dsn']);
    }
    protected static $role_settings = [];
    protected static $sessions = [];
}
