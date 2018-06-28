<?php
/**
 * InlisSearchs
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (opensource.ommu.co)
 * @created date 11 April 2016, 03:24 WIB
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
 * This is the model class for table "ommu_inlis_searchs".
 *
 * The followings are the available columns in table 'ommu_inlis_searchs':
 * @property string $search_id
 * @property integer $publish
 * @property string $user_id
 * @property string $device_id
 * @property integer $search_type
 * @property string $search_key
 * @property string $creation_date
 * @property string $creation_ip
 * @property string $deleted_date
 */
class InlisSearchs extends CActiveRecord
{
	public $defaultColumns = array();
	
	// Variable Search
	public $user_search;

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InlisSearchs the static model class
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
		return 'ommu_inlis_searchs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('search_type, search_key', 'required'),
			array('publish, search_type', 'numerical', 'integerOnly'=>true),
			array('user_id, device_id', 'length', 'max'=>11),
			array('creation_ip', 'length', 'max'=>20),
			array('user_id, device_id', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('search_id, publish, user_id, device_id, search_type, search_key, creation_date, creation_ip, deleted_date,
				user_search', 'safe', 'on'=>'search'),
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
			'search_id' => Yii::t('attribute', 'Search'),
			'publish' => Yii::t('attribute', 'Publish'),
			'user_id' => Yii::t('attribute', 'User'),
			'device_id' => Yii::t('attribute', 'Device'),
			'search_type' => Yii::t('attribute', 'Search Type'),
			'search_key' => Yii::t('attribute', 'Search Key'),
			'creation_date' => Yii::t('attribute', 'Creation Date'),
			'creation_ip' => Yii::t('attribute', 'Creation Ip'),
			'deleted_date' => Yii::t('attribute', 'Deleted Date'),
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

		$criteria->compare('t.search_id',strtolower($this->search_id),true);
		if(isset($_GET['type']) && $_GET['type'] == 'publish')
			$criteria->compare('t.publish',1);
		elseif(isset($_GET['type']) && $_GET['type'] == 'unpublish')
			$criteria->compare('t.publish',0);
		elseif(isset($_GET['type']) && $_GET['type'] == 'trash')
			$criteria->compare('t.publish',2);
		else {
			$criteria->addInCondition('t.publish',array(0,1));
			$criteria->compare('t.publish',$this->publish);
		}
		if(isset($_GET['user']))
			$criteria->compare('t.user_id',$_GET['user']);
		else
			$criteria->compare('t.user_id',$this->user_id);
		if(isset($_GET['device']))
			$criteria->compare('t.device_id',$_GET['device']);
		else
			$criteria->compare('t.device_id',$this->device_id);
		$criteria->compare('t.search_type',$this->search_type);
		$criteria->compare('t.search_key',strtolower($this->search_key),true);
		if($this->creation_date != null && !in_array($this->creation_date, array('0000-00-00 00:00:00', '0000-00-00')))
			$criteria->compare('date(t.creation_date)',date('Y-m-d', strtotime($this->creation_date)));
		$criteria->compare('t.creation_ip',strtolower($this->creation_ip),true);
		if($this->deleted_date != null && !in_array($this->deleted_date, array('0000-00-00 00:00:00', '0000-00-00')))
			$criteria->compare('date(t.deleted_date)',date('Y-m-d', strtotime($this->deleted_date)));
		
		// Custom Search
		$criteria->with = array(
			'user' => array(
				'alias'=>'user',
				'select'=>'displayname',
			),
		);
		$criteria->compare('user.displayname',strtolower($this->user_search), true);

		if(!isset($_GET['InlisSearchs_sort']))
			$criteria->order = 't.search_id DESC';

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
			//$this->defaultColumns[] = 'search_id';
			$this->defaultColumns[] = 'publish';
			$this->defaultColumns[] = 'user_id';
			$this->defaultColumns[] = 'device_id';
			$this->defaultColumns[] = 'search_type';
			$this->defaultColumns[] = 'search_key';
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
				'name' => 'user_search',
				'value' => '$data->user->displayname',
			);
			$this->defaultColumns[] = array(
				'name' => 'search_type',
				'value' => '$data->search_type == 0 ? Yii::t("Simple", "Simple") : Yii::t("Advance", "Advance")',
				'filter'=>array(
					0=>Yii::t('Simple', 'Simple'),
					1=>Yii::t('Advance', 'Advance'),
				),
			);
			$this->defaultColumns[] = 'search_key';
			$this->defaultColumns[] = array(
				'name' => 'creation_date',
				'value' => 'Utility::dateFormat($data->creation_date)',
				'htmlOptions' => array(
					'class' => 'center',
				),
				'filter' => Yii::app()->controller->widget('application.libraries.core.components.system.CJuiDatePicker', array(
					'model'=>$this,
					'attribute'=>'creation_date',
					'language' => 'en',
					'i18nScriptFile' => 'jquery-ui-i18n.min.js',
					//'mode'=>'datetime',
					'htmlOptions' => array(
						'id' => 'creation_date_filter',
					),
					'options'=>array(
						'showOn' => 'focus',
						'dateFormat' => 'dd-mm-yy',
						'showOtherMonths' => true,
						'selectOtherMonths' => true,
						'changeMonth' => true,
						'changeYear' => true,
						'showButtonPanel' => true,
					),
				), true),
			);
			$this->defaultColumns[] = 'creation_ip';
			if(!isset($_GET['type'])) {
				$this->defaultColumns[] = array(
					'name' => 'publish',
					'value' => 'Utility::getPublish(Yii::app()->controller->createUrl("publish",array("id"=>$data->search_id)), $data->publish, 1)',
					'htmlOptions' => array(
						'class' => 'center',
					),
					'filter'=>array(
						1=>Yii::t('phrase', 'Yes'),
						0=>Yii::t('phrase', 'No'),
					),
					'type' => 'raw',
				);
			}
			$this->defaultColumns[] = array(
				'name' => 'deleted_date',
				'value' => '!in_array($data->deleted_date, array("0000-00-00 00:00:00","1970-01-01 00:00:00")) ? Utility::dateFormat($data->deleted_date) : "-"',				
				'htmlOptions' => array(
					'class' => 'center',
				),
				'filter' => Yii::app()->controller->widget('application.libraries.core.components.system.CJuiDatePicker', array(
					'model'=>$this,
					'attribute'=>'deleted_date',
					'language' => 'en',
					'i18nScriptFile' => 'jquery-ui-i18n.min.js',
					//'mode'=>'datetime',
					'htmlOptions' => array(
						'id' => 'creation_date_filter',
					),
					'options'=>array(
						'showOn' => 'focus',
						'dateFormat' => 'dd-mm-yy',
						'showOtherMonths' => true,
						'selectOtherMonths' => true,
						'changeMonth' => true,
						'changeYear' => true,
						'showButtonPanel' => true,
					),
				), true),
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
			$model = self::model()->findByPk($id,array(
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
	 * Lists all models.
	 */
	public static function insertSearch($post, $type=null) 
	{
		$token = trim($post['token']);
		$apikey = trim($post['apikey']);
		$type = $type != null ? $type : 0;
		
		$keyPost = array_diff_key($post, array_flip((array) ['pagesize','token','apikey','dump','variable']));
		$key = serialize(array_map('trim', $keyPost));
			
		if($token != null && $token != '') {
			$user = ViewUsers::model()->findByAttributes(array('token_password' => $token), array(
				'select' => 'user_id',
			));
			if($user != null) {
				$search = InlisSearchs::model()->find(array(
					'select'    => 'search_id',
					'condition' => 'publish= :publish AND user_id= :user AND search_type= :type AND search_key= :key',
					'params'    => array(
						':publish' => 1,
						':user' => $user->user_id,
						':type' => $type,
						':key' => $key,
					),
				));
				if($search == null) {
					$data=new InlisSearchs;
					$data->user_id = $user->user_id;
					$data->search_type = $type;
					$data->search_key = $key;
					$data->save();
				}
			}
			
		} else {
			$device = UserDevice::model()->findByAttributes(array('android_id' => $apikey), array(
				'select' => 'id, user_id',
			));
			if($device != null) {
				$search = InlisSearchs::model()->find(array(
					'select'    => 'search_id',
					'condition' => 'publish= :publish AND device_id= :device AND search_type= :type AND search_key= :key',
					'params'    => array(
						':publish' => 1,
						':device' => $device->id,
						':type' => $type,
						':key' => $key,
					),
				));
				if($search == null) {
					$data=new InlisSearchs;
					if($device->user_id != 0)
						$data->user_id = $device->user_id;
					$data->device_id = $device->id;
					$data->search_type = $type;
					$data->search_key = $key;
					$data->save();
				}				
			}			
		}
		
		return true;
	}
	
	/**
	 * Lists all models.
	 */
	public static function getKeyword($data, $type=null) 
	{
		$keyword = '';
		$type = $type != null ? $type : 0;
		if($type == 0)
			$keyword = ucwords(strtolower(trim($data['keyword']))).' ('.$data['category'].'), '.SyncWorksheets::getInfo($data['worksheet'], 'Name').' (worksheet)';
			
		else {
			foreach($data as $key => $val) {
				if($val != '')
					$keyword .= ucwords(strtolower(trim($val))).' ('.$key.') ';
			}
		}
		
		return trim($keyword);
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