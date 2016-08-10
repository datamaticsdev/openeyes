<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="element-fields">
	<?php echo $form->radioButtons($element, 'anaesthetic_type_id', 'AnaestheticType')?>
	<?php echo $form->radioButtons($element, 'anaesthetist_id', 'Anaesthetist')?>

	<?php echo $form->hiddenField($element, 'anaesthetic_delivery_id')?>
	<?php echo $form->multiSelectList($element, 'AnaestheticAgent', 'anaesthetic_agents', 'id', $this->getAnaesthetic_agent_list($element), null, array('empty' => '- Anaesthetic agents -', 'label' => 'Agents'), false, false, null, false, false, array('field' => 3))?>
	<?php echo $form->textArea($element, 'anaesthetic_comment', array(), false, array('rows' => 2))?>
</div>