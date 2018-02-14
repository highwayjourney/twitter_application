<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_current_lists  extends CI_Migration {

    private $_table = 'current_lists';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                // 'auto_increment' => TRUE
            ),
            'list_id' => array(
                'type' => 'BIGINT',
                'null' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
            'social_group_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),            
            'member_count' => array(
                'type' => 'INT',
                'null' => TRUE
            ),            
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 125
            ),            
            'show' => array(
                'type' => 'BOOLEAN',
                'null' => TRUE
            ),            
            'subscriber_count' => array(
                'type' => 'INT',
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