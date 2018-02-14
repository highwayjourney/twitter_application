<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class Social_trivia extends DataMapper
{
    var $validation = array();   
    var $table = 'social_trivias';
    var $has_one = array(
        'social_campaigns_item',
        ); 

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

}