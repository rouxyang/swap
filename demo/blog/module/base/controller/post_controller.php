<?php
use kern\clock;
use kern\visitor;
use kern\binder;
use kern\lazy_checker;
use kern\check_failed;
class post_controller extends kern\controller {
    public static function index_action() {
        // 呈现
        $page_size = 4;
        $order_limit = array(array('id' => 'DESC'), g_int('page', 1), $page_size);
        $tag_name = g_str('tag');
        $posts_binder = function (&$posts) {
            binder::bind($posts, 'belongs_to', 'member');
            binder::bind($posts, 'belongs_to', 'category');
            binder::bind($posts, 'many_many', 'tag', array('post_tag', 0));
        };
        if ($tag_name !== '') {
            $tag = tag_model::get_one(array('name' => $tag_name));
            if ($tag === null) {
                $pager = [];
                $posts = [];
            } else {
                $post_ids = [];
                foreach (post_tag_model::get(array('tag_id' => $tag->id)) as $post_tag) {
                    $post_ids[] = (int)$post_tag->post_id;
                }
                list($pager, $posts) = post_model::pager_by_ids($post_ids, $order_limit);
                $posts_binder($posts);
                $pager['target'] = array('post/index', array('tag' => $tag_name));
            }
        } else {
            $category_id = g_int('category_id', 0);
            if ($category_id === 0) {
                list($pager, $posts) = post_model::pager_all($order_limit);
                $posts_binder($posts);
                $pager['target'] = 'post/index';
            } else {
                list($pager, $posts) = post_model::pager(array('category_id' => $category_id), $order_limit);
                $posts_binder($posts);
                $pager['target'] = 'post/index?category_id=' . $category_id;
            }
        }
        self::set('pager', $pager);
        self::set('posts', $posts);
        self::set('logined', visitor::has_role('member'));
        self::show_page('', 'four');
    }
    public static function show_action() {
        // 呈现
        $post_id = g_int('id');
        $post = post_model::get_by_id($post_id);
        self::forward_404_if($post === null, '文章不存在');
        binder::bind($post, 'belongs_to', 'member');
        binder::bind($post, 'belongs_to', 'category');
        binder::bind($post, 'many_many', 'tag', array('post_tag', 0));
        self::set('post', $post);
        $order_limit = array(array('id' => 'DESC'), g_int('page', 1), 10);
        list($pager, $comments) = comment_model::pager_with_count($post->comment_count, array('post_id' => $post_id), $order_limit);
        $pager['target'] = 'post/show?id=' . $post_id;
        self::set('pager', $pager);
        self::set('comments', $comments);
        self::set('logined', visitor::has_role('member'));
        self::set('captcha_question', setting_model::get_by_id(setting_model::id_captcha_question)->value);
        self::show_page('', 'four');
    }
    public static function edit_action() {
        // 拦截
        self::role('member');
        $post = post_model::get_by_id(g_int('id'));
        self::forward_404_if($post === null, '文章不存在');
        if (visitor::is_post()) {
            // 拦截
            self::csrf('member');
            try {
                // 校验
                $checker = new lazy_checker(p());
                $checker->check_model_rules('post');
                // 执行
                $props = $checker->get_all();
                $tags = $props['tags'];
                unset($props['tags']);
                $category_id = $props['category_id'];
                $post->add_props($props);
                publish_service::update($post, $category_id, $tags);
                // 成功
                self::json_result(true, '文章编辑成功。', 0, url('post/show?id=' . $post->id));
            } catch (check_failed $e) {
                // 失败
                self::json_result(false, $e->get_reasons());
            }
        } else {
            // 呈现
            $post->tags = publish_service::fetch_tags_from_post($post);
            self::set('post', $post);
            self::set('categories', category_model::get_all());
            self::show_page('', 'two');
        }
    }
    public static function new_action() {
        // 拦截
        self::role('member');
        // 呈现
        self::set('categories', category_model::get_all());
        self::show_page('', 'two');
    }
    public static function do_new_action() {
        // 拦截
        self::method('post');
        self::role('member');
        self::csrf('member');
        try {
            // 校验
            $checker = new lazy_checker(p());
            $checker->check_model_rules('post');
            // 执行
            $props = $checker->get_all();
            $tags = $props['tags'];
            unset($props['tags']);
            $props['member_id'] = visitor::get_role_id('member');
            $props['pub_time'] = clock::get_stamp();
            $post = new post_model();
            $post->set_props($props);
            publish_service::publish($post, $tags);
            // 成功
            self::json_result(true, '文章发表成功', 0, url('post/show?id=' . $post->id));
        } catch (check_failed $e) {
            // 失败
            self::json_result(false, $e->get_reasons());
        }
    }
    public static function delete_action() {
        // 拦截
        self::method('delete');
        self::role('member');
        self::csrf('member');
        // 校验
        // 执行
        $id = g_int('id');
        $post = post_model::get_by_id($id);
        if ($post !== null) {
            comment_model::del(array('post_id' => $id));
            category_model::dec_by_id(array('post_count' => 1), $post->category_id);
            post_model::del_by_id($id);
            publish_service::delete_tags_for_post($post);
        }
        // 成功
        self::send_json(true);
    }
}
