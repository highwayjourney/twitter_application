<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class Current_list extends DataMapper
{

    var $has_one = array(
        'user',
        'social_group'
    );


    var $table = 'current_lists';

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    public function get_list($list_id, $social_group_id, $user_id){
        $list = $this->where(array('list_id' => $list_id, 'social_group_id' => $social_group_id, 'user_id' => $user_id))->count();
        return empty($list)?false:$list;
    }

    public function toogle_show(){
        if($this->show){
            $this->show = 0;
        } else {
            $this->show = 1;
        }
        $this->save();
    }

    public function add_new($post, $user_id, $social_group_id){
        $data = $this->get_list($post['id'], $user_id, $social_group_id)?$this->get_list($post['id'], $user_id, $social_group_id): new self;
        foreach ($post as $key => $value) {
            if($key == 'id'){
                $data->list_id = $value;
            } elseif($key == 'show') {
                $data->key = $value == 'true'?1:0;
            } else {
                $data->$key = $value;
            }
        }
        $data->user_id = $user_id;
        $data->show = 1;
        $data->social_group_id = $social_group_id;
        $data->save();
        $id = $data->id;
        if(empty($id)){
            $id = $this->db->insert_id();
        }
        return $id;        
    }
}
