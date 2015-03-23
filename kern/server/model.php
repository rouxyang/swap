<?php
/**
 * 基于实体联系模型的领域模型
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
// 去除最后一个分隔符及其后面的字符串后缀
function /* @kern */ strip_suffix($str, $separator = '_') {
    $last_pos = strrpos($str, $separator);
    if ($last_pos !== false) {
        $str = substr($str, 0, $last_pos);
    }
    return $str;
}
// [类型] 模型接口
interface model_api {
    function __construct($is_new = true);
    function __get($key);
    function __set($key, $value);
    function __isset($key);
    function __unset($key);
    function get_props();
    function set_props(array $props);
    function add_props(array $props);
    function save();
    
    static function get(array $keyvalues, array $order_limit = array([], 0, 0));
    static function get_one(array $keyvalues);
    static function get_where($where, $args = [], array $order_limit = array([], 0, 0));
    static function get_by_id($id);
    static function get_by_ids(array $ids, array $order_limit = array([], 0, 0));
    static function get_in($field_name, array $values, array $order_limit = array([], 0, 0));
    static function get_all(array $order_limit = array([], 0, 0));
    static function fetch($sql, array $args = []);
    static function fetch_one($sql, array $args = []);
    
    static function pager(array $keyvalues, array $order_limit = array([], 0, 0));
    static function pager_where($where, array $args = [], array $order_limit = array([], 0, 0));
    static function pager_by_ids(array $ids, array $order_limit = array([], 0, 0));
    static function pager_in($field_name, array $values, array $order_limit = array([], 0, 0));
    static function pager_all(array $order_limit = array([], 0, 0));
    static function pager_with_count($count, array $keyvalues, array $order_limit = array([], 0, 0));
    static function pager_where_with_count($count, $where, array $args = [], array $order_limit = array([], 0, 0));
    static function pager_by_ids_with_count($count, array $ids, array $order_limit = array([], 0, 0));
    static function pager_in_with_count($count, $field_name, array $values, array $order_limit = array([], 0, 0));
    static function pager_all_with_count($count, array $order_limit = array([], 0, 0));
    
    static function count(array $keyvalues);
    static function count_where($where, array $args = []);
    static function count_by_ids(array $ids);
    static function count_in($field_name, array $values);
    static function count_all();
    
    static function set(array $keyvalues, array $conditions);
    static function set_where(array $keyvalues, $where, array $args = []);
    static function set_by_id(array $keyvalues, $id);
    static function set_by_ids(array $keyvalues, array $ids);
    static function set_all(array $keyvalues);
    static function modify($sql, array $args = []);
    
    static function add(array $keyvalues);
    static function add_many(array $keyvalues_list);
    static function create($sql, array $args = []);
    
    static function del(array $keyvalues);
    static function del_where($where, array $args = []);
    static function del_by_id($id);
    static function del_by_ids(array $ids);
    static function remove($sql, array $args = []);
    
    static function inc(array $keyvalues, array $conditions);
    static function inc_by_id(array $keyvalues, $id);
    static function inc_by_ids(array $keyvalues, array $ids);
    static function set_and_inc(array $sets, array $incs, array $conditions);
    static function set_and_inc_by_id(array $sets, array $incs, $id);
    static function set_and_inc_by_ids(array $sets, array $incs, array $ids);
    static function dec(array $keyvalues, array $conditions);
    static function dec_by_id(array $keyvalues, $id);
    static function dec_by_ids(array $keyvalues, array $ids);
}
// [类型] 模型
abstract class model implements model_api, html_escapable {
    public function __construct($is_new = true) {
        if (!is_bool($is_new)) {
            throw new developer_error('bad constructor argument, should be bool');
        }
        $this->_is_new = $is_new;
        if ($is_new) {
            $this->_model_name = strip_suffix(get_class($this));
        }
    }
    public function __get($key) {
        return isset($this->_current_props[$key]) ? $this->_current_props[$key] : null;
    }
    public function __set($key, $value) {
        $this->_current_props[$key] = $value;
        if ($this->_is_new) {
            $this->_original_props[$key] = $value;
        }
    }
    public function __isset($key) {
        return isset($this->_current_props[$key]);
    }
    public function __unset($key) {
        unset($this->_current_props[$key]);
    }
    public function get_props() {
        $props = $this->_current_props;
        foreach ($props as $key => $value) {
            if ($value instanceof model_api) {
                $props[$key] = $value->get_props();
            }
        }
        return $props;
    }
    public function set_props(array $props) {
        $this->_current_props = $props;
        if ($this->_is_new) {
            $this->_original_props = $this->_current_props;
        }
    }
    public function add_props(array $props) {
        $this->_current_props = array_merge($this->_current_props, $props);
        if ($this->_is_new) {
            $this->_original_props = $this->_current_props;
        }
    }
    public function save() {
        $current_props = $this->_current_props;
        if ($this->_is_new) {
            unset($current_props['id']);
            $id = rdb::add($this->_model_name, $current_props);
            $this->_current_props['id'] = $id;
            $this->_original_props['id'] = $id;
            $this->_is_new = false;
        } else {
            $original_props = $this->_original_props;
            if ($current_props === $original_props) {
                return;
            }
            $id = $original_props['id'];
            unset($current_props['id']);
            unset($original_props['id']);
            $props_diff = array_diff_assoc($current_props, $original_props);
            if ($props_diff !== []) {
                rdb::set_by_id($this->_model_name, $props_diff, $id);
            }
        }
    }
    
    public static function get(array $keyvalues, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        return self::create_models($model_name, rdb::get($model_name, $keyvalues, $order_limit));
    }
    public static function get_one(array $keyvalues) {
        $model_name = self::get_model_name();
        return self::create_model($model_name, rdb::get_one($model_name, $keyvalues));
    }
    public static function get_where($where, $args = [], array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        return self::create_models($model_name, rdb::get_where($model_name, $where, $args, $order_limit));
    }
    public static function get_by_id($id) {
        $model_name = self::get_model_name();
        return self::create_model($model_name, rdb::get_by_id($model_name, $id));
    }
    public static function get_by_ids(array $ids, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        return self::create_models($model_name, rdb::get_by_ids($model_name, $ids, $order_limit));
    }
    public static function get_in($field_name, array $values, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        return self::create_models($model_name, rdb::get_in($model_name, $field_name, $values, $order_limit));
    }
    public static function get_all(array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        return self::create_models($model_name, rdb::get_all($model_name, $order_limit));
    }
    public static function fetch($sql, array $args = []) {
        $model_name = self::get_model_name();
        return self::create_models($model_name, rdb::fetch($model_name, $sql, $args));
    }
    public static function fetch_one($sql, array $args = []) {
        $model_name = self::get_model_name();
        return self::create_model($model_name, rdb::fetch_one($model_name, $sql, $args));
    }
    
    public static function pager(array $keyvalues, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager($model_name, $keyvalues, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_where($where, array $args = [], array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_where($model_name, $where, $args, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_by_ids(array $ids, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_by_ids($model_name, $ids, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_in($field_name, array $values, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_in($model_name, $field_name, $values, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_all(array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_all($model_name, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_with_count($count, array $keyvalues, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_with_count($count, $model_name, $keyvalues, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_where_with_count($count, $where, array $args = [], array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_where_with_count($count, $model_name, $where, $args, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_by_ids_with_count($count, array $ids, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_by_ids_with_count($count, $model_name, $ids, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_in_with_count($count, $field_name, array $values, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_in_with_count($count, $model_name, $field_name, $values, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    public static function pager_all_with_count($count, array $order_limit = array([], 0, 0)) {
        $model_name = self::get_model_name();
        list($pager, $records) = rdb::pager_all_with_count($count, $model_name, $order_limit);
        return array($pager, self::create_models($model_name, $records));
    }
    
    public static function count(array $keyvalues) {
        return rdb::count(self::get_model_name(), $keyvalues);
    }
    public static function count_where($where, array $args = []) {
        return rdb::count_where(self::get_model_name(), $where, $args);
    }
    public static function count_by_ids(array $ids) {
        return rdb::count_by_ids(self::get_model_name(), $ids);
    }
    public static function count_in($field_name, array $values) {
        return rdb::count_in(self::get_model_name(), $field_name, $values);
    }
    public static function count_all() {
        return rdb::count_all(self::get_model_name());
    }
    
    public static function set(array $keyvalues, array $conditions) {
        return rdb::set(self::get_model_name(), $keyvalues, $conditions);
    }
    public static function set_where(array $keyvalues, $where, array $args = []) {
        return rdb::set_where(self::get_model_name(), $keyvalues, $where, $args);
    }
    public static function set_by_id(array $keyvalues, $id) {
        return rdb::set_by_id(self::get_model_name(), $keyvalues, $id);
    }
    public static function set_by_ids(array $keyvalues, array $ids) {
        return rdb::set_by_ids(self::get_model_name(), $keyvalues, $ids);
    }
    public static function set_all(array $keyvalues) {
        return rdb::set_all(self::get_model_name(), $keyvalues);
    }
    public static function modify($sql, array $args = []) {
        return rdb::modify(self::get_model_name(), $sql, $args);
    }
    
    public static function add(array $keyvalues) {
        return rdb::add(self::get_model_name(), $keyvalues);
    }
    public static function add_many(array $keyvalues_list) {
        return rdb::add_many(self::get_model_name(), $keyvalues_list);
    }
    public static function create($sql, array $args = []) {
        return rdb::create(self::get_model_name(), $sql, $args);
    }
    
    public static function del(array $keyvalues) {
        return rdb::del(self::get_model_name(), $keyvalues);
    }
    public static function del_where($where, array $args = []) {
        return rdb::del_where(self::get_model_name(), $where, $args);
    }
    public static function del_by_id($id) {
        return rdb::del_by_id(self::get_model_name(), $id);
    }
    public static function del_by_ids(array $ids) {
        return rdb::del_by_ids(self::get_model_name(), $ids);
    }
    public static function remove($sql, array $args = []) {
        return rdb::remove(self::get_model_name(), $sql, $args);
    }
    
    public static function inc(array $keyvalues, array $conditions) {
        return rdb::inc(self::get_model_name(), $keyvalues, $conditions);
    }
    public static function inc_by_id(array $keyvalues, $id) {
        return rdb::inc_by_id(self::get_model_name(), $keyvalues, $id);
    }
    public static function inc_by_ids(array $keyvalues, array $ids) {
        return rdb::inc_by_ids(self::get_model_name(), $keyvalues, $ids);
    }
    public static function set_and_inc(array $sets, array $incs, array $conditions) {
        return rdb::set_and_inc(self::get_model_name(), $sets, $incs, $conditions);
    }
    public static function set_and_inc_by_id(array $sets, array $incs, $id) {
        return rdb::set_and_inc_by_id(self::get_model_name(), $sets, $incs, $id);
    }
    public static function set_and_inc_by_ids(array $sets, array $incs, array $ids) {
        return rdb::set_and_inc_by_ids(self::get_model_name(), $sets, $incs, $ids);
    }
    public static function dec(array $keyvalues, array $conditions) {
        return rdb::dec(self::get_model_name(), $keyvalues, $conditions);
    }
    public static function dec_by_id(array $keyvalues, $id) {
        return rdb::dec_by_id(self::get_model_name(), $keyvalues, $id);
    }
    public static function dec_by_ids(array $keyvalues, array $ids) {
        return rdb::dec_by_ids(self::get_model_name(), $keyvalues, $ids);
    }
    
    protected static function /* @kern */ get_model_name() {
        return strip_suffix(get_called_class());
    }
    protected static function /* @kern */ create_model($model_name, $record) {
        if ($record === null) {
            return null;
        }
        return self::do_create_model($model_name, $record);
    }
    protected static function /* @kern */ create_models($model_name, array $records) {
        if ($records === []) {
            return [];
        }
        $models = [];
        foreach ($records as $model_id => $record) {
            $models[$model_id] = self::do_create_model($model_name, $record);
        }
        return $models;
    }
    protected static function /* @kern */ do_create_model($model_name, array $record) {
        $class_name = $model_name . '_model';
        $model = new $class_name(false);
        $model->_model_name = $model_name;
        foreach ($record as $prop_name => $prop_value) {
            $model->_current_props[$prop_name] = $prop_value;
        }
        $model->_original_props = $model->_current_props;
        return $model;
    }
    
    public function /* @kern */ html_escape() {
        $that = clone $this;
        $that->_current_props = html::escape($that->_current_props);
        return $that;
    }
    public function /* @kern */ html_unescape() {
        $that = clone $this;
        $that->_current_props = html::unescape($that->_current_props);
        return $that;
    }
    protected $_model_name = '';
    protected $_is_new = false;
    protected $_current_props = [];
    protected $_original_props = [];
}
// [实体] 关联关系绑定器
class binder {
    public static function bind($model_arg, $assoc_type_name, $assoc_model_name /*, ... */) {
        if ($model_arg === null || $model_arg === []) {
            return;
        }
        if (is_array($model_arg)) {
            $model_type = 'multiple_models';
            $model = current($model_arg);
        } else {
            $model_type = 'single_model';
            $model = $model_arg;
        }
        if (!$model instanceof model) {
            throw new developer_error('bad model, expect object, but get ' . gettype($model));
        }
        $binder = 'bind_' . $model_type . '_with_assoc_model_of_' . $assoc_type_name;
        $assoc_class_name = $assoc_model_name . '_model';
        $model_class_name = get_class($model);
        $model_name = strip_suffix($model_class_name);
        $func_args = func_get_args();
        switch ($assoc_type_name) {
            case 'points_to':
            case 'belongs_to':
            case 'has_one': {
                $refer_field_name = array_key_exists(3, $func_args) ? $func_args[3] : $assoc_model_name . '_id';
                $as_field_name = array_key_exists(4, $func_args) ? $func_args[4] : $assoc_model_name;
                self::$binder($model_arg, $assoc_class_name, $refer_field_name, $as_field_name);
                break;
            }
            case 'has_many': {
                $order_limit = array_key_exists(3, $func_args) ? $func_args[3] : 0;
                $refer_field_name = array_key_exists(4, $func_args) ? $func_args[4] : $assoc_model_name . '_id';
                $as_field_name = array_key_exists(5, $func_args) ? $func_args[5] : $assoc_model_name;
                self::$binder($model_arg, $assoc_class_name, $order_limit, $refer_field_name, $as_field_name);
                break;
            }
            case 'many_many': {
                $through = $func_args[3];
                $through_field_names = array_key_exists(4, $func_args) ? $func_args[4] : array($model_name . '_id', $assoc_model_name . '_id');
                $as_field_name = array_key_exists(5, $func_args) ? $func_args[5] : $through[0];
                self::$binder($model_arg, $assoc_class_name, $through, $through_field_names, $as_field_name);
                break;
            }
            default: {
                throw new developer_error('未知的关联类型：' . $assoc_type_name);
            }
        }
    }
    // binder::bind($user, 'points_to', 'tweet', 'last_tweet_id', 'last_tweet');
    protected static function bind_single_model_with_assoc_model_of_points_to(model $model, $assoc_class_name, $refer_field_name, $as_field_name) {
        self::bind_single_model_with_assoc_model_of_belongs_to($model, $assoc_class_name, $refer_field_name, $as_field_name);
    }
    // binder::bind($users, 'points_to', 'tweet', 'last_tweet_id', 'last_tweet');
    protected static function bind_multiple_models_with_assoc_model_of_points_to(array &$models, $assoc_class_name, $refer_field_name, $as_field_name) {
        self::bind_multiple_models_with_assoc_model_of_belongs_to($models, $assoc_class_name, $refer_field_name, $as_field_name);
    }
    // binder::bind($comment, 'belongs_to', 'post', 'post_id', 'post');
    protected static function bind_single_model_with_assoc_model_of_belongs_to(model $model, $assoc_class_name, $refer_field_name, $as_field_name) {
        $model->$as_field_name = $assoc_class_name::get_by_id($model->$refer_field_name);
    }
    // binder::bind($comments, 'belongs_to', 'post', 'post_id', 'post');
    protected static function bind_multiple_models_with_assoc_model_of_belongs_to(array &$models, $assoc_class_name, $refer_field_name, $as_field_name) {
        self::init_field_value_for($models, $as_field_name, null);
        $assoc_model_ids = self::get_assoc_model_ids_from($models, $refer_field_name);
        $assoc_models = $assoc_class_name::get_by_ids($assoc_model_ids);
        if ($assoc_models === []) {
            return;
        }
        foreach ($models as &$model) {
            $assoc_model_id = $model->$refer_field_name;
            if (array_key_exists($assoc_model_id, $assoc_models)) {
                $model->$as_field_name = $assoc_models[$assoc_model_id];
            }
        }
        unset($model);
        reset($models);
    }
    // binder::bind($user, 'has_one', 'info', 'user_id', 'info');
    protected static function bind_single_model_with_assoc_model_of_has_one(model $model, $assoc_class_name, $refer_field_name, $as_field_name) {
        $model->$as_field_name = $assoc_class_name::get_one(array($refer_field_name => $model->id));
    }
    // binder::bind($users, 'has_one', 'info', 'user_id', 'info');
    protected static function bind_multiple_models_with_assoc_model_of_has_one(array &$models, $assoc_class_name, $refer_field_name, $as_field_name) {
        self::init_field_value_for($models, $as_field_name, null);
        $model_ids = self::get_model_ids_from($models);
        $assoc_models = $assoc_class_name::get_in($refer_field_name, $model_ids);
        if ($assoc_models === []) {
            return;
        }
        foreach ($assoc_models as $assoc_model) {
            $model_id = $assoc_model->$refer_field_name;
            $models[$model_id]->$as_field_name = $assoc_model;
        }
    }
    // binder::bind($user, 'has_many', 'comment', 0, 'user_id', 'comments');
    protected static function bind_single_model_with_assoc_model_of_has_many(model $model, $assoc_class_name, $order_limit, $refer_field_name, $as_field_name) {
        $assoc_models = $assoc_class_name::get(array($refer_field_name => $model->id), $order_limit === 0 ? array([], 0, 0) : $order_limit);
        $model->$as_field_name = $assoc_models;
    }
    // binder::bind($users, 'has_many', 'comment', 0, 'user_id', 'comments');
    protected static function bind_multiple_models_with_assoc_model_of_has_many(array &$models, $assoc_class_name, $order_limit, $refer_field_name, $as_field_name) {
        if ($order_limit === 0) {
            self::init_field_value_for($models, $as_field_name, []);
            $model_ids = self::get_model_ids_from($models);
            $assoc_models = $assoc_class_name::get_in($refer_field_name, $model_ids);
            if ($assoc_models === []) {
                return;
            }
            $model_id_to_assoc_models = [];
            foreach ($assoc_models as $assoc_model) {
                $model_id = $assoc_model->$refer_field_name;
                if (!isset($model_id_to_assoc_models[$model_id])) {
                    $model_id_to_assoc_models[$model_id] = [];
                }
                $model_id_to_assoc_models[$model_id][$assoc_model->id] = $assoc_model;
            }
            foreach ($model_id_to_assoc_models as $model_id => $assoc_models) {
                $models[$model_id]->$as_field_name = $assoc_models;
            }
        } else {
            foreach ($models as $model) {
                self::bind_single_model_with_assoc_model_of_has_many($model, $assoc_class_name, $order_limit, $refer_field_name, $as_field_name);
            }
            reset($models);
        }
    }
    // binder::bind($user, 'many_many', 'board', array('board_manager', 0), array('user_id', 'board_id'), 'board_managers');
    // binder::bind($user, 'many_many', 'user', array('follow', 0), array('follower_id', 'followee_id'), 'stars');
    protected static function bind_single_model_with_assoc_model_of_many_many(model $model, $assoc_class_name, array $through, array $through_field_names, $as_field_name) {
        $model->$as_field_name = [];
        list($through_model_name, $order_limit) = $through;
        $through_class_name = $through_model_name . '_model';
        list($from_field_name, $to_field_name) = $through_field_names;
        $model_id = $model->id;
        $through_models = $through_class_name::get(array($from_field_name => $model_id), $order_limit === 0 ? array([], 0, 0) : $order_limit);
        if ($through_models === []) {
            return;
        }
        $assoc_model_ids = [];
        foreach ($through_models as $through_model) {
            $assoc_model_ids[] = $through_model->$to_field_name;
        }
        $assoc_models = $assoc_class_name::get_by_ids($assoc_model_ids);
        if ($assoc_models !== []) {
            $model->$as_field_name = $assoc_models;
        }
    }
    // binder::bind($users, 'many_many', 'board', array('board_manager', 0), array('user_id', 'board_id'), 'board_managers');
    // binder::bind($users, 'many_many', 'user', array('follow', 0), array('follower_id', 'followee_id'), 'stars');
    protected static function bind_multiple_models_with_assoc_model_of_many_many(array &$models, $assoc_class_name, array $through, array $through_field_names, $as_field_name) {
        list($through_model_name, $order_limit) = $through;
        if ($order_limit === 0) {
            self::init_field_value_for($models, $as_field_name, []);
            $through_class_name = $through_model_name . '_model';
            list($from_field_name, $to_field_name) = $through_field_names;
            $model_ids = self::get_model_ids_from($models);
            $through_models = $through_class_name::get_in($from_field_name, $model_ids);
            if ($through_models === []) {
                return;
            }
            $assoc_model_ids = [];
            foreach ($through_models as $through_model) {
                $assoc_model_ids[] = $through_model->$to_field_name;
            }
            $assoc_models = $assoc_class_name::get_by_ids($assoc_model_ids);
            if ($assoc_models === []) {
                return;
            }
            $model_id_to_assoc_models = [];
            foreach ($through_models as $through_model) {
                $model_id = $through_model->$from_field_name;
                $assoc_model_id = $through_model->$to_field_name;
                if (!isset($assoc_models[$assoc_model_id])) {
                    continue;
                }
                if (!isset($model_id_to_assoc_models[$model_id])) {
                    $model_id_to_assoc_models[$model_id] = [];
                }
                $model_id_to_assoc_models[$model_id][$assoc_model_id] = $assoc_models[$assoc_model_id];
            }
            foreach ($model_id_to_assoc_models as $model_id => $assoc_models) {
                $models[$model_id]->$as_field_name = $assoc_models;
            }
        } else {
            foreach ($models as $model) {
                self::bind_single_model_with_assoc_model_of_many_many($model, $assoc_class_name, $through, $through_field_names, $as_field_name);
            }
            reset($models);
        }
    }
    protected static function get_model_ids_from(array $models) {
        $model_ids = [];
        foreach ($models as $model) {
            $model_ids[] = $model->id;
        }
        return $model_ids;
    }
    protected static function get_assoc_model_ids_from(array $models, $refer_field_name) {
        $assoc_model_ids = [];
        foreach ($models as $model) {
            $assoc_model_ids[] = $model->$refer_field_name;
        }
        return array_unique($assoc_model_ids);
    }
    protected static function init_field_value_for(array &$models, $field_name, $value) {
        foreach ($models as &$model) {
            $model->$field_name = $value;
        }
        unset($model);
        reset($models);
    }
}
