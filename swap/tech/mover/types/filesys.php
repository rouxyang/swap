<?php
/**
 * 以本地文件移动的方式将文件移到 Upload
 *
 * @copyright Copyright (c) 2009-2014 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
/**
 * [类型] 本地文件方式的文件移动器
 */
class filesys_mover extends mover {
    public function __construct($dsn) {
        list(, $this->target_dir) = explode('://', $dsn, 2);
    }
    public function move_file($src_file_path, $dst_file_path) {
        return rename($src_file_path, $this->target_dir . '/' . $dst_file_path);
    }
    public function copy_file($src_file_path, $dst_file_path) {
        return copy($src_file_path, $this->target_dir . '/' . $dst_file_path);
    }
    public function write_file($data, $dst_file_path) {
        $dir = $this->target_dir . '/' . dirname($dst_file_path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return file_put_contents($this->target_dir . '/' . $dst_file_path, $data);
    }
    public function delete_file($dst_file_path) {
        return unlink($this->target_dir . '/' . $dst_file_path);
    }
    protected $target_dir = '';
}
