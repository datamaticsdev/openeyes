<?php

class m160816_191200_remove_required_eye_for_procedure extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophtrconsent_procedure', 'eye_id', 'int(10) unsigned');
		$this->alterColumn('et_ophtrconsent_procedure_version', 'eye_id', 'int(10) unsigned');
	}

	public function down()
	{
		$this->alterColumn('et_ophtrconsent_procedure', 'eye_id', 'int(10) unsigned NOT NULL');
		$this->alterColumn('et_ophtrconsent_procedure_version', 'eye_id', 'int(10) unsigned NOT NULL');
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