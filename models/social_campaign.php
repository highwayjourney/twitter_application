<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class Social_campaign extends DataMapper
{
    var $validation = array(
        array(
            'field' => 'type',
            'label' => 'Type',
            'rules' => array('required', 'min_length' => 2)
        )
    );
    var $created_field = 'created';
    var $updated_field = 'updated';
    var $auto_populate_has_many = TRUE;

    var $has_one = array(
        'user',
    );
    
    var $has_many = array(
        'social_campaigns_item'
    );


    var $table = 'social_campaigns';

    /**
     * Initialize social_campaign model
     *
     * @access public
     *
     * @param $id (int)      - id
     * @param $user_id (int) - user id
     * @param $name (string) - campaign name
     *
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function get_user_campaigns($user_id, $profile_id, $type = 'social'){
        return $this->where(array('user_id' => $user_id, 'profile_id' => $profile_id, 'type' => $type, 'status' => 'enabled'))->order_by('id','desc')->get();
    }

    public function create_campaign($user_id,$name){
        $campaign = new Social_campaign();
        $campaign->user_id = $user_id;
        $campaign->name = $name;
        $campaign->save();
        return $campaign->id;

        /*$this->insert(array(
                'user_id' => $user_id,
                'name' => $name
            ));
        return $this->insert_id();*/
    }
    private function is_enabled(){
        if($this->enable == "checked"){
            return true;
        } else {
            return false;
        }
    }
    // public function save(){
    //     if(empty($this->type) || $this->type == 0){
    //         continue;
    //     } else {
    //         parent::save($this);
    //     }
    // }
}
