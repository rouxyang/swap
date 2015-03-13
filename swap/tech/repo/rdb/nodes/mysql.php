<?php
/**
 * MySQL 关系数据库节点
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
// [类型] mysql 关系数据库节点
abstract class mysql_rdb_node extends rdb_node {}
// [类型] mysql 关系数据库主节点
class mysql_master_rdb_node extends mysql_rdb_node {}
// [类型] mysql 关系数据库从节点
class mysql_slave_rdb_node extends mysql_rdb_node {}
