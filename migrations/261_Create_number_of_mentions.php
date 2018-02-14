<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_number_of_mentions  extends CI_Migration {

    private $_table = 'number_of_mentions_twitter';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'count' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            ),
            'date' => array(
                'type' => 'DATE',
            ),
            'token_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
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