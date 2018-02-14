<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_253add_twitter_language extends CI_Migration {

    private $_table = 'twitter_languages';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'code' => array(
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
            ), 
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => TRUE,
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