<?php
// 本文件由框架自动加载，可以在 Web 应用的所有地方使用
use swap\visitor;
use swap\router;
use swap\target;
use swap\html;
use swap\clock;
function puts($str) {
    visitor::add_content($str);
}
function g($key = '', $default = null) {
    return visitor::g($key, $default);
}
function g_has($key) {
    return visitor::g_has($key);
}
function g_int($key, $default = 0) {
    return visitor::g_int($key, $default);
}
function g_str($key, $default = '') {
    return visitor::g_str($key, $default);
}
function g_arr($key, $default = []) {
    return visitor::g_arr($key, $default);
}
function p($key = '', $default = null) {
    return visitor::p($key, $default);
}
function p_has($key) {
    return visitor::p_has($key);
}
function p_int($key, $default = 0) {
    return visitor::p_int($key, $default);
}
function p_str($key, $default = '') {
    return visitor::p_str($key, $default);
}
function p_arr($key, $default = []) {
    return visitor::p_arr($key, $default);
}
function c($key = '', $default = null) {
    return visitor::c($key, $default);
}
function c_has($key) {
    return visitor::c_has($key);
}
function c_int($key, $default = 0) {
    return visitor::c_int($key, $default);
}
function c_str($key, $default = '') {
    return visitor::c_str($key, $default);
}
function c_arr($key, $default = []) {
    return visitor::c_arr($key, $default);
}
function f($key = '', $default = null) {
    return visitor::f($key, $default);
}
function f_has($key) {
    return visitor::f_has($key);
}
function url($target, $for_html = false) {
    return router::build_php_url($target, false, $for_html);
}
function uri($target, $for_html = false) {
    return router::build_php_url($target, false, $for_html, false);
}
function csrf_url($csrf_role, $target, $for_html = false) {
    return router::build_csrf_url($csrf_role, $target, false, $for_html);
}
function csrf_uri($csrf_role, $target, $for_html = false) {
    return router::build_csrf_url($csrf_role, $target, false, $for_html, false);
}
function h($value) {
    return html::escape($value);
}
function r($value) {
    return html::unescape($value);
}
function str_bytes($str) {
    return swap\str_bytes($str);
}
function str_chars($str, $encoding = 'UTF-8') {
    return swap\str_chars($str, $encoding);
}
function str_units($str, $encoding = 'UTF-8') {
    return swap\str_units($str, $encoding);
}
function str_sub($str, $begin, $length, $encoding = 'UTF-8') {
    return swap\str_sub($str, $begin, $length, $encoding);
}
function random_sha1() {
    return swap\random_sha1();
}
function random_md5() {
    return md5(random_sha1());
}
function timezone() {
    return clock::get_timezone();
}
