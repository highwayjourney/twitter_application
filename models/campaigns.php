<?php

/**
 * Class Campaign
 *
 * @property integer    $id
 * @property integer    $user_id
 * @property string     $status
 * @property string     $name
 * @property string     $sources
 * @property string     $keywords
 * @property integer    $periodicity
 * @property string     $timezone
 * @property integer     $last_run
 * @property string     $type
 * @property integer    $profile_id
 */
class Campaigns extends DataMapper {

    var $table = 'campaigns';
    // var $created_field = 'created';
    var $updated_field = 'updated';
    //var $auto_populate_has_many = TRUE;

    var $has_one = array(
        'user',
    );
    
    // var $has_many = array(
    //     'social_campaigns_item'
    // );


    function __construct($id = NULL) {
        parent::__construct($id);    
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    public function get_user_campaigns( $user_id, $profile_id, $type = 'social', $status= 'enabled') {
        if($status == 'all'){
            $where = array(
                'user_id' => $user_id,
                'profile_id' => $profile_id,
                'type'=>    $type
            );
        } else{
            $where = array(
                'user_id' => $user_id,
                'profile_id' => $profile_id,
                'type'=>    $type,
                'status' => $status
            );
        }
        $campaigns = $this->where($where)
            ->order_by('id', 'DESC')
            ->get();
        return $campaigns;
    }

    public function todb($feeds){

        $campaign = isset($feeds->id) ? new self((int)$feeds->id) : new self;
        $campaign->status = $feeds->status;
        $campaign->user_id = $feeds->user_id;
        $campaign->name = $feeds->name;
        $campaign->keywords = $feeds->keywords;
        $campaign->sources = $feeds->sources;
// if($_SERVER['REMOTE_ADDR'] == '186.89.150.214'){
//  var_dump($feeds->url);
// }         
        $campaign->url = $feeds->urls;
        $campaign->priority = $feeds->priority;
        $campaign->timezone = $feeds->timezone;
        $campaign->profile_id = $feeds->profile_id;
        $campaign->status = $feeds->status;
        $campaign->type = $feeds->type;
        if($campaign->save()){
            if(empty($feeds->id)){
                $feeds->id = $this->db->insert_id();
            } 
            return $feeds;
        } else {
            return array("error" => "Something bad Happened");
        }

        
    }
    public function _delete( $campaign_id, $user_id ) {
        $campaign = $this->where(array('id' => $campaign_id, 'user_id' => $user_id))
            ->get();
        if( $campaign->result_count() > 0 ) {
           return $campaign->delete();
        } else {
            return true;
        }
    }

}
