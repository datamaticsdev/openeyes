<?php

class m160810_152254_rename_event extends CDbMigration
{
    public function up()
    {
        $this->update('event_type', array('name' => 'Treatment note'), 'class_name = "OphTrOperationnote"');
    }

    public function down()
    {
        $this->update('event_type', array('name' => 'Operation note'), 'class_name = "OphTrOperationnote"');
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