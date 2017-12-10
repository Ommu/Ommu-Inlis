<?php
/**
 * ViewInlisSyncCollectionloans
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (opensource.ommu.co)
 * @created date 16 May 2016, 14:36 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 * @contact (+62)856-299-4114
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
 * This is the model class for table "_view_inlis_sync_collectionloans".
 *
 * The followings are the available columns in table '_view_inlis_sync_collectionloans':
 * @property string $date_key
 * @property string $loans
 * @property string $loan_collection
 * @property string $returns
 * @property string $return_late
 * @property string $return_collection
 */
class ViewInlisSyncCollectionloans extends CActiveRecord
{
	public $defaultColumns = array();

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ViewInlisSyncCollectionloans the static model class
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
		return '_view_inlis_sync_collectionloans';
	}

	/**
	 * @return string the primarykey column
	 */
	public function primaryKey()
	{
		return 'date_key';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('loans', 'length', 'max'=>21),
			array('loan_collection, return_collection', 'length', 'max'=>32),
			array('returns, return_late', 'length', 'max'=>23),
			array('date_key', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('date_key, loans, loan_collection, returns, return_late, return_collection', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'date_key' => Yii::t('attribute', 'Date Key'),
			'loans' => Yii::t('attribute', 'Loans'),
			'loan_collection' => Yii::t('attribute', 'Loan Collection'),
			'returns' => Yii::t('attribute', 'Returns'),
			'return_late' => Yii::t('attribute', 'Return Late'),
			'return_collection' => Yii::t('attribute', 'Return Collection'),
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

		if($this->date_key != null && !in_array($this->date_key, array('0000-00-00 00:00:00', '0000-00-00')))
			$criteria->compare('date(t.date_key)',date('Y-m-d', strtotime($this->date_key)));
		$criteria->compare('t.loans',strtolower($this->loans),true);
		$criteria->compare('t.loan_collection',strtolower($this->loan_collection),true);
		$criteria->compare('t.returns',strtolower($this->returns),true);
		$criteria->compare('t.return_late',strtolower($this->return_late),true);
		$criteria->compare('t.return_collection',strtolower($this->return_collection),true);

		if(!isset($_GET['ViewInlisSyncCollectionloans_sort']))
			$criteria->order = 't.date_key DESC';

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
			$this->defaultColumns[] = 'date_key';
			$this->defaultColumns[] = 'loans';
			$this->defaultColumns[] = 'loan_collection';
			$this->defaultColumns[] = 'returns';
			$this->defaultColumns[] = 'return_late';
			$this->defaultColumns[] = 'return_collection';
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
			$this->defaultColumns[] = array(
				'name' => 'date_key',
				'value' => 'Utility::dateFormat($data->date_key)',
				'filter' => Yii::app()->controller->widget('application.libraries.core.components.system.CJuiDatePicker', array(
					'model'=>$this,
					'attribute'=>'date_key',
					'language' => 'en',
					'i18nScriptFile' => 'jquery-ui-i18n.min.js',
					//'mode'=>'datetime',
					'htmlOptions' => array(
						'id' => 'date_key_filter',
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
			$this->defaultColumns[] = array(
				'name' => 'loans',
				'value' => '$data->loans != null ? $data->loans : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'loan_collection',
				'value' => '$data->loan_collection != null ? $data->loan_collection : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'returns',
				'value' => '$data->returns != null ? $data->returns : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'return_late',
				'value' => '$data->return_late != null ? $data->return_late : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'return_collection',
				'value' => '$data->return_collection != null ? $data->return_collection : "-"',
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
				'select' => $column
			));
			return $model->$column;
			
		} else {
			$model = self::model()->findByPk($id);
			return $model;			
		}
	}

}