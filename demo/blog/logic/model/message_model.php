<?php
class message_model extends swap\model {
    const author_len  = 16;
    const email_len   = 64;
    const site_len    = 128;
    const content_len = 65535;
    public static function prop_rules($for_operation, $extra_data) {
        return array(
            'author' => array(
                'change_to' => 'string',
                'char_size' => array(1, self::author_len, '昵称长度为%d到%d个字符'),
            ),
            'email' => array(
                'change_to'  => 'string',
                'pass_if_be' => '',
                'char_size'  => array(4, self::email_len, '邮箱长度为%d到%d个字符'),
                'value_type' => array(swap\value::email_type, '邮箱地址不合法'),
            ),
            'site' => array(
                'change_to'  => 'string',
                'pass_if_be' => '',
                'char_size'  => array(4, self::site_len, '网址长度为%d到%d个字符'),
            ),
            'content' => array(
                'change_to' => 'string',
                'char_size' => array(1, self::content_len, '内容长度为%d到%d个字符'),
            ),
        );
    }
}
