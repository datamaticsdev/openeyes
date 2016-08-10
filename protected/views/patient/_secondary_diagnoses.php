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

<?php

$specialty = Specialty::model()->findByAttributes(array('code' => $code));

?>
<section class="box patient-info associated-data js-toggle-container">

    <header class="box-header">
        <h3 class="box-title">
            <span class="icon-patient-clinician-hd_flag"></span>
            Other <?= ucfirst($specialty->adjective) ?> Diagnoses
        </h3>
        <a href="#" class="toggle-trigger toggle-hide js-toggle">
			<span class="icon-showhide">
				Show/hide this section
			</span>
        </a>
    </header>

    <div class="js-toggle-body">

        <table class="plain patient-data">
            <thead>
            <tr>
                <th>Date</th>
                <th>Diagnosis</th>
                <?php if ($this->checkAccess('OprnEditOtherOphDiagnosis')) { ?>
                    <th>Actions</th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->patient->getSpecialtyDiagnoses($code) as $diagnosis) {?>
                <tr>
                    <td><?php echo $diagnosis->dateText?></td>
                    <td><?php echo $diagnosis->eye ? $diagnosis->eye->adjective : ''; ?> <?php echo $diagnosis->disorder->term?></td>
                    <?php if ($this->checkAccess('OprnEditOtherOphDiagnosis')) { ?>
                        <td><a href="#" class="removeDiagnosis" rel="<?php echo $diagnosis->id?>">Remove</a></td>
                    <?php } ?>
                </tr>
            <?php }?>
            </tbody>
        </table>

        <?php if ($this->checkAccess('OprnEditOtherOphDiagnosis')) { ?>
            <div class="box-actions">
                <button id='btn-add_new_<?=$code?>_diagnosis' class="secondary small">
                    Add <?= ucfirst($specialty->adjective) ?> Diagnosis
                </button>
            </div>

            <div id="add_new_<?=$code?>_diagnosis" style="display: none;">

                <?php
                $form = $this->beginWidget('FormLayout', array(
                    'id' => "add-{$code}-diagnosis",
                    'enableAjaxValidation' => false,
                    'action' => array('patient/adddiagnosis'),
                    'layoutColumns' => array(
                        'label' => 3,
                        'field' => 9,
                    ),
                    'htmlOptions' => array(
                        'class' => 'form add-data',
                    ),
                ))?>

                <fieldset class="field-row">

                    <legend><strong>Add <?=$specialty->adjective?> diagnosis</strong></legend>

                    <?php $form->widget('application.widgets.DiagnosisSelection', array(
                        'field' => "{$code}_disorder_id",
                        'label' => 'Diagnosis',
                        'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
                        'code' => $code,
                        'default' => false,
                        'layout' => 'patientSummary',
                        'loader' => "add_{$code}_diagnosis_loader",
                    ))?>

                    <div class="row field-row hide" id="add_<?=$code?>_diagnosis_loader">
                        <p class="large-offset-<?php echo $form->layoutColumns['label'];?> large-<?php echo $form->layoutColumns['field'];?> column end">
                            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" />
                            searching...
                        </p>
                    </div>

                    <input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

<!--                    <fieldset class="diagnosis_eye row field-row">-->
<!--                        <legend class="--><?php //echo $form->columns('label');?><!--">-->
<!--                            Eye:-->
<!--                        </legend>-->
<!--                        <div class="--><?php //echo $form->columns('field');?><!--">-->
<!--                            --><?php //foreach (Eye::model()->findAll(array('order' => 'display_order')) as $i => $eye) {?>
<!--                                <label class="inline">-->
<!--                                    <input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="--><?php //echo $eye->id?><!--"--><?php //if ($i == 0) {?><!-- checked="checked"--><?php //}?><!-- /> --><?php //echo $eye->name?>
<!--                                </label>-->
<!--                            --><?php //}?>
<!--                        </div>-->
<!--                    </fieldset>-->

                    <?php $this->renderPartial('_fuzzy_date', array('form' => $form, 'label' => 'Date diagnosed'))?>

                    <div class="<?=$code?>_diagnoses_form_errors alert-box alert hide"></div>

                    <div class="buttons">
                        <img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_<?=$code?>_diagnosis_loader" style="display: none;" />
                        <button type="submit" class="secondary small btn_save_<?=$code?>_diagnosis">
                            Save
                        </button>
                        <button class="warning small btn_cancel_<?=$code?>_diagnosis">
                            Cancel
                        </button>
                    </div>

                </fieldset>
                <?php $this->endWidget()?>
            </div>
        <?php } ?>
    </div>
</section>
<?php if ($this->checkAccess('OprnEditOtherOphDiagnosis')) { ?>
    <!-- Confirm deletion dialog -->
    <div id="confirm_remove_diagnosis_dialog" title="Confirm remove diagnosis" style="display: none;">
        <div id="delete_diagnosis">
            <div class="alert-box alert with-icon">
                <strong>WARNING: This will remove the diagnosis from the patient record.</strong>
            </div>
            <p>
                <strong>Are you sure you want to proceed?</strong>
            </p>
            <div class="buttons">
                <input type="hidden" id="diagnosis_id" value="" />
                <button type="submit" class="warning small btn_remove_diagnosis">Remove diagnosis</button>
                <button type="submit" class="secondary small btn_cancel_remove_diagnosis">Cancel</button>
                <img class="loader hide" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." />
            </div>
        </div>
    </div>

    <script type="text/javascript">

        $('#btn-add_new_<?=$code?>_diagnosis').click(function() {
            $('#add_new_<?=$code?>_diagnosis').slideToggle('fast');
            $('#btn-add_new_<?=$code?>_diagnosis').attr('disabled',true);
            $('#btn-add_new_<?=$code?>_diagnosis').addClass('disabled');
        });
        $('button.btn_cancel_<?=$code?>_diagnosis').click(function(e) {
            $('#add_new_<?=$code?>_diagnosis').slideToggle('fast');
            $('#btn-add_new_<?=$code?>_diagnosis').attr('disabled',false);
            $('#btn-add_new_<?=$code?>_diagnosis').removeClass('disabled');
            OpenEyes.Form.reset($(e.target).closest('form'));
            return false;
        });
        $('button.btn_save_<?=$code?>_diagnosis').click(function() {
            $.ajax({
                'type': 'POST',
                'dataType': 'json',
                'url': baseUrl+'/patient/validateadddiagnosis',
                'data': $('#add-<?=$code?>-diagnosis').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                'success': function(data) {
                    $('div.<?=$code?>_diagnoses_form_errors').hide();
                    if (data.length == 0) {
                        $('img.add_<?=$code?>_diagnosis_loader').show();
                        $('#add-<?=$code?>-diagnosis').submit();
                        return true;
                    } else {
                        $('div.<?=$code?>_diagnoses_form_errors').html('');
                        for (var i in data) {
                            $('div.<?=$code?>_diagnoses_form_errors').show().append('<div>'+data[i]+'</div>');
                        }
                    }
                }
            });
            return false;
        });

    </script>
<?php } ?>