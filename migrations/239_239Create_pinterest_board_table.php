<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_239Create_pinterest_board_table  extends CI_Migration {

    private $_table = 'pinterest_boards';

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
            'access_token_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),            
            'board_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
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