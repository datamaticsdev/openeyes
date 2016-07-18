<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class Element_OphTrOperationnote_Trabeculectomy extends Element_OnDemand
{
	public function tableName()
	{
		return 'et_ophtroperationnote_trabeculectomy';
	}

	public function rules()
	{
		return array(
			array('eyedraw, conjunctival_flap_type_id, stay_suture, site_id, size_id, sclerostomy_type_id, viscoelastic_type_id, viscoelastic_removed, viscoelastic_flow_id, report, difficulty_other, complication_other', 'safe'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'conjunctival_flap_type_id' => 'Conjunctival flap',
			'stay_suture' => 'Stay suture',
			'site_id' => 'Site',
			'size_id' => 'Size',
			'sclerostomy_type_id' => 'Sclerostomy',
			'viscoelastic_type_id' => 'Viscoelastic',
			'viscoelastic_removed' => 'Removed',
			'viscoelastic_flow_id' => 'Flow',
			'complication_other' => 'Other complication',
			'difficulty_other' => 'Other difficulty',
			'report' => 'Description'
		);
	}

	public function relations()
	{
		return array(
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'conjunctival_flap_type' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Conjunctival_Flap_Type', 'conjunctival_flap_type_id'),
			'site' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Site', 'site_id'),
			'size' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Size', 'size_id'),
			'sclerostomy_type' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Sclerostomy_Type', 'sclerostomy_type_id'),
			'viscoelastic_type' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Viscoelastic_Type', 'viscoelastic_type_id'),
			'viscoelastic_flow' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Viscoelastic_Flow', 'viscoelastic_flow_id'),
			'difficulties' => array(self::MANY_MANY, 'OphTrOperationnote_Trabeculectomy_Difficulty', 'ophtroperationnote_trabeculectomy_difficulties(element_id, difficulty_id)'),
			'difficulty_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_Trabeculectomy_Difficulties', 'element_id'),
			'complications' => array(self::MANY_MANY, 'OphTrOperationnote_Trabeculectomy_Complication', 'ophtroperationnote_trabeculectomy_complications(element_id, complication_id)'),
			'complication_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_Trabeculectomy_Complications', 'element_id'),
		);
	}

	public function getEye()
	{
		return Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?',array($this->event_id))->eye;
	}

	public function afterValidate()
	{
		if ($this->hasMultiSelectValue('difficulties','Other')) {
			if (!$this->difficulty_other) {
				$this->addError('difficulty_other',$this->getAttributeLabel('difficulty_other').' cannot be blank.');
			}
		}

		if ($this->hasMultiSelectValue('complications','Other')) {
			if (!$this->complication_other) {
				$this->addError('complication_other',$this->getAttributeLabel('complication_other').' cannot be blank.');
			}
		}

		return parent::afterValidate();
	}

	/**
	 * @var boolean
	 */
	protected $_has_left;

	/**
	 * @return bool
	 */
	public function hasLeft()
	{
		if (is_null($this->_has_left)) {
			if ($proc = Element_OphTrOperationnote_ProcedureList::model()->find('event_id = ?', array($this->event_id))) {
				$this->_has_left = $proc->hasLeft();
			}
			else {
				$this->_has_left = false;
			}
		}
		return $this->_has_left;
	}

	public function getLeft_eyedraw()
	{
		return $this->hasLeft() ? $this->eyedraw : null;
	}

	/**
	 * @var boolean
	 */
	protected $_has_right;

	/**
	 * @return bool
	 */
	public function hasRight()
	{
		if (is_null($this->_has_right)) {
			if ($proc = Element_OphTrOperationnote_ProcedureList::model()->find('event_id = ?', array($this->event_id))) {
				$this->_has_right = $proc->hasRight();
			}
			else {
				$this->_has_right = false;
			}
		}
		return $this->_has_right;
	}

	/**
	 * @return null|string
	 */
	public function getRight_eyedraw()
	{
		return $this->hasRight() ? $this->eyedraw : null;
	}
}
