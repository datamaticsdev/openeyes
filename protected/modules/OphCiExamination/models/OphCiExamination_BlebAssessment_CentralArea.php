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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_bleb_assessment_central_area".
 *
 * @property integer $id
 * @property integer $area
 * @property boolean $active

 */
class OphCiExamination_BlebAssessment_CentralArea extends \BaseActiveRecordVersioned
{
    protected $attribute_options = array();

    /**
     * Returns the static model of the specified AR class.
     * @return OphCiExamination_BlebAssessment_CentralArea the static model class
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
        return 'ophciexamination_bleb_assessment_central_area';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('area', 'required'),
                array('id, area', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            //'attribute_elements' => array(self::HAS_MANY, 'OphCiExamination_BlebAssessment_CentralAreaElement', 'attribute_id'),
        );
    }



    public function getAttributeOptions()
    {
        return $this->attribute_options;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('area', $this->name, true);
        return new CActiveDataProvider(get_class($this), array(
                'criteria'=>$criteria,
        ));
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
            'FieldImages' => 'FieldImages'
        );
    }

    public function fieldImages()
    {
        return array('area');
    }
}
