<?php

/**
 * Class campaign_posts
 *
 * @property integer    $id
 * @property integer    $user_id
 * @property integer    $campaign_id
 * @property string     $source_id
 */
class Campaign_posts extends DataMapper {

    var $table = 'campaign_posts';

    function __construct($id = NULL) {
        parent::__construct($id); 
    }

    public static function inst($id = NULL) {
        return new self($id);
    }
    
    public function add_new_post($user_id, $source_id, $profile_id, $source) {
        $this->source_id = $source_id;
        $this->user_id = $user_id;
        $this->profile_id = $profile_id;
        $this->source = $source;
        $this->save();
    }

    public function get_user_posts( $user_id, $source, $profile_id) {
        $where = array('user_id' => $user_id, 'profile_id' => $profile_id,  'source' => $source);
        $user_posts = $this->where($where)
            ->order_by('id', 'DESC')
            ->get(100);

        if(empty($user_posts)){
            $user_posts = array();
        }
        return $user_posts; 
    }

    public function save($object = '', $related_field = '', $set_last_check = true)
    {
        if ($set_last_check) {
            $this->last_check = time();
        }
        //d($this->to_array());
        $result = parent::save($object, $related_field);
        return $result;
    }
    
}
