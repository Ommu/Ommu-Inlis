<?php
/**
 * InlisSettings
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (www.ommu.co)
 * @created date 27 April 2016, 12:00 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 *
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 *
 * --------------------------------------------------------------------------------------
 *
 * This is the model class for table "ommu_inlis_settings".
 *
 * The followings are the available columns in table 'ommu_inlis_settings':
 * @property integer $id
 * @property string $license
 * @property integer $permission
 * @property string $meta_keyword
 * @property string $meta_description
 * @property string $modified_date
 * @property string $modified_id
 */
class InlisSettings extends CActiveRecord
{
	use GridViewTrait;

	public $defaultColumns = array();
	
	// Variable Search
	public $modified_search;

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InlisSettings the static model class
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
		return 'ommu_inlis_settings';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('license, permission, meta_keyword, meta_description', 'required'),
			array('id, permission', 'numerical', 'integerOnly'=>true),
			array('license', 'length', 'max'=>32),
			array('modified_id', 'length', 'max'=>11),
			array('license', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, license, permission, meta_keyword, meta_description, modified_date, modified_id,
				modified_search', 'safe', 'on'=>'search'),
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
			'modified' => array(self::BELONGS_TO, 'Users', 'modified_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('attribute', 'ID'),
			'license' => Yii::t('attribute', 'License'),
			'permission' => Yii::t('attribute', 'Permission'),
			'meta_keyword' => Yii::t('attribute', 'Meta Keyword'),
			'meta_description' => Yii::t('attribute', 'Meta Description'),
			'modified_date' => Yii::t('attribute', 'Modified Date'),
			'modified_id' => Yii::t('attribute', 'Modified'),
			'modified_search' => Yii::t('attribute', 'Modified'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.license', strtolower($this->license), true);
		$criteria->compare('t.permission', $this->permission);
		$criteria->compare('t.meta_keyword', strtolower($this->meta_keyword), true);
		$criteria->compare('t.meta_description', strtolower($this->meta_description), true);
		if($this->modified_date != null && !in_array($this->modified_date, array('0000-00-00 00:00:00','1970-01-01 00:00:00','0002-12-02 07:07:12','-0001-11-30 00:00:00')))
			$criteria->compare('date(t.modified_date)', date('Y-m-d', strtotime($this->modified_date)));
		if(Yii::app()->getRequest()->getParam('modified'))
			$criteria->compare('t.modified_id', Yii::app()->getRequest()->getParam('modified'));
		else
			$criteria->compare('t.modified_id', $this->modified_id);
		
		// Custom Search
		$criteria->with = array(
			'modified_relation' => array(
				'alias'=>'modified_relation',
				'select'=>'displayname',
			),
		);
		$criteria->compare('modified_relation.displayname', strtolower($this->modified_search), true);


		if(!Yii::app()->getRequest()->getParam('InlisSettings_sort'))
			$criteria->order = 't.id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>30,
			),
		));
	}


	/**
	 * Get column for CGrid View
	 */
	public function getGridColumn($columns=null) {
		if($columns !== null) {
			foreach($columns as $val) {
				/*
				if(trim($val) == 'enabled') {
					$this->defaultColumns[] = array(
						'name'  => 'enabled',
						'value' => '$data->enabled == 1? "Ya": "Tidak"',
					);
				}
				*/
				$this->defaultColumns[] = $val;
			}
		} else {
			//$this->defaultColumns[] = 'id';
			$this->defaultColumns[] = 'license';
			$this->defaultColumns[] = 'permission';
			$this->defaultColumns[] = 'meta_keyword';
			$this->defaultColumns[] = 'meta_description';
			$this->defaultColumns[] = 'modified_date';
			$this->defaultColumns[] = 'modified_id';
		}

		return $this->defaultColumns;
	}

	/**
	 * Set default columns to display
	 */
	protected function afterConstruct() {
		if(count($this->defaultColumns) == 0) {
			$this->defaultColumns[] = array(
				'header' => 'No',
				'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1'
			);
			$this->defaultColumns[] = 'license';
			$this->defaultColumns[] = 'permission';
			$this->defaultColumns[] = 'meta_keyword';
			$this->defaultColumns[] = 'meta_description';
			$this->defaultColumns[] = array(
				'name' => 'modified_date',
				'value' => 'Utility::dateFormat($data->modified_date)',
				'htmlOptions' => array(
					'class' => 'center',
				),
				'filter' => $this->filterDatepicker($this, 'modified_date'),
			);
			$this->defaultColumns[] = array(
				'name' => 'modified_search',
				'value' => '$data->modified_relation->displayname',
			);
		}
		parent::afterConstruct();
	}

	/**
	 * User get information
	 */
	public static function getInfo($id, $column=null)
	{
		if($column != null) {
			$model = self::model()->findByPk($id, array(
				'select' => $column,
			));
			if(count(explode(',', $column)) == 1)
				return $model->$column;
			else
				return $model;
			
		} else {
			$model = self::model()->findByPk($id);
			return $model;
		}
	}

	/**
	 * before validate attributes
	 */
	protected function beforeValidate() {
		if(parent::beforeValidate()) {			
			$this->modified_id = Yii::app()->user->id;
		}
		return true;
	}
}