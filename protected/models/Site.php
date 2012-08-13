<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "site".
 *
 * The followings are the available columns in table 'site':
 * @property string $id
 * @property string $name
 * @property string $code
 * @property string $short_name
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $postcode
 * @property string $fax
 * @property string $telephone
 *
 * The followings are the available model relations:
 * @property Theatre[] $theatres
 * @property Ward[] $wards
 */
class Site extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Site the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'site';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'theatres' => array(self::HAS_MANY, 'Theatre', 'site_id'),
			'wards' => array(self::HAS_MANY, 'Ward', 'site_id'),
			'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Fetch an array of site IDs and names
	 * @return array
	 */
	public function getList()
	{
		$list = Site::model()->findAll(array('order' => 'short_name'));

		$result = array();

		foreach ($list as $site) {
			$result[$site->id] = $site->short_name;
		}

		return $result;
	}

	public function getListForCurrentInstitution($field=false) {
		if (!$field) $field = 'short_name';

		$site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

		$criteria = new CDbCriteria;
		$criteria->compare('institution_id',$site->institution_id);
		$criteria->compare('id','<>13');
		$criteria->order = $field.' asc';

		$result = array();

		foreach (Site::model()->findAll($criteria) as $site) {
			$result[$site->id] = $site->$field;
		}

		return $result;
	}

	public function getDefaultSite() {
		$site = null;
		if(Yii::app()->params['default_site_code']) {
			$site = $this->findByAttributes(array('code' => Yii::app()->params['default_site_code']));
		}
		if(!$site) {
			$site = $this->find();
		}
		return $site;
	}
	
	public function getLetterHtml() {
		$address = array();
		foreach (array('name', 'address1', 'address2', 'address3', 'postcode') as $field) {
			if (!empty($this->$field)) {
				$address[] = CHtml::encode($this->$field);
			}
		}
		return implode('<br />', $address);
	}

	public function getLetterArray() {
		$address = array();
		foreach (array('address1', 'address2', 'address3', 'postcode') as $field) {
			if (!empty($this->$field)) {
				if ($field == 'address1') {
					$address[] = CHtml::encode(str_replace(',','',$this->$field));
				} else {
					$address[] = CHtml::encode($this->$field);
				}
			}
		}
		return $address;
	}

	public function getLetterAddress() {
		$address = "$this->name\n";

		return $address . implode("\n",$this->getLetterArray(false));
	}
}
