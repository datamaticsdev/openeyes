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


class SnippetController extends ModuleAdminController
{

	protected $admin;

	/**
	 * @var int
	 */
	public $itemsPerPage = 100;


	protected function beforeAction($action)
	{
		$this->admin = new Admin(LetterString::model(), $this);
		$this->admin->setModelDisplayName('Letter String');

		return parent::beforeAction($action);
	}

	/**
	 * Lists snippets
	 *
	 * @throws CHttpException
	 */
	public function actionList()
	{
		$this->admin->setListFields(array(
			'display_order',
			'id',
			'name',
			'body',
			'elementTypeName',
			'eventTypeName',
		));
		$this->admin->getSearch()->addSearchItem('site_id', array(
			'type' => 'dropdown',
			'options' => CHtml::listData(Institution::model()->getCurrent()->sites,'id', 'name'),
			'default' => Yii::app()->session['selected_site_id'],
		));
		$this->admin->listModel();
	}

	/**
	 * Edits or adds a snippets
	 *
	 * @param bool|int $id
	 * @throws CHttpException
	 */
	public function actionEdit($id = false)
	{
		if($id){
			$this->admin->setModelId($id);
		}
		$this->admin->setEditFields(array(
			'site_id' => array(
				'widget' => 'DropDownList',
				'options' => CHtml::listData(Institution::model()->getCurrent()->sites, 'id', 'short_name'),
				'default' => Yii::app()->request->getParam('site_id'),
				'htmlOptions' => null,
				'hidden' => false,
				'layoutColumns' => null
			),
			'letter_string_group_id' => array(
				'widget' => 'DropDownList',
				'options' => CHtml::listData(LetterStringGroup::model()->findAll(),'id', 'name'),
				'default' => Yii::app()->request->getParam('group_id'),
				'htmlOptions' => null,
				'hidden' => false,
				'layoutColumns' => null
			),
			'name' => 'text',
			'body' => array(
				'widget' => 'CustomView',
				'viewName' => '//admin/generic/shortcodeText',
				'viewArguments' => array('model' => $this->admin->getModel())
			),
			'event_type' =>  array(
				'widget' => 'DropDownList',
				'options' => CHtml::listData(EventType::model()->findAll(),'class_name', 'name'),
				'htmlOptions' => array('empty' => '- Select -'),
				'hidden' => false,
				'layoutColumns' => null
			),
			'element_type' => array(
				'widget' => 'DropDownList',
				'options' => CHtml::listData(ElementType::model()->findAll(),'class_name', 'name'),
				'htmlOptions' => array('empty' => '- Select -'),
				'hidden' => false,
				'layoutColumns' => null
			),
		));
		$this->admin->editModel();
	}

	/**
	 * Deletes rows for the model
	 */
	public function actionDelete()
	{
		$this->admin->deleteModel();
	}

	/**
	 * Save ordering of the objects
	 */
	public function actionSort()
	{
		$this->admin->sortModel();
	}
}