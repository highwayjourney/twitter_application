<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class User_design extends DataMapper
{

    var $created_field = 'created';
    var $updated_field = 'updated';

    var $has_one = array(
        'user'
    );
    var $has_many = array(
        'user_designs_tag'
    );

    var $validation = array();

    var $table = 'user_designs';

    /**
     * Initialize user_designs model
     *
     * @access public
     *
     * @param $id (int) - user id
     *
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function newEntry($nameJson, $user_id, $type){
        $this->name = $nameJson;
        $this->user_id = $user_id;
        $this->type = $type;
        $this->save();  
        return $this->id;      
    }

    public function customDelete($id, $user_id){
        $user_designs_tag = new User_designs_tag();
        $user_designs_tag->where('user_design_id', $id)->get();
        foreach ($user_designs_tag as $tag) {
            $tag->delete();
        }
        if($this->where(array('id' => $id, 'user_id' => $user_id))->get()->delete()){
            return true;
        } else {
            return false;
        }
    }
    /**
     * @param string $object
     * @param string $related_field
     * @param bool $set_last_check
     * @return bool
     */
    // public function save($object = '', $related_field = '', $set_last_check = true)
    // {
    //     if ($set_last_check) {
    //         $this->last_check = time();
    //     }
    //     $result = parent::save($object, $related_field);
    //     return $result;
    // }
}
