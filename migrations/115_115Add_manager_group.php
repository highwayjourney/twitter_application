<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_115Add_manager_group extends CI_Migration {

	public function up()
	{
		// Dumping data for table 'groups'
		$data = array(
			array(
				'id' => '3',
				'name' => 'managers',
				'description' => 'Account manager'
			)
		);
		$this->db->insert_batch('groups', $data);

	}

	public function down()
	{
		$this->db->query('DELETE FROM '.$this->db->dbprefix .'groups WHERE id = 3');
	}
}
