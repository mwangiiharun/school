<?php

class Migration_create_markrelation extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'markrelationID' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'markID' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => FAlSE
			),
			'markpercentageID' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => FAlSE
			),
			'mark' => array(
				'type' => 'VARCHAR',
				'constraint' => 128,
				'null' => TRUE
			)
		));
		$this->dbforge->add_key('markrelationID', TRUE);
		$this->dbforge->create_table('markrelation');
	}

	public function down()
	{
		$this->dbforge->drop_table('markrelation');
	}
}
