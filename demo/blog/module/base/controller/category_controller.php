<?php
use kern\clock;
use kern\visitor;
use kern\lazy_checker;
use kern\check_failed;
class category_controller extends kern\controller {
    public static $layout = 'two';
    public static function index_action() {
        // 拦截
        self::role('member');
        // 呈现
        self::set('categories', category_model::get_all(array(array('id' => 'DESC'), 0, 0)));
        self::show_page();
    }
    public static function new_action() {
        // 拦截
        self::method('post');
        self::role('member');
        self::csrf('member');
        try {
            // 校验
            $checker = new lazy_checker(p());
            $checker->check_model_rules('category');
            // 执行
            category_model::add(array('name' => $checker->name, 'post_count' => 0));
            // 成功
            self::json_result(true, '分类创建成功。', 0, url('category/index'));
        } catch (check_failed $e) {
            // 失败
            self::json_result(false, $e->get_reasons());
        }
    }
    public static function edit_action() {
        // 拦截
        self::role('member');
        $category = category_model::get_by_id(g_int('id'));
        self::forward_404_if($category === null, '分类不存在');
        if (visitor::is_post()) {
            // 拦截
            self::csrf('member');
            try {
                // 校验
                $checker = new lazy_checker(p());
                $checker->check_model_rules('category');
                // 执行
                $category->name = $checker->name;
                $category->save();
                // 成功
                self::json_result(true, '分类编辑成功。', 0, url('category/index'));
            } catch (check_failed $e) {
                // 失败
                self::json_result(false, $e->get_reasons());
            }
        } else {
            // 呈现
            self::set('category', $category);
            self::show_page();
        }
    }
    public static function delete_action() {
        // 拦截
        self::method('delete');
        self::role('member');
        self::csrf('member');
        // 校验
        // 执行
        self::send_json(category_service::delete(g_int('id')));
    }
}
