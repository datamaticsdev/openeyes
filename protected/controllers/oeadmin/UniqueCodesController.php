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
 * Class UniqueCodesController
 */
class UniqueCodesController extends BaseAdminController
{
	/**
	 * @var string
	 */
	public $layout = 'admin';

	/**
	 * @var int
	 */
	public $itemsPerPage = 100;

	/**
	 * Lists procedures
	 *
	 * @throws CHttpException
	 */
    
        
	public function actionList()
	{
		$admin = new Admin(UniqueCodes::model(), $this);
                
                
                $admin->setModelDisplayName('Unique Codes');
                
		$admin->setListFields(array(
			'id',
			'code',
			'active'
		));
                
                
		$admin->searchAll();
		$admin->getSearch()->addActiveFilter();
		$admin->getSearch()->setItemsPerPage($this->itemsPerPage);
		$admin->listModel(false);
	}

	/**
	 * Edits or adds a Procedure
	 *
	 * @param bool|int $id
	 * @throws CHttpException
	 */
	public function actionEdit($id = false)
	{
		$admin = new Admin(UniqueCodes::model(), $this);
		if ($id) {
			$admin->setModelId($id);
		}
                $admin->setModelDisplayName('Unique Codes');
		$admin->setEditFields(array(
			'code' => 'label',
			'active' => 'checkbox'
		));
		$admin->editModel();
	}

}