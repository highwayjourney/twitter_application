<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_social_sent_posts  extends CI_Migration {

    private $_table = 'social_sent_posts';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'access_token_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'source_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ),    
            'date' => array(
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