<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_224Update_social_posts extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'campaign_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'default' => null
            ),            
            'post_cron_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => 8,
                'default' => null
            ),
            'campaign_data' => array(
                'type' => 'BLOB',
                'default' => null
            )
            'url_text' => array(
                'type' => 'VARCHAR',
                'constraint' => 1024,
                'default' => null
            ), 
            'disabled' => array(
                'type' => 'TINYINT',
                'unsigned' => TRUE,
                'null' => TRUE
            ),            
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'post_cron_id');
    }

}