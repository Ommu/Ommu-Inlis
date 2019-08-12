<?php
/**
 * ViewController
 * @var $this ViewController
 * @var $model InlisViews
 * @var $form CActiveForm
 *
 * Reference start
 * TOC :
 *	Index
 *	List
 *	Run
 *	toggle
 *
 *	LoadModel
 *	performAjaxValidation
 *
 * @author Putra Sudaryanto <putra@ommu.co>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 3 April 2016, 14:23 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 *
 *----------------------------------------------------------------------------------------------------------
 */

class ViewController extends ControllerApi
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
			$pagesize = trim($_POST['pagesize']);
			$token = trim($_POST['token']);
			$apikey = trim($_POST['apikey']);
			
			$criteria=new CDbCriteria;
			$criteria->with = array(
				'user.view' => array(
					'alias' => 'view',
				),
			);
			$criteria->select = array('t.catalog_id','t.creation_date');
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
			$criteria->group = 't.catalog_id';
			$criteria->order = 't.view_id DESC';
			
			$dataProvider = new CActiveDataProvider('InlisViews', array(
				'criteria'=>$criteria,
				'pagination'=>array(
					'pageSize'=>$pagesize != null && $pagesize != '' ? $pagesize : 20,
				),
			));			
			$model = $dataProvider->getData();
			
			$data = '';
			if(!empty($model)) {
				foreach($model as $key => $item) {
					$title = $item->catalog->Title != null && $item->catalog->Title != '' ? $item->catalog->Title : '-';
					
					$data[] = array(
						'catalog_id'=>$item->catalog_id,
						'creation_date'=>$item->creation_date,
						'title'=>$title,
						'author'=>$item->catalog->Author != null && $item->catalog->Author != '' ? $item->catalog->Author : '-',
						'publisher'=>$item->catalog->Publisher != null && $item->catalog->Publisher != '' ? $item->catalog->Publisher : '-',
						'publish_location'=>$item->catalog->PublishLocation != null && $item->catalog->PublishLocation != '' ? $item->catalog->PublishLocation : '-',
						'publish_year'=>$item->catalog->PublishYear != null && $item->catalog->PublishYear != '' ? $item->catalog->PublishYear : '-',
						'subject'=>$item->catalog->Subject != null && $item->catalog->Subject != '' ? $item->catalog->Subject : '-',
						'bookmark'=>InlisBookmarks::getBookmark($_POST, $item->catalog_id),
						'favourite'=>InlisFavourites::getFavourite($_POST, $item->catalog_id),
						'like'=>InlisLikes::getLike($_POST, $item->catalog_id),
						'share'=>InlisCatalogs::getShareUrl($item->catalog_id, $title),
					);					
				}
			} else
				$data = array();
		
			$pager = OFunction::getDataProviderPager($dataProvider);
			$get = array_merge($_GET, array($pager['pageVar']=>$pager['nextPage']));
			$nextPager = $pager['nextPage'] != 0 ? OFunction::validHostURL(Yii::app()->controller->createUrl('list', $get)) : '-';
			$return = array(
				'data' => $data,
				'pager' => $pager,
				'nextPager' => $nextPager,
			);
				
			$this->_sendResponse(200, CJSON::encode($this->renderJson($return)));
			
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
				$model=InlisViews::model()->findByPk($id);
				
				if($model != null && $model->publish == 1) {
					if($model->user->view->token_password == $token || $model->device->android_id == $apikey) {
						$model->publish = 0;
						if($model->update()) {
							$return = array(
								'success'=>1,
								'message'=>Yii::t('phrase', 'success, view berhasil dihapus'),
							);
						} else {
							$return = array(
								'success'=>0,
								'error'=>'VIEW_NOT_UPDATE',
								'message'=>Yii::t('phrase', 'success, view tidak berhasil dihapus'),
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
						'error'=>'VIEW_IS_NULL',
						'message'=>Yii::t('phrase', 'error, view tidak dalam kondisi publish'),
					);
				}
			} else {
				$return = array(
					'success'=>0,
					'error'=>'VIEW_IS_NULL',
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
		$model = InlisViews::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='inlis-views-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
