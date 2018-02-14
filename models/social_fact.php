<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class Social_fact extends DataMapper
{
    var $validation = array();   
    var $table = 'social_facts';


    public function __construct($id = null)
    {
        parent::__construct($id);
    }

}