<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Social_campaigns  extends CI_Migration {

    private $_table = 'social_campaigns';

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
            ),
            'profile_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),            
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'category' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'timezone' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),            
            'start_from' => array(
                'type' => 'INT',
            ),
            'interval' => array(
                'type' => 'INT',
                'default' => '120',
            ),
            'image_count' => array(
                'type' => 'INT',
                'default' => '1',
            ),
            'custom_quote' => array(
                'type' => 'VARCHAR',
                'constraint' => 512
            ),
            'data_url' => array(
                'type' => 'TEXT'
            ),
            'created' => array(
                'type' => 'INT'
            ),
            'updated' => array(
                'type' => 'INT'
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