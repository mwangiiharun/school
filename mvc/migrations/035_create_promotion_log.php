<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_promotion_log extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'promotionLogID' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'classesID' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => FALSE
			),
			'jumpClassID' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => FALSE
			),
			'schoolyearID' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => FALSE
			),
			'subjectandsubjectcodeandmark' => array(
				'type' => 'LONGTEXT',
				'null' => FALSE
			),
			'exams' => array(
				'type' => 'LONGTEXT',
				'null' => FALSE
			),
			'markpercentages' => array(
				'type' => 'LONGTEXT',
				'null' => FALSE
			),
			'status' => array(
				'type' => 'INT',
				'constraint' => '11',
				'default' => 0,
				'null' => FALSE
			),
			'create_at' => array(
				'type' => 'TIMESTAMP',
				'constant' => 'CURRENT_TIMESTAMP'
			),
			'create_userID' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => FALSE
			)
		));
		$this->dbforge->add_key('promotionLogID', TRUE);
		$this->dbforge->create_table('promotionlog');
	}

	public function down()
	{
		$this->dbforge->drop_table('promotionlog');
	}

}

/* End of file 035_create_promotion_log.php */
