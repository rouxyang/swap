<?php
class post_model extends kern\model {
    const title_len = 64;
    const content_len = 65535;
    public static function prop_rules($for_operation, $extra_data) {
        return array(
            'category_id' => array(
                'change_to' => 'int',
                'should_in' => array(array_keys(category_model::get_all()), ''),
            ),
            'title' => array(
                'change_to' => 'string',
                'char_size' => array(3, self::title_len, '标题长度必须为%d到%d个字符'),
            ),
            'content' => array(
                'change_to' => 'string',
                'char_size' => array(2, self::content_len, '内容长度必须为%d到%d个字符'),
            ),
            'tags' => array(
                'change_to' => 'string',
                'callback' => function ($tags) {
                    $tags = array_map('trim', explode(',', $tags));
                    foreach ($tags as $tag) {
                        if (str_chars($tag) > tag_model::name_len) {
                            return '标签太长';
                        }
                    }
                },
            ),
        );
    }
}
