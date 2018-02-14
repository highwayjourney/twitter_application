<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_235Add_configs extends CI_Migration {

    private $_table = 'configs';

    public function up() {
        $data = array(
            array(
                'name' => 'Smart Engage',
                'key' => 'smart_engage',
            ),
            array(
                'name' => 'Smart Mention',
                'key' => 'smart_mention',
            )                                   
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $this->db->delete($this->_table, array('key' => 'piwik_site_id'));
    }

}