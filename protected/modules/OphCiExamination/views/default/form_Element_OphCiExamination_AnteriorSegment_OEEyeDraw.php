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
<?php
$preload = Yii::app()->moduleAPI->getPatientEyedrawDoodles($this->patient, $side, array('PCIOL'));

$onreadycommand = array(
    array('addDoodle', array('AntSeg')),
    array('deselectDoodles', array()),
);

if (count($preload)) {
    $onreadycommand[] = array('loadAdditional', $preload);
}

$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
    'doodleToolBarArray' => array(
        array('NuclearCataract', 'CorticalCataract', 'PostSubcapCataract', 'PCIOL', 'ACIOL', 'Bleb', 'PI',
            'Fuchs', 'RK', 'LasikFlap', 'CornealScar', 'SectorIridectomy', 'PosteriorSynechia', 'Rubeosis',
            'TransilluminationDefect', 'KrukenbergSpindle', 'KeraticPrecipitates', 'PosteriorCapsule', 'Hypopyon',
            'CornealOedema', 'Episcleritis', 'Hyphaema'),
        array('TrabySuture', 'Supramid', 'TubeLigation', 'CornealSuture', 'TrabyFlap', 'SidePort', 'Patch',
            'ConjunctivalSuture', 'ACMaintainer', 'Tube', 'TubeExtender')
    ),
    'onReadyCommandArray' => $onreadycommand,
    'bindingArray' => array(
        'NuclearCataract' => array(
            'grade' => array('id' => 'OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'.$side.'_nuclear_id', 'attribute' => 'data-value'),
        ),
        'CorticalCataract' => array(
            'grade' => array('id' => 'OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'.$side.'_cortical_id', 'attribute' => 'data-value'),
        ),
    ),
    'deleteValueArray' => array(
        'OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'.$side.'_nuclear_id' => '',
        'OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'.$side.'_cortical_id' => '',
    ),
    'listenerArray' => array('anteriorListener', 'pupilListener'),
    
    'idSuffix' => $side.'_'.$element->elementType->id,
    'side' => ($side == 'right') ? 'R' : 'L',
    'mode' => 'edit',
    'width' => 300,
    'height' => 300,
    'model' => $element,
    'attribute' => $side.'_eyedraw',
    'maxToolbarButtons' => 7,
    'template' => 'OEEyeDrawWidget_InlineToolbar',
    'toggleScale' => 0.72,
    'fields' => $this->renderPartial($element->form_view . '_OEEyeDraw_fields', array(
        'form' => $form,
        'side' => $side,
        'element' => $element
    ), true)
));
?>
