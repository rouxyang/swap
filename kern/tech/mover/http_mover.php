<?php
/**
 * 以 HTTP 的形式将文件移到 Upload
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
/**
 * HTTP 方式的文件移动器
 */
class http_mover extends mover {
    public function __construct($dsn) {
    }
    public function move_file($src_file_path, $dst_file_path) {
    }
    public function copy_file($src_file_path, $dst_file_path) {
    }
    public function write_file($data, $dst_file_path) {
    }
    public function delete_file($dst_file_path) {
    }
}
