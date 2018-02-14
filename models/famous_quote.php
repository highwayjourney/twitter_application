<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class Famous_quote extends DataMapper
{
    var $has_one = array();
    var $has_many = array();

    var $validation = array();

    var $table = 'famous_quotes';
    
    public function __construct($id = null)
    {
        parent::__construct($id);
    }
}
