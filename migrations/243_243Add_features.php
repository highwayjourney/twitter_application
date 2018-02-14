<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_243Add_features extends CI_Migration {

    private $table = 'features';

    public function up()
    {
        $data = array(
            array(
                'name' => 'MiniGif Creator',
                'description' => null,
                'slug' => 'minigif_creator',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Monetize',
                'description' => null,
                'slug' => 'monetize',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Social Campaigns',
                'description' => null,
                'slug' => 'social_campaign',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),                                     
        );

        $this->db->insert_batch($this->table, $data);

    }

    public function down()
    {
    }

}