<?php

class Twitter_api_key extends DataMapper {

    var $table = 'twitter_api_key';

    var $has_one = array();
    var $has_many = array();
    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    public static function value($user_id, $key = NULL) {
        $rows = self::inst()->where('user_id', $user_id);
        if ($key) {
            return $rows->where('key', $key)->get(1)->value;
        }
        $result = array();
        foreach ($rows->get() as $row) {
            $result[$row->key] = $row->value;
        }
        return $result;
    } 

    public static function build_config($user_id, $from_file = array()) {
        $from_database = self::value($user_id);
        return array_merge($from_file, $from_database);
    }

    public static function has_empty() {
        return self::inst()
           ->where('value IS NULL')
           ->count() > 0;
    }

}