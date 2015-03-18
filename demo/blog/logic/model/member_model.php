<?php
class member_model extends kern\model {
    const name_len = 16;
    public static function prop_rules($for_operation, $extra_data) {
        $member = $extra_data;
        $rules = array(
            'name' => array(
                'change_to' => 'string',
                'char_size' => array(3, self::name_len, '昵称长度：%d-%d'),
                'callback' => $for_operation === 'new' ? function ($name) {
                    if (member_model::get_one(array('name' => $name)) !== null) {
                        return '该用户已经存在';
                    }
                } : function ($name) use ($member) {
                    if ($name !== $member->name && member_model::get_one(array('name' => $name)) !== null) {
                        return '该用户已经存在';
                    }
                },
            ),
            'pass' => array(
                'change_to' => 'string',
                'char_size' => array(6, -1, '密码不能小于%d个字符'),
            ),
            'repass' => array(
                'same_as' => array('pass', '两次输入的密码不相同'),
            ),
        );
        if ($for_operation === 'edit') {
            $rules['pass']['pass_if_be'] = '';
        }
        return $rules;
    }
    public static function generate_pass($pass, $salt) {
        return sha1($pass . $salt);
    }
    public function set_pass($pass) {
        $this->pass = self::generate_pass($pass, $this->salt);
    }
    public function is_valid_pass($pass) {
        return $this->pass === self::generate_pass($pass, $this->salt);
    }
}
