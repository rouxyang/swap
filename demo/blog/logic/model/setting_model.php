<?php
class setting_model extends kern\model {
    const value_len = 65535;
    const id_blog_name        = 1;
    const id_blog_description = 2;
    const id_post_count       = 3;
    const id_category_count   = 4;
    const id_tag_count        = 5;
    const id_comment_count    = 6;
    const id_about            = 7;
    const id_copyright        = 8;
    const id_captcha_question = 9;
    const id_captcha_answer   = 10;
    const id_blog_keywords    = 11;
    public static $titles = array(
        self::id_blog_name        => '博客名称',
        self::id_blog_description => '博客描述',
        self::id_post_count       => '文章数',
        self::id_category_count   => '分类数',
        self::id_tag_count        => '标签数',
        self::id_comment_count    => '评论数',
        self::id_about            => '关于页',
        self::id_copyright        => '版权信息',
        self::id_captcha_question => '防止机器人而准备的问题',
        self::id_captcha_answer   => '防止机器人的问题的答案',
        self::id_blog_keywords    => '博客关键字',
    );
    public static function prop_rules($for_operation, $extra_data) {
        return [];
    }
    public static function get_about() {
        $setting = self::get_by_id(self::id_about);
        return $setting->value;
    }
}
