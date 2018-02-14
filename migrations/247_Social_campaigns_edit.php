<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Social_campaigns_edit extends CI_Migration {

    private $table = 'social_campaigns';

    public function up()
    {
        $fields = array(
            'post_to_socials' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            ),
            'post_to_groups' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            ),
            'font' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'url' => array(
                'type' => 'BLOB',
                'null' => TRUE
            ), 
            'url_text' => array(
                'type' => 'BLOB',
                'null' => TRUE
            ),
            'post_text' => array(
                'type' => 'BLOB',
                'null' => TRUE
            ), 
            'enable' => array(
                'type' => 'VARCHAR',
                'constraint' => '7',
                'null' => TRUE,
            ), 
            'quote_position' => array(
                'type' => 'VARCHAR',
                'constraint' => '6',
                'null' => TRUE,
            ), 
            'logo' => array(
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => TRUE,
            )                                                   
        );

        $this->dbforge->add_column($this->table, $fields);

    }

    public function down()
    {
    }

}