<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Twitter_retweet extends CI_Migration {

    private $_table = 'twitter_retweets';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ),
            'tweet_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'last_check' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),            
            'start_retweet_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'end_retweet_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'need_retweet' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),            
            'access_token_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
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