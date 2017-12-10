<?php
/**
 * SearchController
 * @var $this SearchController
 * @var $model InlisSearchs
 * @var $form CActiveForm
 * version: 0.0.1
 * Reference start
 *
 * TOC :
 *	Index
 *	List
 *	Run
 *
 *	LoadModel
 *	performAjaxValidation
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 11 April 2016, 03:25 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 * @contect (+62)856-299-4114
 *
 *----------------------------------------------------------------------------------------------------------
 */

class SearchController extends ControllerApi
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';
	public $defaultAction = 'index';	

	/**
	 * @return array action filters
	 */
	public function filters() 
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() 
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','list','run'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
				'expression'=>'isset(Yii::app()->user->level)',
				//'expression'=>'isset(Yii::app()->user->level) && (Yii::app()->user->level != 1)',
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}	
	
	/**
	 * Lists all models.
	 */
	public function actionIndex() 
	{
		$this->redirect(Yii::app()->createUrl('site/index'));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionList()
	{
		if(Yii::app()->request->isPostRequest) {
			$toolbar = trim($_POST['toolbar']);
			$pagesize = trim($_POST['pagesize']);
			$token = trim($_POST['token']);
			$apikey = trim($_POST['apikey']);
			
			$criteria=new CDbCriteria;
			$criteria->with = array(
				'user.view' => array(
					'alias'=>'view',
				),
			);
			$criteria->select = array('t.search_id','t.search_type','t.search_key');
			$criteria->compare('t.publish',1);
			if($token != null && $token != '')
				$criteria->compare('view.token_password',$token);
			else {
				if($apikey != null && $apikey != '') {
					$device = UserDevice::model()->findByAttributes(array('android_id' => $apikey), array(
						'select' => 'id, user_id',
					));
					if($device != null)
						$criteria->compare('t.device_id',$device->id);
				}
			}
			$criteria->group = 't.search_key';
			$criteria->order = 't.search_id DESC';
			
			if($toolbar != null && $toolbar != '' && $toolbar == 'true') {
				$criteria->limit = $pagesize != null && $pagesize != '' ? $pagesize : 5;
				$model = InlisSearchs::model()->findAll($criteria);
				
			} else {
				$dataProvider = new CActiveDataProvider('InlisSearchs', array(
					'criteria'=>$criteria,
					'pagination'=>array(
						'pageSize'=>$pagesize != null && $pagesize != '' ? $pagesize : 20,
					),
				));			
				$model = $dataProvider->getData();				
			}
			
			if(!empty($model)) {
				foreach($model as $key => $item) {
					$data[] = array(
						'search_id'=>$item->search_id,
						'search_type'=>$item->search_type,
						'search_keyword'=>InlisSearchs::getKeyword(unserialize($item->search_key), $item->search_type),
						'search_key'=>$item->search_key,
					);					
				}
			} else
				$data = array();
		
			if($toolbar != null && $toolbar != '' && $toolbar == 'true')
				$this->_sendResponse(200, CJSON::encode($this->renderJson($data)));
				
			else {
				$pager = OFunction::getDataProviderPager($dataProvider);
				$get = array_merge($_GET, array($pager['pageVar']=>$pager['nextPage']));
				$nextPager = $pager['nextPage'] != 0 ? OFunction::validHostURL(Yii::app()->controller->createUrl('list', $get)) : '-';
				$return = array(
					'data' => $data,
					'pager' => $pager,
					'nextPager' => $nextPager,
				);
				
				$this->_sendResponse(200, CJSON::encode($this->renderJson($return)));
			}
			
		} else 
			$this->redirect(Yii::app()->createUrl('site/index'));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionRun()
	{
		if(Yii::app()->request->isPostRequest) {
			$id = trim($_POST['id']);
			$token = trim($_POST['token']);
			$apikey = trim($_POST['apikey']);
			
			if($id != null && $id != '' && $id != 0) {
				$model=InlisSearchs::model()->findByPk($id);
				
				if($model != null && $model->publish == 1) {
					if($model->user->view->token_password == $token || $model->device->android_id == $apikey) {
						$model->publish = 0;
						if($model->update()) {
							$return = array(
								'success'=>1,
								'message'=>Yii::t('phrase', 'success, search history berhasil dihapus'),
							);
						} else {
							$return = array(
								'success'=>0,
								'error'=>'SEARCH_NOT_UPDATE',
								'message'=>Yii::t('phrase', 'success, search history tidak berhasil dihapus'),
							);							
						}					
					} else {
						$return = array(
							'success'=>0,
							'error'=>'USER_ERROR',
							'message'=>Yii::t('phrase', 'error, user tidak diizinkan untuk menghapus'),
						);
					}
				} else {
					$return = array(
						'success'=>0,
						'error'=>'SEARCH_IS_NULL',
						'message'=>Yii::t('phrase', 'error, search tidak dalam kondisi publish'),
					);
				}
			} else {
				$return = array(
					'success'=>0,
					'error'=>'SEARCH_IS_NULL',
					'message'=>Yii::t('phrase', 'error, id tidak ditemukan'),
				);
			}
			
			$this->_sendResponse(200, CJSON::encode($this->renderJson($return)));
			
		} else 
			$this->redirect(Yii::app()->createUrl('site/index'));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) 
	{
		$model = InlisSearchs::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, Yii::t('phrase', 'The requested page does not exist.'));
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model) 
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='inlis-searchs-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
