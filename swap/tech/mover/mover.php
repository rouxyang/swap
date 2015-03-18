<?php
/**
 * 移动用户上传的文件到 Upload
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
/**
 * [类型] upload 移动器
 */
abstract class mover {
    abstract public function move_file($src_file_path, $dst_file_path);
    abstract public function copy_file($src_file_path, $dst_file_path);
    abstract public function write_file($data, $dst_file_path);
    abstract public function delete_file($dst_file_path);
    public static function move_file_to($target_name, $src_file_path, $dst_file_path) {
        $mover = mover_pool::get_mover($target_name);
        $mover->move_file($src_file_path, $dst_file_path);
    }
    public static function copy_file_to($target_name, $src_file_path, $dst_file_path) {
        $mover = mover_pool::get_mover($target_name);
        $mover->copy_file($src_file_path, $dst_file_path);
    }
    public static function write_file_to($target_name, $data, $dst_file_path) {
        $mover = mover_pool::get_mover($target_name);
        $mover->write_file($data, $dst_file_path);
    }
    public static function delete_file_on($target_name, $dst_file_path) {
        $mover = mover_pool::get_mover($target_name);
        $mover->delete_file($dst_file_path);
    }
}
/**
 * [实体] 移动器池
 */
class mover_pool {
    public static function get_mover($target_name) {
        static $movers = [];
        if (!isset($movers[$target_name])) {
            $dsn = setting::get_logic('mover.' . $target_name);
            list($mover_type, ) = explode('://', $dsn, 2);
            $mover_class = __NAMESPACE__ . '\\' . $mover_type . '_mover';
            if (!class_exists($mover_class, true)) {
                throw new developer_error("mover: '{$mover_class}' does not exist");
            }
            $mover = new $mover_class($dsn);
            $movers[$target_name] = $mover;
        }
        return $movers[$target_name];
    }
}
