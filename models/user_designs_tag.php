<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class User_designs_tag extends DataMapper
{

    var $has_one = array(
        'user_design'
    );
    var $has_many = array();

    var $validation = array();

    var $table = 'user_designs_tags';

    /**
     * Initialize user_design model
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

    public function newEntry($name){
        $this->name = $name;
        $this->save();        
    }

    public static function getUserTags($user_id) {
        $designs= new User_design();
        $designs->where('user_id', $user_id)->get();
        $result = [];
        foreach ($designs as  $design) {
            $design->user_designs_tag->get_iterated();
            foreach ($design->user_designs_tag as $u)
            {
                $result[] = $u->to_array()['name'];
            }     
        }   
        return array_unique($result);     
    }

}
