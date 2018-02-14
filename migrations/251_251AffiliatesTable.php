<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_251AffiliatesTable extends CI_Migration {
  
  private $_table = 'affiliates_info';

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
            'profile_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),            
            'source' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),         
            'data' => array(
                'type' => 'TEXT',
                'null' => TRUE
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