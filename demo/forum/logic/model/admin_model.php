<?php
class admin_model extends kern\model {
    const name_len = 16;
    public static function get_crypted_pass($pass, $salt) {
        return sha1($pass . $salt);
    }
    public function is_valid_pass($pass) {
        return $this->pass === self::get_crypted_pass($pass, $this->salt);
    }
    public function change_pass_to($pass) {
        $this->pass = self::get_crypted_pass($pass, $this->salt);
    }
}
