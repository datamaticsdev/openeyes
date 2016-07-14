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
?>
<div class="sub-element-fields">
    <div class="field-row row">
        <div class="large-6 column">
            <?php $form->radioButtons($element, 'anticoagulant', array(
                0 => 'Not Checked',
                1 => 'Yes',
                2 => 'No'
            ),
                ($element->anticoagulant !== null) ? $element->anticoagulant : 0,
                false,
                false,
                false,
                false,
                array(
                    'text-align' => 'right',
                    'nowrapper' => false
                ),
                array(
                    'label' => 4,
                    'field' => 8,
                ));
            ?>
        </div>
        <div class="large-4 column end">
            <?php $form->textField(
                $element,
                'anticoagulant_name',
                array(),
                array(),
                array(
                    'label' => 4,
                    'field' => 8,
                )
            );?>
        </div>
    </div>
    <div class="field-row row">
        <div class="large-6 column">
            <?php $form->radioButtons($element, 'alphablocker', array(
                0 => 'Not Checked',
                1 => 'Yes',
                2 => 'No'
            ),
                ($element->alphablocker !== null) ? $element->alphablocker : 0,
                false,
                false,
                false,
                false,
                array(
                    'text-align' => 'right',
                    'nowrapper' => false
                ),
                array(
                    'label' => 4,
                    'field' => 8,
                ));
            ?>
        </div>
        <div class="large-4 column end">
            <?php $form->textField(
                $element,
                'alpha_blocker_name',
                array(),
                array(),
                array(
                    'label' => 4,
                    'field' => 8,
                ));?>
        </div>
    </div>
</div>