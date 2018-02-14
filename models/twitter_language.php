<?php

/**
 * Twitter Languages
 *
 * @author cquintini
 */
class Twitter_language extends DataMapper {

    var $table = 'twitter_languages';

    var $has_one = array(
    );
    var $has_many = array(
    );

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }
    
    public static function inst($id = NULL) {
        return new self($id);
    }

    public  function update($data) {
        $this->db->empty_table('twitter_languages');
        foreach ($data as $value) {
            $instance = new self();
            $instance->code = $value->code;
            $instance->name = $value->name;
            $instance->status = $value->status;
            $instance->save();
        }
    }
    public function getAll(){
        return $this->get()->all_to_array();
    }
}