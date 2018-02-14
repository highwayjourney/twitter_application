<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_242Add_features extends CI_Migration {

    private $table = 'features';

    public function up()
    {
        $data = array(
            array(
                'name' => 'PRO',
                'description' => null,
                'slug' => 'pro_package',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'PLATINIUM',
                'description' => null,
                'slug' => 'platinium_package',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'DIAMOND',
                'description' => null,
                'slug' => 'diamond_package',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'BLACK',
                'description' => null,
                'slug' => 'black_package',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'ENTERPRISE',
                'description' => null,
                'slug' => 'enterprise_package',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            )                                    
        );

        $this->db->insert_batch($this->table, $data);

    }

    public function down()
    {
    }

}