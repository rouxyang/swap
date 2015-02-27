<?php
class publish_service {
    public static function publish(post_model $post, $tags) {
        $post->save();
        category_model::inc_by_id(array('post_count' => 1), $post->category_id);
        setting_model::inc_by_id(array('value' => 1), setting_model::id_post_count);
        self::create_tags_for_post($post, $tags);
    }
    public static function update(post_model $post, $category_id, $tags) {
        $post->save();
        if ($category_id != $post->category_id) {
            category_model::dec_by_id(array('post_count' => 1), $category_id);
            category_model::inc_by_id(array('post_count' => 1), $post->category_id);
        }
        self::delete_tags_for_post($post);
        self::create_tags_for_post($post, $tags);
    }
    public static function fetch_tags_from_post(post_model $post) {
        $post_id = $post->id;
        $post_tags = post_tag_model::get(array('post_id' => $post_id));
        if ($post_tags === []) {
            return '';
        }
        $tag_ids = [];
        foreach ($post_tags as $post_tag) {
            $tag_ids[] = (int)$post_tag->tag_id;
        }
        $tags = tag_model::get_by_ids($tag_ids);
        $tag_names = [];
        foreach ($tags as $tag) {
            $tag_names[] = $tag->name;
        }
        return implode(',', $tag_names);
    }
    public static function create_tags_for_post(post_model $post, $tags) {
        if ($tags === '') {
            return;
        }
        $post_tags = [];
        $tag_names = array_map('trim', explode(',', $tags));
        $old_tags = tag_model::get_in('name', $tag_names);
        if ($old_tags === []) {
            foreach ($tag_names as $tag_name) {
                $tag_id = tag_model::add(array('name' => $tag_name, 'refer_count' => 1));
                $post_tags[] = array('post_id' => $post->id, 'tag_id' => $tag_id);
            }
            setting_model::inc_by_id(array('value' => count($tag_names)), setting_model::id_tag_count);
        } else {
            tag_model::inc_by_ids(array('refer_count' => 1), array_keys($old_tags));
            $old_tag_names = [];
            foreach ($old_tags as $old_tag) {
                $post_tags[] = array('post_id' => $post->id, 'tag_id' => $old_tag->id);
                $old_tag_names[] = $old_tag->name;
            }
            $new_tag_names = array_diff($tag_names, $old_tag_names);
            if ($new_tag_names !== []) {
                foreach ($new_tag_names as $new_tag_name) {
                    $tag_id = tag_model::add(array('name' => $new_tag_name, 'refer_count' => 1));
                    $post_tags[] = array('post_id' => $post->id, 'tag_id' => $tag_id);
                }
                setting_model::inc_by_id(array('value' => count($new_tag_names)), setting_model::id_tag_count);
            }
        }
        if ($post_tags !== []) {
            post_tag_model::add_many($post_tags);
        }
    }
    public static function delete_tags_for_post(post_model $post) {
        $post_id = $post->id;
        $post_tags = post_tag_model::get(array('post_id' => $post_id));
        if ($post_tags === []) {
            return;
        }
        $tag_ids = [];
        foreach ($post_tags as $post_tag) {
            $tag_ids[] = (int)$post_tag->tag_id;
        }
        post_tag_model::del(array('post_id' => $post_id));
        tag_model::dec_by_ids(array('refer_count' => 1), $tag_ids);
        tag_model::del(array('refer_count' => 0));
    }
}
