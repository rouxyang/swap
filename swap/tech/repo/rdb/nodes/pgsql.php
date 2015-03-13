<?php
/**
 * PostgreSQL 关系数据库节点
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
// [类型] postgresql 关系数据库节点
abstract class pgsql_rdb_node extends rdb_node {}
// [类型] postgresql 关系数据库主节点
class pgsql_master_rdb_node extends pgsql_rdb_node {}
// [类型] postgresql 关系数据库从节点
class pgsql_slave_rdb_node extends pgsql_rdb_node {}
