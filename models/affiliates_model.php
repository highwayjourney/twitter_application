<?php

/**
 * Class Affiliates_model
 *
 * @property integer    $id
 * @property integer    $user_id
 * @property integer    $profile_id
 * @property string     $source
 * @property string     $data
 */
class Affiliates_model extends DataMapper {

    var $table = 'affiliates_info';

    function __construct($id = NULL) {
        parent::__construct($id);    
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    public function get_affiliate_info($user_id, $profile_id) {
        $where = array(
            'user_id' => $user_id,
            'profile_id' => $profile_id
        );

        $data = $this->where($where)
            ->order_by('id', 'DESC')
            ->get();
        $_data = $data = unserialize($data->data);
        return $_data;
    }

    public function todb($user_id, $profile_id, $feeds){
        $current = $this->get_affiliate_info($user_id, $profile_id);
        $data = isset($current->id) ? new self((int)$feeds->id) : new self;
        $data->data = serialize($feeds);
        $data->user_id = $user_id;
        $data->profile_id = $profile_id;

        if($data->save()){
            if(empty($feeds->id)){
                $data->id = $this->db->insert_id();
            } 
            return $feeds;
        } else {
            return array("error" => "Something bad Happened");
        }

        
    }
    public function _delete( $id, $user_id, $profile_id ) {
        $data = $this->where(array('id' => $id, 'user_id' => $user_id, 'profile_id' => $profile_id))
            ->get();
        if( $data->result_count() > 0 ) {
           return $data->delete();
        } else {
            return true;
        }
    }

}
