<?php

class m160705_120830_proc_repetition extends CDbMigration
{
	public function up()
	{
		$this->addColumn('proc', 'once_only', 'boolean default false');
		$this->addColumn('proc_version', 'once_only', 'boolean default false');
		$this->addColumn('proc', 'repeats', 'boolean default false');
		$this->addColumn('proc_version', 'repeats', 'boolean default false');
	}

	public function down()
	{
		$this->dropColumn('proc_version', 'repeats');
		$this->dropColumn('proc', 'repeats');
		$this->dropColumn('proc_version', 'once_only');
		$this->dropColumn('proc', 'once_only');
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