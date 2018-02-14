<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class Suggested_list extends DataMapper
{

    var $has_one = array(
        'user',
        'social_group'
    );


    var $table = 'suggested_lists';

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function to_db($data, $user_id, $social_group_id){
        $current = $this->where(array('user_id' => $user_id, 'social_group_id' => $social_group_id))->count();
        if($current){
            //var_dump($data);
            $this->where(array('user_id' => $user_id, 'social_group_id' => $social_group_id))->update([
                    'data' => serialize($data)
                ]);
        } else {
            $this->data = serialize($data);
            $this->show = true;
            $this->user_id = $user_id;
            $this->social_group_id = $social_group_id;
            $this->save();
        }        
    }

}
