<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class AddProceduresToEventInfoCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'Add procedure names to event info Command.';
	}

	public function getHelp()
	{
		return "A simple script that will iterate through all booking events and set the info attribute to ensure\n
		procedures are correctly listed.\n";
	}

	public function run($args)
	{
		$event_type = EventType::model()->find('class_name=?', array("OphTrOperationbooking"));
		$count = 0;

		$criteria = new CDbCriteria();
		$criteria->addCondition('event_type_id = :eid');
		$criteria->params[':eid'] = $event_type->id;

		foreach (Event::model()->findAll($criteria) as $event) {
			if ($op_el = Element_OphTrOperationbooking_Operation::model()->find('event_id = ?', array($event->id))) {
				$info = $op_el->infotext;
				if ($event->info != $info) {
					$event->info = $info;
					$event->save();
					$count++;
				}
			}
		}

		echo $count . " updated\n";
	}
}