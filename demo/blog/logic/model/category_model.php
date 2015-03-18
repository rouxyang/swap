<?php
class category_model extends kern\model {
    const name_len = 64;
    const default_id = 1;
    public static function prop_rules($for_operation, $extra_data) {
        return array(
            'name' => array(
                'change_to' => 'string',
                'char_size' => array(1, self::name_len, '分类名为%d到%d个字符'),
            ),
        );
    }
    public function can_be_deleted() {
        if (!isset($this->id)) {
            return true;
        }
        return $this->id !== self::default_id;
    }
    public function has_posts() {
        return $this->post_count > 0;
    }
}
