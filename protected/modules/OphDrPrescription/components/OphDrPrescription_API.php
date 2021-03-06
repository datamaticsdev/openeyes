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

class OphDrPrescription_API extends BaseAPI
{
	/**
	 * get the prescription letter text for the latest prescription in the episode for the patient
	 *
	 * @param Patient $patient
	 * @param Episode $episode
	 * @return string
	 */
	public function getLetterPrescription($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($details = $this->getElementForLatestEventInEpisode($episode, 'Element_OphDrPrescription_Details')) {
				return $details->getLetterText();
			}
		}
	}

	public function canUpdate($event_id)
	{
		$details = Element_OphDrPrescription_Details::model()->find('event_id=?',array($event_id));

		return $details->isEditable();
	}

	/**
	 * Get or Create a Medication instance for the given patient id and item id.
	 *
	 * @TODO: consider error checking for Medication already existing?
	 * @param $patient_id
	 * @param $item_id
	 * @return Medication
	 * @throws Exception
	 */
	public function getMedicationForPrescriptionItem($patient_id, $item_id)
	{
		if ($item = OphDrPrescription_Item::model()->with('prescription.event.episode')->findByPk($item_id)) {
			if ($item->prescription->event->episode->patient_id != $patient_id) {
				throw new Exception("prescription item id and patient id must match");
			}
			$medication = new Medication();
			$medication->createFromPrescriptionItem($item);
			return $medication;
		};
	}

	/**
	 * @param Patient $patient
	 * @param array $exclude
	 * @return array|CActiveRecord[]|mixed|null
	 */
	public function getPrescriptionItemsForPatient(Patient $patient, $exclude = array())
	{
		$prescriptionCriteria = new CDbCriteria(array('order' => 'event_date DESC'));
		$prescriptionCriteria->addCondition('episode.patient_id = :id');
		$prescriptionCriteria->addNotInCondition('t.id',$exclude);
		$prescriptionCriteria->params = array_merge($prescriptionCriteria->params, array(':id' => $patient->id));
		$prescriptionItems = OphDrPrescription_Item::model()->with('prescription', 'drug', 'duration', 'prescription.event', 'prescription.event.episode')->findAll($prescriptionCriteria);

		return $prescriptionItems;
	}
}
