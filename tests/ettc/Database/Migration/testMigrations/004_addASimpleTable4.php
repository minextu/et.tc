<?php namespace nexttrex\Ettc\Database\Migration;

class addASimpleTable4 extends AbstractMigration
{
	public function upgrade()
	{
		$sql = 'CREATE TABLE `simpleTable4` ( `id` INT(255) NULL )';
		return $this->db->query($sql);
	}

	public function downgrade()
	{
		$sql = 'DROP TABLE `simpleTable4`';
		return $this->db->query($sql);
	}
}
