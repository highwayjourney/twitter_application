<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Social_campaigns_items  extends CI_Migration {

    private $_table = 'social_campaigns_items';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'social_campaign_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
            'background_image' => array(
                'type' => 'VARCHAR',
                'constraint' => '250',
            ),
            'final_image' => array(
                'type' => 'VARCHAR',
                'constraint' => '250',
            ),
            'final_image_thumb' => array(
                'type' => 'VARCHAR',
                'constraint' => '250',
            ),
            'quote' => array(
                'type' => 'TEXT'
            ),
            'schedule_date' => array(
                'type' => 'INT'
            ),
            'created' => array(
                'type' => 'INT'
            ),
            'posted' => array(
                'type' => 'TINYINT',
                'null' => TRUE
            ),
            'updated' => array(
                'type' => 'INT'
            ),
            'url' => array(
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => TRUE,
            ), 
            'url_text' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => TRUE,
            ),
            'post_text' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => TRUE,
            ),                       
            'post_to_socials' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            ),
            'post_to_groups' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            ),
            'social_trivia_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
            'external_url' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
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