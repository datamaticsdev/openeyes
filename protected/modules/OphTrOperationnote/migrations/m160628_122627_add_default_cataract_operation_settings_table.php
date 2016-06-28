<?php

class m160628_122627_add_default_cataract_operation_settings_table extends OEMigration
{
	public function up()
	{
		$this->createOETable('ophtroperationnote_cataract_defaults', array(
			'id' => 'pk',
			'user_id' => 'int(10) unsigned NOT NULL',
			'firm_id' => 'int(10) unsigned NOT NULL',
			'defaults' => 'text'
		), true);
		$this->addForeignKey('cataract_defaults_user_fk','ophtroperationnote_cataract_defaults','user_id','user','id');
		$this->addForeignKey('cataract_defaults_firm_fk','ophtroperationnote_cataract_defaults','firm_id','firm','id');
	}

	public function down()
	{
		$this->dropTable('ophtroperationnote_cataract_defaults');
		$this->dropTable('ophtroperationnote_cataract_defaults_version');
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