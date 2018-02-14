<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_rss_feed_industries  extends CI_Migration {

    private $_table = 'rss_feed_industries';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',    
                'constraint' => '100',
                'null' => TRUE,
            ),
            'industry_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $sql = "CREATE UNIQUE INDEX rss_feed_industries_name_industry_id_UNIQUE ON " . $this->db->dbprefix 
            . $this->_table . "(name ASC, industry_id ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);

        $sql = "DROP INDEX rss_feed_industries_name_industry_id_UNIQUE ON " . $this->db->dbprefix . 
            $this->_table . ";";
        $this->db->query($sql);
    }

}