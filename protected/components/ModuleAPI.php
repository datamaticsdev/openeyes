<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class ModuleAPI extends CApplicationComponent
{
	public function get($moduleName)
	{
		if ($module = Yii::app()->getModule($moduleName)) {
			if ($et = EventType::model()->find('class_name = ?', array($moduleName))) {
				// if the module has been inherited from, and has its own API, should return that instead
				if ($child = EventType::model()->find('parent_event_type_id = ?', array($et->id))) {
					if ($child_api = self::get($child->class_name)) {
						return $child_api;
					}
				}
			}

			if (file_exists(Yii::getPathOfAlias("application.modules.{$moduleName}.components") . DIRECTORY_SEPARATOR . "{$moduleName}_API.php")) {
				$APIClass_prefix = '';
				$ns_components = explode('\\', get_class($module));
				if (count($ns_components) > 1) {
					// we're namespaced so the class for the api will also be namespaced.
					$APIClass_prefix = implode('\\', array_slice($ns_components, 0, count($ns_components)-1)) . '\components\\';
				}

				$APIClass = $APIClass_prefix . $moduleName.'_API';
				if (class_exists($APIClass)) {
					return new $APIClass;
				}
			}
		}

		return false;
	}

	protected $_module_class_map;

	/**
	 * Simple mapping function from module class name to it's id.
	 *
	 * @param $class_name
	 * @return mixed
	 */
	public function moduleIDFromClass($class_name) {
		if (!$this->_module_class_map) {
			foreach (Yii::app()->getModules() as $id => $mc) {
				$this->_module_class_map[$mc['class']] = $id;
			}
		}
		return @$this->_module_class_map[$class_name];
	}

	/**
	 * This is almost certainly not in the correct place for this.
	 *
	 * @param Patient $patient
	 * @param $side
	 * @param array $doodles
	 * @return array|null
	 */
	public function getPatientEyedrawDoodles(Patient $patient, $side, $doodles = array())
	{
		$elements_by_event_type_id = array();

		// build a list of relevant event types for the requested doodles
		foreach (Yii::app()->params['eyedraw_elements'] as $module_name => $elements) {
			$et_id = null;
			$elements_for_event_type = array();
			foreach ($elements as $element_cls => $element_doodles) {
				if (array_intersect($doodles, $element_doodles)) {
					if (!count($elements_for_event_type)) {
						if ($et = EventType::model()->find('class_name = ?', array($module_name)) ) {
							$et_id = $et->id;
						}
						else {
							break;
						}
					}
					$elements_for_event_type[] = array('cls' => $element_cls, 'doodles' => $element_doodles);
				}
			}
			if ($et_id)
				$elements_by_event_type_id[$et_id] = $elements_for_event_type;
		}

		if (!count($elements_by_event_type_id))
			return null;


		$criteria = new CDbCriteria();
		$criteria->addInCondition('event_type_id', array_keys($elements_by_event_type_id));
		$criteria->compare('episode.patient_id', $patient->id);
		$criteria->order = 't.event_date desc, t.created_date desc';

		$result = array();

		// iterate through the relevant events for the requested doodles, and check the elements defined
		// for those events for the given doodles. As each doodle is found, it's removed from the search
		// to ensure we only get the most recent definition of the doodle in this search.
		foreach (Event::model()->with(array('episode', 'episode.patient'))->findAll($criteria) as $event) {
			foreach ($elements_by_event_type_id[$event->event_type_id] as $element_info) {
				$model = $element_info['cls'];
				if ($element = $model::model()->find('event_id=?', array($event->id))) {
					if (!$element->{"has" . ucfirst($side)}())
						continue;
					$eyedraw = json_decode($element->{strtolower($side) . '_eyedraw'});

					foreach ($eyedraw as $ed) {
						$idx = array_search($ed->subclass, $doodles);
						if ($idx !== false) {
							$result[] = $ed;
						}
					}
					// don't want to search for any of the doodles that this element is defined as having
					// in previous events, if they weren't present in the last drawing it's assumed they
					// weren't present
					$to_remove = array_intersect($doodles, $element_info['doodles']);
					foreach (array_keys($to_remove) as $i)
						unset($doodles[$i]);
				}
			}
		}

		return $result;
	}
}
