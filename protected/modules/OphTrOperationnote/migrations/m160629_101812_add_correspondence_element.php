<?php

class m160629_101812_add_correspondence_element extends OEMigration
{
	public function up()
	{
		$event_type_id = $this->dbConnection->createCommand()->select("id")->from("event_type")->where("class_name = :class_name", array(":class_name" => "OphTrOperationnote"))->queryScalar();

		$element_types = array(
			'Element_OphTrOperationnote_Correspondence' =>
				array('name' => 'Correspondence', 'parent_element_type_id' => NULL, 'display_order'=>70, 'default'=>1),
		);
		$this->insertOEElementType($element_types, $event_type_id);

		$this->createOETable('et_ophtroperationnote_correspondence',
			array(	'id'=>'pk',
					'event_id'=>'int(10) unsigned',
					'address_target'=>'int(10) unsigned',
					'macro_id'=>'int(10) unsigned',
					'correspondence_id'=>'int(10) unsigned'), true);
	}

	public function down()
	{
		$this->delete('element_type', 'class_name = :class',
			array(':class'=>'Element_OphTrOperationnote_Correspondence'));
		$this->dropTable('et_ophtroperationnote_correspondence_version');
		$this->dropTable('et_ophtroperationnote_correspondence');
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