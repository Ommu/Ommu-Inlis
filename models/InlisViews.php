<?php
/**
 * InlisViews
 *
 * @author Putra Sudaryanto <putra@ommu.co>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (www.ommu.co)
 * @created date 3 April 2016, 09:59 WIB
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
 * This is the model class for table "ommu_inlis_views".
 *
 * The followings are the available columns in table 'ommu_inlis_views':
 * @property string $view_id
 * @property integer $publish
 * @property string $catalog_id
 * @property string $user_id
 * @property string $device_id
 * @property string $creation_date
 * @property string $creation_ip
 * @property string $deleted_date
 */
class InlisViews extends CActiveRecord
{
	use GridViewTrait;

	public $defaultColumns = array();
	
	// Variable Search
	public $catalog_search;
	public $user_search;

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InlisViews the static model class
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
		return 'ommu_inlis_views';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('publish, catalog_id', 'required'),
			array('publish', 'numerical', 'integerOnly'=>true),
			array('catalog_id, user_id, device_id', 'length', 'max'=>11),
			array('creation_ip', 'length', 'max'=>20),
			array('user_id, device_id', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('view_id, publish, catalog_id, user_id, device_id, creation_date, creation_ip, deleted_date,
				catalog_search, user_search', 'safe', 'on'=>'search'),
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
			'catalog' => array(self::BELONGS_TO, 'SyncCatalogs', 'catalog_id'),
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
			'device' => array(self::BELONGS_TO, 'UserDevice', 'device_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'view_id' => Yii::t('attribute', 'View'),
			'publish' => Yii::t('attribute', 'Publish'),
			'catalog_id' => Yii::t('attribute', 'Catalog'),
			'user_id' => Yii::t('attribute', 'User'),
			'device_id' => Yii::t('attribute', 'Device'),
			'creation_date' => Yii::t('attribute', 'Creation Date'),
			'creation_ip' => Yii::t('attribute', 'Creation Ip'),
			'deleted_date' => Yii::t('attribute', 'Deleted Date'),
			'catalog_search' => Yii::t('attribute', 'Catalog'),
			'user_search' => Yii::t('attribute', 'User'),
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

		$criteria->compare('t.view_id', strtolower($this->view_id), true);
		if(Yii::app()->getRequest()->getParam('type') == 'publish')
			$criteria->compare('t.publish', 1);
		elseif(Yii::app()->getRequest()->getParam('type') == 'unpublish')
			$criteria->compare('t.publish', 0);
		elseif(Yii::app()->getRequest()->getParam('type') == 'trash')
			$criteria->compare('t.publish', 2);
		else {
			$criteria->addInCondition('t.publish', array(0,1));
			$criteria->compare('t.publish', $this->publish);
		}
		if(Yii::app()->getRequest()->getParam('catalog'))
			$criteria->compare('t.catalog_id', Yii::app()->getRequest()->getParam('catalog'));
		else
			$criteria->compare('t.catalog_id', $this->catalog_id);
		if(Yii::app()->getRequest()->getParam('user'))
			$criteria->compare('t.user_id', Yii::app()->getRequest()->getParam('user'));
		else
			$criteria->compare('t.user_id', $this->user_id);
		if(Yii::app()->getRequest()->getParam('device'))
			$criteria->compare('t.device_id', Yii::app()->getRequest()->getParam('device'));
		else
			$criteria->compare('t.device_id', $this->device_id);
		if($this->creation_date != null && !in_array($this->creation_date, array('0000-00-00 00:00:00','1970-01-01 00:00:00','0002-12-02 07:07:12','-0001-11-30 00:00:00')))
			$criteria->compare('date(t.creation_date)', date('Y-m-d', strtotime($this->creation_date)));
		$criteria->compare('t.creation_ip', strtolower($this->creation_ip), true);
		if($this->deleted_date != null && !in_array($this->deleted_date, array('0000-00-00 00:00:00','1970-01-01 00:00:00','0002-12-02 07:07:12','-0001-11-30 00:00:00')))
			$criteria->compare('date(t.deleted_date)', date('Y-m-d', strtotime($this->deleted_date)));
		
		// Custom Search
		$criteria->with = array(
			'catalog' => array(
				'alias' => 'catalog',
				'select' => 'Title',
			),
			'user' => array(
				'alias' => 'user',
				'select' => 'displayname',
			),
		);
		$criteria->compare('catalog.Title', strtolower($this->catalog_search), true);
		$criteria->compare('user.displayname', strtolower($this->user_search), true);

		if(!Yii::app()->getRequest()->getParam('InlisViews_sort'))
			$criteria->order = 't.view_id DESC';

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
			//$this->defaultColumns[] = 'view_id';
			$this->defaultColumns[] = 'publish';
			$this->defaultColumns[] = 'catalog_id';
			$this->defaultColumns[] = 'user_id';
			$this->defaultColumns[] = 'device_id';
			$this->defaultColumns[] = 'creation_date';
			$this->defaultColumns[] = 'creation_ip';
			$this->defaultColumns[] = 'deleted_date';
		}

		return $this->defaultColumns;
	}

	/**
	 * Set default columns to display
	 */
	protected function afterConstruct() {
		if(count($this->defaultColumns) == 0) {
			/*
			$this->defaultColumns[] = array(
				'class' => 'CCheckBoxColumn',
				'name' => 'id',
				'selectableRows' => 2,
				'checkBoxHtmlOptions' => array('name' => 'trash_id[]')
			);
			*/
			$this->defaultColumns[] = array(
				'header' => 'No',
				'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1'
			);
			$this->defaultColumns[] = array(
				'name' => 'catalog_search',
				'value' => '$data->catalog->Title',
			);
			$this->defaultColumns[] = array(
				'name' => 'user_search',
				'value' => '$data->user->displayname',
			);
			$this->defaultColumns[] = array(
				'name' => 'creation_date',
				'value' => 'Yii::app()->dateFormatter->formatDateTime($data->creation_date, \'medium\', false)',
				'htmlOptions' => array(
					'class' => 'center',
				),
				'filter' => $this->filterDatepicker($this, 'creation_date'),
			);
			$this->defaultColumns[] = 'creation_ip';
			if(!Yii::app()->getRequest()->getParam('type')) {
				$this->defaultColumns[] = array(
					'name' => 'publish',
					'value' => 'Utility::getPublish(Yii::app()->controller->createUrl(\'publish\', array(\'id\'=>$data->view_id)), $data->publish, 1)',
					'htmlOptions' => array(
						'class' => 'center',
					),
					'filter' => $this->filterYesNo(),
					'type' => 'raw',
				);
			}
			$this->defaultColumns[] = array(
				'name' => 'deleted_date',
				'value' => '!in_array($data->deleted_date, array("0000-00-00 00:00:00","1970-01-01 00:00:00")) ? Yii::app()->dateFormatter->formatDateTime($data->deleted_date, \'medium\', false) : \'-\'',
				'htmlOptions' => array(
					'class' => 'center',
				),
				'filter' => $this->filterDatepicker($this, 'deleted_date'),
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
			if($this->isNewRecord && $this->user_id == '')
				$this->user_id = Yii::app()->user->id;
			$this->creation_ip = $_SERVER['REMOTE_ADDR'];
		}
		return true;
	}

}