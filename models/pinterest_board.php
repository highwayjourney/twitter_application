<?php

class Pinterest_board extends DataMapper {


    var $table = 'pinterest_boards';

    var $has_one = array(
    );
    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * Used to add new record in Pinterest Boards table
     * 
     *
     * @access public
     *
     * @param      $user_id
     * @param      $board_id
     * @param      $access_token_id
     */
    public function save_selected_board($user_id, $board_id, $access_token_id = null) {
        $page = $this->where(array('user_id' => $user_id, 'access_token_id' => $access_token_id))
            ->get();
        //ddd($user_id, $board_id, $board_name, $access_token_id );
        $page->user_id = $user_id;
        $page->board_id = $board_id;
        $page->access_token_id = $access_token_id;
        $page->save();
    }

    /**
     * Used to get current user main Board
     *
     * @access public
     * @param $user_id
     * @return DataMapper
     */
    public function get_selected_board($user_id, $access_token_id) {
        $page = $this->where(array(
            'user_id' => $user_id,
            'access_token_id' => $access_token_id
        ))->get();
        return $page;
    }

}