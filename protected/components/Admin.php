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

/**
 * A class for generic admin actions on a model
 */
class Admin
{
	/**
	 * @var BaseActiveRecord
	 */
	protected $model;

	/**
	 * @var string
	 */
	protected $modelName;

	/**
	 * @var string
	 */
	protected $modelDisplayName;

	/**
	 * @var string
	 */
	protected $listTemplate = '//admin/generic/list';

	/**
	 * @var string
	 */
	protected $editTemplate = '//admin/generic/edit';

	/**
	 * @var array
	 */
	protected $listFields = array();

	/**
	 * @var array
	 */
	protected $editFields = array();

	/**
	 * @var array
	 */
	protected $unsortableColumns = array('active');

	/**
	 * @var BaseAdminController
	 */
	protected $controller;

	/**
	 * @var CPagination
	 */
	protected $pagination;

	/**
	 * @var ModelSearch
	 */
	protected $search;

	/**
	 * @var int
	 */
	protected $modelId;

	/**
	 * @var string
	 */
	protected $customSaveURL;

	/**
	 * @var string
	 */
	protected $customCancelURL;

	/**
	 * @var bool
	 */
	protected $isSubList = false;

	/**
	 * @var int
	 *
	 */
	public $displayOrder = 0;

	/**
	 * @var array
	 */
	protected $filterFields = array();

	/**
	 * Contains key value of parent object relation for a sublist
	 * @var array
	 */
	protected $subListParent = array();

	/**
	 * @param $filters
	 */
	public function setFilterFields($filters)
	{
		$this->filterFields = $filters;
	}

	/**
	 * @return array
	 */
	public function getFilterFields()
	{
		return $this->filterFields;
	}

	/**
	 * @return BaseActiveRecord
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * @param BaseActiveRecord $model
	 */
	public function setModel(BaseActiveRecord $model)
	{
		$this->model = $model;
		if (!$this->modelName) {
			$this->modelName = get_class($model);
		}
	}

	/**
	 * @return string
	 */
	public function getModelName()
	{
		return $this->modelName;
	}

	/**
	 * @param string $modelName
	 */
	public function setModelName($modelName)
	{
		$this->modelName = $modelName;
	}

	/**
	 * @return string
	 */
	public function getModelDisplayName()
	{
		if (isset($this->modelDisplayName)) {
			return $this->modelDisplayName;
		} else {
			return $this->modelName;
		}
	}

	/**
	 * @param string $modelName
	 */
	public function setModelDisplayName($displayName)
	{
		$this->modelDisplayName = $displayName;
	}

	/**
	 * @return ModelSearch
	 */
	public function getSearch()
	{
		return $this->search;
	}

	/**
	 * @param ModelSearch $search
	 */
	public function setSearch($search)
	{
		$this->search = $search;
	}

	/**
	 * @return string
	 */
	public function getListTemplate()
	{
		return $this->listTemplate;
	}

	/**
	 * @param string $listTemplate
	 */
	public function setListTemplate($listTemplate)
	{
		$this->listTemplate = $listTemplate;
	}

	/**
	 * @return array
	 */
	public function getListFields()
	{
		return $this->listFields;
	}

	/**
	 * @param array $listFields
	 */
	public function setListFields($listFields)
	{
		$this->listFields = $listFields;
	}

	/**
	 * @return mixed
	 */
	public function getPagination()
	{
		return $this->pagination;
	}

	/**
	 * @param mixed $pagination
	 */
	public function setPagination($pagination)
	{
		$this->pagination = $pagination;
	}

	/**
	 * @return int
	 */
	public function getModelId()
	{
		return $this->modelId;
	}

	/**
	 * @param int $modelId
	 */
	public function setModelId($modelId)
	{
		$this->modelId = $modelId;
		$this->model = $this->model->findByPk($modelId);
	}

	/**
	 * @return array
	 */
	public function getEditFields()
	{
		return $this->editFields;
	}

	/**
	 * @param array $editFields
	 */
	public function setEditFields($editFields)
	{
		$this->editFields = $editFields;
	}

	/**
	 * @return string
	 */
	public function getEditTemplate()
	{
		return $this->editTemplate;
	}

	/**
	 * @param string $editTemplate
	 */
	public function setEditTemplate($editTemplate)
	{
		$this->editTemplate = $editTemplate;
	}

	/**
	 * @param $saveURL
	 */
	public function setCustomSaveURL($saveURL)
	{
		$this->customSaveURL = $saveURL;
	}

	/**
	 * @return string
	 */
	public function getCustomSaveURL()
	{
		return $this->customSaveURL;
	}

	/**
	 * @param $cancelURL
	 */
	public function setCustomCancelURL($cancelURL)
	{
		$this->customCancelURL = $cancelURL;
	}

	/**
	 * @return string
	 */
	public function getCustomCancelURL()
	{
		return $this->customCancelURL;
	}

	/**
	 * @return boolean
	 */
	public function isSubList()
	{
		return $this->isSubList;
	}

	/**
	 * @param boolean $isSubList
	 */
	public function setIsSubList($isSubList)
	{
		$this->isSubList = $isSubList;
	}

	/**
	 * @return BaseAdminController
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * @param BaseAdminController $controller
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
	}

	/**
	 * @return array
	 */
	public function getUnsortableColumns()
	{
		return $this->unsortableColumns;
	}

	/**
	 * @param array $unsortableColumns
	 */
	public function setUnsortableColumns($unsortableColumns)
	{
		$this->unsortableColumns = $unsortableColumns;
	}

	/**
	 * @return array
	 */
	public function getSubListParent()
	{
		return $this->subListParent;
	}

	/**
	 * @param array $subListParent
	 */
	public function setSubListParent($subListParent)
	{
		$this->subListParent = $subListParent;
	}

	/**
	 * @param BaseActiveRecord $model
	 * @param BaseAdminController $controller
	 */
	public function __construct(BaseActiveRecord $model, BaseAdminController $controller)
	{
		$this->setModel($model);
		$this->controller = $controller;
		$this->search = new ModelSearch($this->model);
		$this->request = $request = Yii::app()->getRequest();
		$this->assetManager = Yii::app()->getAssetManager();
		$this->assetManager->registerScriptFile('js/oeadmin/OpenEyes.admin.js');
	}

	/**
	 * Lists all the rows returned from the search in a table
	 *
	 * @throws CHttpException
	 */
	public function listModel($buttons=true)
	{
		if (!$this->model) {
			throw new CHttpException(500, 'Nothing to list');
		}

		$order = $this->request->getParam('d');

		if ($order == 0) {
			$this->displayOrder = 1;
		}

		$this->assetManager->registerScriptFile('js/oeadmin/list.js');
		$this->audit('list');
		$this->pagination = $this->getSearch()->initPagination();
		$this->render($this->listTemplate, array('admin' => $this, 'displayOrder' => $this->displayOrder,'buttons'=>$buttons));
	}

	/**
	 * Edits the model, runs validation and renders the edit form.
	 *
	 * @throws CHttpException
	 * @throws Exception
	 */
	public function editModel()
	{
		$this->assetManager->registerScriptFile('js/oeadmin/edit.js');
		$errors = array();
		if (Yii::app()->request->isPostRequest) {
			$post = Yii::app()->request->getPost($this->modelName);
			if(empty($post)){
				$this->modelName = str_replace("\\","_",$this->modelName);
				$post = $_POST[$this->modelName];
			}
			if (array_key_exists('id', $post) && $post['id']) {
				$this->model->attributes = $post;
			} else {
				$this->model = new $this->modelName;
				$this->model->attributes = $post;
			}

			if (!$this->model->validate()) {
				$errors = $this->model->getErrors();
			} else {

				if (!$this->model->save()) {
					throw new CHttpException(500,
						'Unable to save ' . $this->modelName . ': ' . print_r($this->model->getErrors(), true));
				}

				$this->audit('edit', $this->model->id);
				$return = '/' . $this->controller->uniqueid . '/list';
				if(Yii::app()->request->getPost('returnUriEdit')){
					$return = urldecode(Yii::app()->request->getPost('returnUriEdit'));
				}
				$this->controller->redirect($return);
			}
		} else {
			$defaults = Yii::app()->request->getParam('default', array());
			foreach($defaults as $key => $defaultValue){
				if($this->model->hasAttribute($key)){
					$this->model->$key = $defaultValue;
				}
			}
		}
		$this->render($this->editTemplate, array('admin' => $this, 'errors' => $errors));
	}

	/**
	 * Deletes the models for which an array of IDs has been posted
	 */
	public function deleteModel()
	{
		$response = 1;
		if (Yii::app()->request->isPostRequest) {
			$post = Yii::app()->request->getPost($this->modelName);
			if(array_key_exists('id', $post) && is_array($post['id'])){
				foreach ($post['id'] as $id) {
					$model = $this->model->findByPk($id);
					if (isset($model->active)) {
						$model->active = 0;
						if ($model && !$model->save()) {
							$response = 0;
						}
					} else {
						if ($model && !$model->delete()) {
							$response = 0;
						}
					}
				}
			}
		}

		echo $response;
	}

	/**
	 * Saves the display_order
	 *
	 * @throws CHttpException
	 */
	public function sortModel()
	{
		if(!$this->model->hasAttribute('display_order')){
			throw new CHttpException(400, 'This object cannot be ordered');
		}

		if (Yii::app()->request->isPostRequest) {
			$post = Yii::app()->request->getPost($this->modelName);
			$page = Yii::app()->request->getPost('page');
			if(!array_key_exists('display_order', $post) || !is_array($post['display_order'])){
				throw new CHttpException(400, 'No objects to order were provided');
			}

			foreach($post['display_order'] as $displayOrder => $id){
				$model = $this->model->findByPk($id);
				if(!$model){
					throw new CHttpException(400, 'Object to be ordered not found');
				}
				//Add one because display_order not zero indexed.
				//Times by page number to get correct order across pages.
				$model->display_order = ($displayOrder + 1) * $page;
				if (!$model->validate()) {
					throw new CHttpException(400, 'Order was invalid');
				}
				if (!$model->save()) {
					throw new CHttpException(500, 'Unable to save order');
				}
			}
			$this->audit('sort');
		}
	}

	/**
	 * Sets up search on all listed elements
	 */
	public function searchAll()
	{
		$searchArray = array('type' => 'compare', 'compare_to' => array());
		$searchFirst = '';
		foreach ($this->listFields as $field) {
			if (method_exists($this->model, 'get_' . $field)) {
				//we don't currently support searching on magic attributes not from the DB so continue
				continue;
			}
			if ($searchFirst === '') {
				$searchFirst = $field;
			} else {
				$searchArray['compare_to'][] = $field;
			}
		}
		$this->search->addSearchItem($searchFirst, $searchArray);
	}

	/**
	 * @param $row
	 * @param $attribute
	 * @return string
	 */
	public function attributeValue($row, $attribute)
	{
		if (isset($row->$attribute)) {
			return $row->$attribute;
		}

		if (strpos($attribute, '.')) {
			$splitAttribute = explode('.', $attribute);
			$relationTable = $splitAttribute[0];
			if (isset($row->$relationTable->$splitAttribute[1])) {
				return $row->$relationTable->$splitAttribute[1];
			}

			if (is_array($row->$relationTable)) {
				$manyResult = array();
				foreach ($row->$relationTable as $relationResult) {
					if (isset($relationResult->$splitAttribute[1])) {
						$manyResult[] = $relationResult->$splitAttribute[1];
					}
				}

				return implode(',', $manyResult);
			}
		}

		return '';
	}

	/**
	 * Returns wether a given column is sortable or not.
	 *
	 * @param $attribute
	 * @return bool
	 */
	public function isSortableColumn($attribute)
	{
		if($this->isSubList){
			return false;
		}

		if(in_array('display_order', $this->listFields, true)){
			return false;
		}

		if(strpos($attribute, 'has_') === 0){
			return false;
		}

		if(in_array($attribute, $this->unsortableColumns, true)){
			return false;
		}

		return true;
	}

	/**
	 * Takes the current URL, sets two values in it and returns it
	 *
	 * @param $attribute
	 * @param $order
	 * @param $queryString
	 * @return string
	 */
	public function sortQuery($attribute, $order, $queryString)
	{
		$queryArray = array();
		parse_str($queryString, $queryArray);
		$queryArray['c'] = $attribute;
		$queryArray['d'] = $order;

		return http_build_query($queryArray);
	}

	public function generateAdminForRelationList($relation, array $listFields)
	{
		$relatedModel = $this->relationClassFromRelation($relation);
		$relatedAdmin = new Admin($relatedModel, $this->controller);
		$relatedAdmin->setListFields($listFields);
		$relatedAdmin->setIsSubList(true);
		$relationField = $this->relationFieldFromRelation($relation);
		if($relationField){
			$criteria = $relatedAdmin->getSearch()->getCriteria();
			$criteria->addCondition($relationField.' = '.$this->model->id);
			$relatedAdmin->setSubListParent(array($relationField => $this->model->id));
		}

		return $relatedAdmin;
	}

	/**
	 * @param $relation
	 * @return BaseActiveRecord
	 * @throws CException
	 */
	protected function relationClassFromRelation($relation)
	{
		$relationDefinition = $this->getRelationDefnition($relation);
		$relationClass = $relationDefinition[1];
		if(!class_exists($relationClass)){
			throw new CException('Relation model does not exist');
		}

		return new $relationClass();
	}

	protected function relationFieldFromRelation($relation)
	{
		$relationDefinition = $this->getRelationDefnition($relation);

		return $relationDefinition[2];
	}

	/**
	 * @param $template
	 * @param array $data
	 */
	protected function render($template, $data = array())
	{
		$this->controller->render($template, $data);
	}

	/**
	 * @param $type
	 * @throws Exception
	 */
	protected function audit($type, $data = null)
	{
		Audit::add('admin-' . $this->modelName, $type, $data);
	}

	/**
	 * @param $relation
	 * @return mixed
	 * @throws CException
	 */
	protected function getRelationDefnition($relation)
	{
		$relations = $this->model->relations();
		if (!array_key_exists($relation, $relations)) {
			throw new CException('Relation does not exist');
		}

		$relationDefinition = $relations[$relation];

		return $relationDefinition;
	}

	/**
	 * @return string
	 */
	function generateReturnUrl($requestUri)
	{

		$split = explode('?', $requestUri);
		if (count($split) > 1) {
			$queryArray = array();
			parse_str($split[1], $queryArray);
			unset($queryArray['returnUri']);
			$split[1] = urlencode(http_build_query($queryArray));
		}
		$returnUri = implode('?', $split);
		return $returnUri;
	}
}