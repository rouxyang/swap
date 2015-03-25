<?php
/**
 * 数据校验器
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
// [类型] 数据校验器抽象
abstract class checker {
    const model_rules_getter = 'prop_rules';
    public function __construct(array $props) {
        $this->props = $props;
        $this->checked_props = [];
    }
    public function exists($prop_name) {
        return isset($this->props[$prop_name]);
    }
    public function check($prop_name, array $rules) {
        if (!$this->exists($prop_name)) {
            $this->failed($prop_name, $prop_name . '必须提供');
            return;
        }
        $this->checked_props[$prop_name] = $this->props[$prop_name];
        $this->prop_rules[$prop_name] = $rules;
        $this->do_check($prop_name, $this->checked_props, $rules);
    }
    public function check_model_rules($model_name, $for_operation = null, $extra_data = null) {
        $model = $model_name . '_model';
        if (method_exists($model, self::model_rules_getter)) {
            $this->check_prop_rules($model::prop_rules($for_operation, $extra_data));
        }
    }
    public function check_prop_rules($prop_rules) {
        foreach ($prop_rules as $prop_name => $rules) {
            $this->check($prop_name, $rules);
        }
    }
    public function failed($prop_name, $reason) {
        $this->reasons[$prop_name] = $reason;
    }
    public function check_failed() {
        return $this->get_reasons() !== [];
    }
    public function get_reasons() {
        return $this->reasons;
    }
    public function del($prop_name) {
        unset($this->checked_props[$prop_name]);
    }
    public function __get($prop_name) {
        return isset($this->checked_props[$prop_name]) ? $this->checked_props[$prop_name] : null;
    }
    public function get($prop_name, $default_value = null) {
        return isset($this->checked_props[$prop_name]) ? $this->checked_props[$prop_name] : $default_value;
    }
    public function get_all() {
        return $this->checked_props;
    }
    protected function do_check($prop_name, &$props, $rules) {
        $has_same_as = false;
        if (isset($rules['same_as'])) {
            // 如果 prop_name 跟另一个 prop_name 等同，则找到另一个 prop_name 的 rules
            $has_same_as = true;
            list($as_prop_name, $reason) = $rules['same_as'];
            $rules = $this->prop_rules[$as_prop_name];
            while (isset($rules['same_as'])) {
                $as_prop_name = $rules['same_as'][0];
                $rules = $this->prop_rules[$as_prop_name];
            }
        }
        // 这些规则会改变 prop_name 的值
        if (isset($rules['change_to'])) {
            $prop_value = $props[$prop_name];
            $rule_value = $rules['change_to'];
            if ($rule_value === 'int') {
                $changed_value = (int)$prop_value;
            } else if ($rule_value === 'string') {
                $changed_value = (string)$prop_value;
            } else if ($rule_value === 'array') {
                $changed_value = (array)$prop_value;
            } else {
                throw new developer_error();
            }
            $props[$prop_name] = $changed_value;
            unset($rules['change_to']);
        }
        if (isset($rules['trim_value']) && $rules['trim_value']) {
            $props[$prop_name] = trim($props[$prop_name]);
            unset($rules['trim_value']);
        }
        if (isset($rules['on_equal_be'])) {
            $rule_value = $rules['on_equal_be'];
            if ($props[$prop_name] === $rule_value[0]) {
                $props[$prop_name] = $rule_value[1];
            }
            unset($rules['on_equal_be']);
        }
        if (isset($rules['pass_if_be'])) {
            if ($props[$prop_name] === $rules['pass_if_be']) {
                return;
            }
            unset($rules['pass_if_be']);
        }
        // 如果为等同模式，则在改完当前字段的值后，跟另一个字段的值（改过后的）比较，不相等则失败
        if ($has_same_as && ($props[$prop_name] !== $this->get($as_prop_name))) {
            $this->failed($prop_name, $reason);
            return;
        }
        // 以下为不改值的检查
        foreach ($rules as $rule_name => $rule_value) {
            $check_function = 'check_' . $rule_name;
            $reason = $this->$check_function($prop_name, $props[$prop_name], $rule_value);
            if (is_string($reason)) {
                $this->failed($prop_name, $reason);
                break;
            }
        }
    }
    protected function check_cannot_be($prop_name, $prop_value, $rule_value) {
        list($value, $reason) = $rule_value;
        if ($prop_value === $value) {
            return sprintf($reason, $value);
        }
    }
    protected function check_cannot_in($prop_name, $prop_value, $rule_value) {
        list($value_list, $reason) = $rule_value;
        if (is_array($prop_value)) {
            if (array_intersect($prop_value, $value_list) !== []) {
                return $reason;
            }
        } else if (in_array($prop_value, $value_list)) {
            return $reason;
        }
    }
    protected function check_should_be($prop_name, $prop_value, $rule_value) {
        list($value, $reason) = $rule_value;
        if ($prop_value !== $value) {
            return sprintf($reason, $value);
        }
    }
    protected function check_should_in($prop_name, $prop_value, $rule_value) {
        list($value_list, $reason) = $rule_value;
        if (is_array($prop_value)) {
            if (array_diff($prop_value, $value_list) !== []) {
                return $reason;
            }
        } else if (!in_array($prop_value, $value_list)) {
            return $reason;
        }
    }
    protected function check_regexp($prop_name, $prop_value, $rule_value) {
        list($regexp, $reason) = $rule_value;
        if (!preg_match($regexp, $prop_value)) {
            return $reason;
        }
    }
    protected function check_callback($prop_name, $prop_value, $rule_value) {
        $reason = $rule_value($prop_value, $this);
        if (is_string($reason)) {
            return $reason;
        }
    }
    protected function check_char_size($prop_name, $prop_value, $rule_value) {
        $char_size = str_chars($prop_value);
        return self::check_size($prop_name, $rule_value, $char_size);
    }
    protected function check_byte_size($prop_name, $prop_value, $rule_value) {
        $byte_size = str_bytes($prop_value);
        return self::check_size($prop_name, $rule_value, $byte_size);
    }
    protected static function check_size($prop_name, $rule_value, $size) {
        list($min_size, $max_size, $reason) = $rule_value;
        if ($min_size === -1) {
            if ($size > $max_size) {
                return sprintf($reason, $max_size);
            }
        } else if ($max_size === -1) {
            if ($size < $min_size) {
                return sprintf($reason, $min_size);
            }
        } else {
            if ($size > $max_size || $size < $min_size) {
                return sprintf($reason, $min_size, $max_size);
            }
        }
    }
    protected $props = [];
    protected $checked_props = [];
    protected $prop_rules = [];
    protected $reasons = [];
}
// [类型] 懒惰型数据校验器
class lazy_checker extends checker {
    public function failed($prop_name, $reason) {
        $e = new check_failed();
        $e->set_reason($prop_name, $reason);
        throw $e;
    }
}
// [类型] 贪婪型数据校验器
class greedy_checker extends checker {}
// [类型] 实时型数据校验器
class instant_checker extends checker {
    public function choose(array $prop_rules) {
        foreach (array_keys($this->props) as $prop_name) {
            if (!array_key_exists($prop_name, $prop_rules)) {
                continue;
            }
            $rules = $prop_rules[$prop_name];
            $this->do_check($prop_name, $this->props, $rules);
        }
    }
}
// [类型] 校验失败时的结果
class check_failed extends \Exception {
    public function set_reason($prop_name, $reason) {
        $this->reasons[$prop_name] = $reason;
    }
    public function get_reasons() {
        return $this->reasons;
    }
    protected $reasons = [];
}
