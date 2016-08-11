<?php

class m160811_135634_add_evd_shortcode extends CDbMigration
{
	public function up()
	{
		$this->insert('patient_shortcode', array('default_code' => 'evd', 'code' => 'evd', 'description' => 'Get date of the most recent event for the patient.'));
	}

	public function down()
	{
		$this->delete('patient_shortcode', array('code' => 'evd'));
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}