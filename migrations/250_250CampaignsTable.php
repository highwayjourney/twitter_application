<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_250CampaignsTable extends CI_Migration {

    private $_table = 'campaigns';

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
                'unsigned' => TRUE
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => TRUE,
            ),
            'name' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),            
            'sources' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),      
            'keywords' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),   
            'priority' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),   
            'url' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),   
            'text' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),                           
            'updated' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ), 
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => 7,
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