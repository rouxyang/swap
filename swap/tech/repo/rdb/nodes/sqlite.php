<?php
/**
 * SQLite 关系数据库节点
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
use SQLite3;
// [类型] sqlite 关系数据库节点
abstract class sqlite_rdb_node extends rdb_node {}
// [类型] sqlite 关系数据库主节点
class sqlite_master_rdb_node extends sqlite_rdb_node {}
// [类型] sqlite 关系数据库从节点
class sqlite_slave_rdb_node extends sqlite_rdb_node {}
