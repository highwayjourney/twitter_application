<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Twitter_favourite extends CI_Migration {

    private $_table = 'twitter_favourites';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ),
            'favourite_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'last_check' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),            
            'start_favourite_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'end_favourite_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'need_favourite' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),            
            'access_token_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            )            
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}