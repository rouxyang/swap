<?php
namespace kern;
if (!extension_loaded('Redis')) throw new environment_error('cannot use redis: Redis extension does not exist');
class redis_master_node extends \Redis {
}
class redis_slave_node extends \Redis {
}
class redis_pool {
    public static function get_master_redis($source_name) {
        if (!isset(self::$redis_nodes['master'][$source_name])) {
            $dsn = setting::get_logic('redis.' . $source_name . '.master');
            $url_parts = parse_url($dsn);
            extract($url_parts, EXTR_SKIP);
            $redis_node = new redis_master_node();
            if (!$redis_node->connect($host, $port, 2)) {
                throw new server_except("cannot connect to redis: {$dsn}");
            }
            self::$redis_nodes['master'][$source_name] = $redis_node;
        }
        return self::$redis_nodes['master'][$source_name];
    }
    public static function get_slave_redis($source_name) {
        if (!isset(self::$redis_nodes['slave'][$source_name])) {
            $dsns = setting::get_logic('redis.' . $source_name . '.slaves', []);
            if ($dsns === []) {
                $dsns = array(setting::get_logic('redis.' . $source_name . '.master'));
            }
            $all_attempts_failed = true;
            foreach ($dsns as $dsn) {
                $url_parts = parse_url($dsn);
                extract($url_parts, EXTR_SKIP);
                $redis_node = new redis_slave_node();
                if ($redis_node->connect($host, $port, 2)) {
                    self::$redis_nodes['slave'][$source_name] = $redis_node;
                    $all_attempts_failed = false;
                    break;
                } else {
                    logger::log_error("cannot connect to dsn: '{$dsn}', maybe failed?");
                }
            }
            if ($all_attempts_failed) {
                throw new server_except("cannot connect to all dsns of redis source: {$source_name}");
            }
        }
        return self::$redis_nodes['slave'][$source_name];
    }
    protected static $redis_nodes = array(
        'master' => [],
        'slave' => [],
    );
}
