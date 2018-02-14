<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Famous_quotes  extends CI_Migration {

    private $_table = 'famous_quotes';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'a2z' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'lastname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'author' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'profession' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'nationality' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'birthday' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'deathday' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'quote' => array(
                'type' => 'TEXT',
            ),
            'keywords' => array(
                'type' => 'VARCHAR',
                'constraint' => '250',
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