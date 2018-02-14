<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_238Add_api_keys_pinterest  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

       $data = array(
            array('social' => 'pinterest', 'key' => 'app_id', 'name' => 'App ID'),
            array('social' => 'pinterest', 'key' => 'app_secret', 'name' => 'App Secret'),
            array('social' => 'pixalbay', 'key' => 'api_key', 'name' => 'Api Key')
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        //$this->db->delete($this->_table, array('social' => 'pinterest', 'key' => 'token'));
    }

}