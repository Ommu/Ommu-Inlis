<?php
/**
 * ViewInlisSyncCheckpoints
 *
 * @author Putra Sudaryanto <putra@ommu.co>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (www.ommu.co)
 * @created date 16 May 2016, 14:36 WIB
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
 * This is the model class for table "_view_inlis_sync_checkpoints".
 *
 * The followings are the available columns in table '_view_inlis_sync_checkpoints':
 * @property string $date_key
 * @property string $checkpoints
 * @property string $checkpoint_member
 * @property string $checkpoint_non_member
 */
class ViewInlisSyncCheckpoints extends CActiveRecord
{
	use GridViewTrait;

	public $defaultColumns = array();

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ViewInlisSyncCheckpoints the static model class
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
		return '_view_inlis_sync_checkpoints';
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
			array('checkpoints', 'length', 'max'=>21),
			array('checkpoint_member, checkpoint_non_member', 'length', 'max'=>23),
			array('date_key', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('date_key, checkpoints, checkpoint_member, checkpoint_non_member', 'safe', 'on'=>'search'),
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
			'checkpoints' => Yii::t('attribute', 'Checkpoints'),
			'checkpoint_member' => Yii::t('attribute', 'Checkpoint Member'),
			'checkpoint_non_member' => Yii::t('attribute', 'Checkpoint Non Member'),
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

		if($this->date_key != null && !in_array($this->date_key, array('0000-00-00 00:00:00','1970-01-01 00:00:00','0002-12-02 07:07:12','-0001-11-30 00:00:00')))
			$criteria->compare('date(t.date_key)', date('Y-m-d', strtotime($this->date_key)));
		$criteria->compare('t.checkpoints', strtolower($this->checkpoints), true);
		$criteria->compare('t.checkpoint_member', strtolower($this->checkpoint_member), true);
		$criteria->compare('t.checkpoint_non_member', strtolower($this->checkpoint_non_member), true);

		if(!Yii::app()->getRequest()->getParam('ViewInlisSyncCheckpoints_sort'))
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
			$this->defaultColumns[] = 'checkpoints';
			$this->defaultColumns[] = 'checkpoint_member';
			$this->defaultColumns[] = 'checkpoint_non_member';
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
				'value' => 'Yii::app()->dateFormatter->formatDateTime($data->date_key, \'medium\', false)',
				'filter' => $this->filterDatepicker($this, 'date_key'),
			);
			$this->defaultColumns[] = array(
				'name' => 'checkpoints',
				'value' => '$data->checkpoints != null ? $data->checkpoints : \'-\'',
			);
			$this->defaultColumns[] = array(
				'name' => 'checkpoint_member',
				'value' => '$data->checkpoint_member != null ? $data->checkpoint_member : \'-\'',
			);
			$this->defaultColumns[] = array(
				'name' => 'checkpoint_non_member',
				'value' => '$data->checkpoint_non_member != null ? $data->checkpoint_non_member : \'-\'',
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

}