<?php
/**
 * ViewInlisSyncMembers
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (opensource.ommu.co)
 * @created date 16 May 2016, 14:37 WIB
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
 * This is the model class for table "_view_inlis_sync_members".
 *
 * The followings are the available columns in table '_view_inlis_sync_members':
 * @property string $date_key
 * @property string $members
 * @property string $member_siswa
 * @property string $member_pelajar
 * @property string $member_mahasiswa
 * @property string $member_karyawan
 * @property string $member_pegawai
 * @property string $member_umum
 */
class ViewInlisSyncMembers extends CActiveRecord
{
	public $defaultColumns = array();

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ViewInlisSyncMembers the static model class
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
		return '_view_inlis_sync_members';
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
			array('members', 'length', 'max'=>21),
			array('member_siswa, member_pelajar, member_mahasiswa, member_karyawan, member_pegawai, member_umum', 'length', 'max'=>23),
			array('date_key', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('date_key, members, member_siswa, member_pelajar, member_mahasiswa, member_karyawan, member_pegawai, member_umum', 'safe', 'on'=>'search'),
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
			'members' => Yii::t('attribute', 'Members'),
			'member_siswa' => Yii::t('attribute', 'Member Siswa'),
			'member_pelajar' => Yii::t('attribute', 'Member Pelajar'),
			'member_mahasiswa' => Yii::t('attribute', 'Member Mahasiswa'),
			'member_karyawan' => Yii::t('attribute', 'Member Karyawan'),
			'member_pegawai' => Yii::t('attribute', 'Member Pegawai'),
			'member_umum' => Yii::t('attribute', 'Member Umum'),
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
		$criteria->compare('t.members',strtolower($this->members),true);
		$criteria->compare('t.member_siswa',strtolower($this->member_siswa),true);
		$criteria->compare('t.member_pelajar',strtolower($this->member_pelajar),true);
		$criteria->compare('t.member_mahasiswa',strtolower($this->member_mahasiswa),true);
		$criteria->compare('t.member_karyawan',strtolower($this->member_karyawan),true);
		$criteria->compare('t.member_pegawai',strtolower($this->member_pegawai),true);
		$criteria->compare('t.member_umum',strtolower($this->member_umum),true);

		if(!isset($_GET['ViewInlisSyncMembers_sort']))
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
			$this->defaultColumns[] = 'members';
			$this->defaultColumns[] = 'member_siswa';
			$this->defaultColumns[] = 'member_pelajar';
			$this->defaultColumns[] = 'member_mahasiswa';
			$this->defaultColumns[] = 'member_karyawan';
			$this->defaultColumns[] = 'member_pegawai';
			$this->defaultColumns[] = 'member_umum';
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
				'name' => 'members',
				'value' => '$data->members != null ? $data->members : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'member_siswa',
				'value' => '$data->member_siswa != null ? $data->member_siswa : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'member_pelajar',
				'value' => '$data->member_pelajar != null ? $data->member_pelajar : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'member_mahasiswa',
				'value' => '$data->member_mahasiswa != null ? $data->member_mahasiswa : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'member_karyawan',
				'value' => '$data->member_karyawan != null ? $data->member_karyawan : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'member_pegawai',
				'value' => '$data->member_pegawai != null ? $data->member_pegawai : "-"',
			);
			$this->defaultColumns[] = array(
				'name' => 'member_umum',
				'value' => '$data->member_umum != null ? $data->member_umum : "-"',
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