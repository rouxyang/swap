<?php
class link_model extends swap\model {
    const name_len = 16;
    const url_len  = 255;
    public static function prop_rules($for_operation, $extra_data) {
        return array(
            'name' => array(
                'change_to' => 'string',
                'char_size' => array(1, self::name_len, '名称长度为%d到%d个字符'),
            ),
            'url' => array(
                'change_to' => 'string',
                'char_size' => array(1, self::url_len, '地址长度为%d到%d个字符'),
            ),
        );
    }
}
