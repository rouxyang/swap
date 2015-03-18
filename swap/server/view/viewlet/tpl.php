<?php
use kern\router;
function show_text($text, $escape_html = false) {
    echo nl2br(str_replace(' ', '&nbsp;', $escape_html ? kern\html::escape($text) : $text));
}
function show_ubb($text, $escape_html = false) {
    $text = str_replace(' ', '&nbsp;', $escape_html ? kern\html::escape($text) : $text);
    $text = nl2br($text);
    $text = preg_replace_callback('/\[upload_img\](.*?)\[\/upload_img\]/', function (array $matches) {
        return '<img src="' . kern\rendor::upload_url($matches[1], false) . '">';
    }, $text);
    echo $text;
}
function link_to($target, $text) {
    echo '<a href="' . router::build_php_url($target, false, true) . '">' . $text . '</a>';
}
