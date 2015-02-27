<?php
use swap\session_manager;
use swap\cache_pool;
class online_service {
    public static function get_online_count() {
        $misc_cache = cache_pool::get_cache('misc');
        $online_count = $misc_cache->get('online_count');
        if ($online_count === null) {
            $online_count = session_manager::online_count('user');
            $misc_cache->set('online_count', $online_count, 30);
        }
        return $online_count;
    }
    public static function refresh_online_count() {
        $misc_cache = cache_pool::get_cache('misc');
        $online_count = session_manager::online_count('user');
        $misc_cache->set('online_count', $online_count, 30);
    }
}
