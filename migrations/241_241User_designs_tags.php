<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_241User_designs_tags  extends CI_Migration {

    private $_table = 'user_designs_tags';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_design_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '14',
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