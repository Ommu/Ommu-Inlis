<?php
/**
 * SiteController
 * @var $this SiteController
 * @var $model SyncCatalogs
 * @var $form CActiveForm
 *
 * Reference start
 * TOC :
 *	Index
 *	Search
 *	Advanced
 *	Detail
 *	Track
 *	Worksheet
 *
 *	LoadModel
 *	performAjaxValidation
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 30 March 2016, 01:49 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 *
 *----------------------------------------------------------------------------------------------------------
 */

class SiteController extends ControllerApi
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
				'actions'=>array('index','search','advanced','detail','track','worksheet'),
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
	public function actionSearch() 
	{
		if(Yii::app()->request->isPostRequest) {
			$keyword = trim($_POST['keyword']);
			$category = trim($_POST['category']);
			$worksheet = trim($_POST['worksheet']);
			$pagesize = trim($_POST['pagesize']);
			$token = trim($_POST['token']);
			$apikey = trim($_POST['apikey']);
			
			InlisSearchs::insertSearch($_POST);
			
			$criteria=new CDbCriteria;
			$criteria->select = array('ID','Title','Author','Publisher','PublishLocation','PublishYear','Subject','ISBN','CallNumber','Worksheet_id');			
			if($category == 'title')
				$criteria->compare('t.Title',strtolower($keyword),true);
			else if($category == 'author')
				$criteria->compare('t.Author',strtolower($keyword),true);
			else if($category == 'publisher')
				$criteria->compare('t.Publisher',strtolower($keyword),true);
			else if($category == 'publishyear')
				$criteria->compare('t.PublishYear',strtolower($keyword),true);
			else if($category == 'subject')
				$criteria->compare('t.Subject',strtolower($keyword),true);
			else if($category == 'callnumber')
				$criteria->compare('t.CallNumber',strtolower($keyword),true);
			else if($category == 'bibid')
				$criteria->compare('t.BIBID',strtolower($keyword),true);
			else if($category == 'isbn')
				$criteria->compare('t.ISBN',strtolower($keyword),true);
			
			if($worksheet != 0)
				$criteria->compare('t.Worksheet_id',$worksheet);
			
			$criteria->compare('t.IsOPAC',1);
			$criteria->order = 't.ID DESC';
			
			$dataProvider = new CActiveDataProvider('SyncCatalogs', array(
				'criteria'=>$criteria,
				'pagination'=>array(
					'pageSize'=>$pagesize != null && $pagesize != '' ? $pagesize : 20,
				),
			));			
			$model = $dataProvider->getData();
			
			if(!empty($model)) {
				foreach($model as $key => $item) {
					$title = $item->Title != null && $item->Title != '' ? $item->Title : '-';
					
					$data[] = array(
						'id'=>$item->ID,
						'title'=>$title,
						'author'=>$item->Author != null && $item->Author != '' ? $item->Author : '-',
						'publisher'=>$item->Publisher != null && $item->Publisher != '' ? $item->Publisher : '-',
						'publish_location'=>$item->PublishLocation != null && $item->PublishLocation != '' ? $item->PublishLocation : '-',
						'publish_year'=>$item->PublishYear != null && $item->PublishYear != '' ? $item->PublishYear : '-',
						'subject'=>$item->Subject != null && $item->Subject != '' ? $item->Subject : '-',
						'isbn'=>$item->ISBN != null && $item->ISBN != '' ? $item->ISBN : '-',
						'callnumber'=>$item->CallNumber != null && $item->CallNumber != '' ? $item->CallNumber : '-',
						'worksheet'=>$item->worksheet->Name,
						'bookmark'=>InlisBookmarks::getBookmark($_POST, $item->ID),
						'favourite'=>InlisFavourites::getFavourite($_POST, $item->ID),
						'like'=>InlisLikes::getLike($_POST, $item->ID),
						'share'=>InlisCatalogs::getShareUrl($item->ID, $title),
					);					
				}
			} else
				$data = array();
		
			$pager = OFunction::getDataProviderPager($dataProvider);
			$get = array_merge($_GET, array($pager['pageVar']=>$pager['nextPage']));
			$nextPager = $pager['nextPage'] != 0 ? OFunction::validHostURL(Yii::app()->controller->createUrl('search', $get)) : '-';
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
	public function actionAdvanced()
	{
		if(Yii::app()->request->isPostRequest) {
			$title = trim($_POST['title']);
			$author = trim($_POST['author']);
			$publisher = trim($_POST['publisher']);
			$publishyear = trim($_POST['publishyear']);
			$subject = trim($_POST['subject']);
			$callnumber = trim($_POST['callnumber']);
			$bibid = trim($_POST['bibid']);
			$isbn = trim($_POST['isbn']);
			$pagesize = trim($_POST['pagesize']);
			$token = trim($_POST['token']);
			$apikey = trim($_POST['apikey']);
			
			InlisSearchs::insertSearch($_POST, 1);
			
			$criteria=new CDbCriteria;
			$criteria->select = array('ID','Title','Author','Publisher','PublishLocation','PublishYear','Subject','ISBN','CallNumber','Worksheet_id');
			$criteria->compare('t.Title',strtolower($title),true);
			$criteria->compare('t.Author',strtolower($author),true);
			$criteria->compare('t.Publisher',strtolower($publisher),true);
			$criteria->compare('t.PublishYear',strtolower($publishyear),true);
			$criteria->compare('t.Subject',strtolower($subject),true);
			$criteria->compare('t.CallNumber',strtolower($callnumber),true);
			$criteria->compare('t.BIBID',strtolower($bibid),true);
			$criteria->compare('t.ISBN',strtolower($isbn),true);
			$criteria->compare('t.IsOPAC',1);
			$criteria->order = 't.ID DESC';

			$dataProvider = new CActiveDataProvider('SyncCatalogs', array(
				'criteria'=>$criteria,
				'pagination'=>array(
					'pageSize'=>$pagesize != null && $pagesize != '' ? $pagesize : 20,
				),
			));			
			$model = $dataProvider->getData();
			
			$data = '';
			if(!empty($model)) {
				foreach($model as $key => $item) {
					$title = $item->Title != null && $item->Title != '' ? $item->Title : '-';
					
					$data[] = array(
						'id'=>$item->ID,
						'title'=>$title,
						'author'=>$item->Author != null && $item->Author != '' ? $item->Author : '-',
						'publisher'=>$item->Publisher != null && $item->Publisher != '' ? $item->Publisher : '-',
						'publish_location'=>$item->PublishLocation != null && $item->PublishLocation != '' ? $item->PublishLocation : '-',
						'publish_year'=>$item->PublishYear != null && $item->PublishYear != '' ? $item->PublishYear : '-',
						'subject'=>$item->Subject != null && $item->Subject != '' ? $item->Subject : '-',
						'isbn'=>$item->ISBN != null && $item->ISBN != '' ? $item->ISBN : '-',
						'callnumber'=>$item->CallNumber != null && $item->CallNumber != '' ? $item->CallNumber : '-',
						'worksheet'=>$item->worksheet->Name,
						'bookmark'=>InlisBookmarks::getBookmark($_POST, $item->ID),
						'favourite'=>InlisFavourites::getFavourite($_POST, $item->ID),
						'like'=>InlisLikes::getLike($_POST, $item->ID),
						'share'=>InlisCatalogs::getShareUrl($item->ID, $title),
					);
				}
			} else
				$data = array();
		
			$pager = OFunction::getDataProviderPager($dataProvider);
			$get = array_merge($_GET, array($pager['pageVar']=>$pager['nextPage']));
			$nextPager = $pager['nextPage'] != 0 ? OFunction::validHostURL(Yii::app()->controller->createUrl('advanced', $get)) : '-';
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
	public function actionDetail()
	{
		if(Yii::app()->request->isPostRequest) {
			$id = trim($_POST['id']);
			$token = trim($_POST['token']);
			$apikey = trim($_POST['apikey']);
			
			$model = SyncCatalogs::model()->findByPk($id);
			
			if($model != null) {
				$path = '/uploaded_files/sampul_koleksi/original/'.$model->worksheet->Name;
				$cover = Yii::app()->params['inlis_address'].$path.'/'.$model->CoverURL;
				$title = $model->Title != null && $model->Title != '' ? $model->Title : '-';
				
				$return = array(
					'success'=>'1',
					'id'=>$model->ID,
					'title'=>$title,
					'author'=>$model->Author != null && $model->Author != '' ? $model->Author : '-',
					'publisher'=>$model->Publisher != null && $model->Publisher != '' ? $model->Publisher : '-',
					'publish_location'=>$model->PublishLocation != null && $model->PublishLocation != '' ? $model->PublishLocation : '-',
					'publish_year'=>$model->PublishYear != null && $model->PublishYear != '' ? $model->PublishYear : '-',
					'subject'=>$model->Subject != null && $model->Subject != '' ? $model->Subject : '-',
					'isbn'=>$model->ISBN != null && $model->ISBN != '' ? $model->ISBN : '-',
					'callnumber'=>$model->CallNumber != null && $model->CallNumber != '' ? $model->CallNumber : '-',
					'worksheet'=>$model->worksheet->Name,
						
					'edition'=>$model->Edition != null && $model->Edition != '' ? $model->Edition : '-',
					'paging'=>$model->Paging != null && $model->Paging != '' ? $model->Paging : '-',
					'sizes'=>$model->Sizes != null && $model->Sizes != '' ? $model->Sizes : '-',
					'description'=>$model->Description != null && $model->Description != '' ? $model->Description : '-',	
					'note'=>$model->Note != null && $model->Note != '' ? $model->Note : '-',	
					'cover'=>$model->CoverURL != null && $model->CoverURL != '' ? (file_exists($cover) ? $cover : '-') : '-',
					
					'bookmark'=>InlisBookmarks::getBookmark($_POST, $model->ID, true),
					'favourite'=>InlisFavourites::getFavourite($_POST, $model->ID, true),
					'like'=>InlisLikes::getLike($_POST, $model->ID, true),
					'share'=>InlisCatalogs::getShareUrl($model->ID, $title),
				);
				if($token != null && $token != '') {
					$user = ViewUsers::model()->findByAttributes(array('token_password' => $token), array(
						'select' => 'user_id',
					));
					if($user != null) {
						$view = InlisViews::model()->find(array(
							'select'    => 'view_id',
							'condition' => 'publish= :publish AND catalog_id= :catalog AND user_id= :user',
							'params'    => array(
								':publish' => 1,
								':catalog' => $id,
								':user' => $user->user_id,
							),
						));
						if($view == null) {
							$data=new InlisViews;
							$data->catalog_id = $id;
							$data->user_id = $user->user_id;
							$data->save();
						}
						$catalog = InlisCatalogs::model()->findByAttributes(array('catalog_id' => $id));
						$catalog->user_views = $catalog->user_views+1;
						$catalog->save();
					}
				} else {
					$device = UserDevice::model()->findByAttributes(array('android_id' => $apikey), array(
						'select' => 'id, user_id',
					));
					if($device != null) {
						$view = InlisViews::model()->find(array(
							'select'    => 'view_id',
							'condition' => 'publish= :publish AND catalog_id= :catalog AND device_id= :device',
							'params'    => array(
								':publish' => 1,
								':catalog' => $id,
								':device' => $device->id,
							),
						));
						if($view == null) {
							$data=new InlisViews;
							$data->catalog_id = $id;
							if($device->user_id != 0)
								$data->user_id = $device->user_id;
							$data->device_id = $device->id;
							$data->save();
						}
						$catalog = InlisCatalogs::model()->findByAttributes(array('catalog_id' => $id));
						$catalog->public_views = $catalog->public_views+1;
						$catalog->save();
					}
				}
			} else {
				$return = array(
					'success'=>'0',
					'error'=>'NULL',
					'message'=>Yii::t('phrase', 'error, catalog tidak ditemukan'),
				);
			}
			$this->_sendResponse(200, CJSON::encode($this->renderJson($return)));
			
		} else 
			$this->redirect(Yii::app()->createUrl('site/index'));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionTrack()
	{
		if(Yii::app()->request->isPostRequest) {
			$type = trim($_POST['type']);
			$query = trim($_POST['query']);
			$pagesize = trim($_POST['pagesize']);
			$token = trim($_POST['token']);
			$apikey = trim($_POST['apikey']);
			
			$criteria=new CDbCriteria;
			$criteria->select = array('t.catalog_id','t.bookmark_unique','t.favourite_unique','t.like_unique','t.view_unique');
			if($type == 'view')
				$criteria->order = 't.view_unique DESC';
			else if($type == 'bookmark')
				$criteria->order = 't.bookmark_unique DESC';
			else if($type == 'favourite')
				$criteria->order = 't.favourite_unique DESC';
			else if($type == 'like')
				$criteria->order = 't.like_unique DESC';
			
			if($query != null && $query != '' && $query == 'small') {
				$criteria->limit = $pagesize != null && $pagesize != '' ? $pagesize : 10;
				$model = ViewInlisCatalogs::model()->findAll($criteria);
			
				$data = '';
				if(!empty($model)) {
					foreach($model as $key => $item) {
						$path = '/uploaded_files/sampul_koleksi/original/'.$item->catalog->worksheet->Name;
						$cover = Yii::app()->params['inlis_address'].$path.'/'.$item->catalog->CoverURL;
						$title = $item->catalog->Title != null && $item->catalog->Title != '' ? $item->catalog->Title : '-';
						
						$data[] = array(
							'catalog_id'=>$item->catalog_id,
							'count'=>$type != 'view' ? ($type != 'bookmark' ? ($type == 'favourite' ? $item->favourite_unique : $item->like_unique) : $item->bookmark_unique) : $item->view_unique,
							'title'=>$title,
							'author'=>$item->catalog->Author != null && $item->catalog->Author != '' ? $item->catalog->Author : '-',
							'publish_year'=>$item->catalog->PublishYear != null && $item->catalog->PublishYear != '' ? $item->catalog->PublishYear : '-',
							'cover'=>$item->catalog->CoverURL != null && $item->catalog->CoverURL != '' ? (file_exists($cover) ? $cover : '-') : '-',
							'bookmark'=>InlisBookmarks::getBookmark($_POST, $item->catalog_id),
							'favourite'=>InlisFavourites::getFavourite($_POST, $item->catalog_id),
							'like'=>InlisLikes::getLike($_POST, $item->catalog_id),
							'share'=>InlisCatalogs::getShareUrl($item->catalog_id, $title),
						);
					}
				} else
					$data = array();
				$return = $data;
				
			} else {
				$dataProvider = new CActiveDataProvider('ViewInlisCatalogs', array(
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
							'count'=>$type != 'view' ? ($type != 'bookmark' ? ($type == 'favourite' ? $item->favourite_unique : $item->like_unique) : $item->bookmark_unique) : $item->view_unique,
							'title'=>$title,
							'author'=>$item->catalog->Author != null && $item->catalog->Author != '' ? $item->catalog->Author : '-',
							'publish_year'=>$item->catalog->PublishYear != null && $item->catalog->PublishYear != '' ? $item->catalog->PublishYear : '-',
							'publisher'=>$item->catalog->Publisher != null && $item->catalog->Publisher != '' ? $item->catalog->Publisher : '-',
							'publish_location'=>$item->catalog->PublishLocation != null && $item->catalog->PublishLocation != '' ? $item->catalog->PublishLocation : '-',
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
				$nextPager = $pager['nextPage'] != 0 ? OFunction::validHostURL(Yii::app()->controller->createUrl('track', $get)) : '-';
				$return = array(
					'data' => $data,
					'pager' => $pager,
					'nextPager' => $nextPager,
				);
			}
			
			$this->_sendResponse(200, CJSON::encode($this->renderJson($return)));
			
		} else 
			$this->redirect(Yii::app()->createUrl('site/index'));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionWorksheet()
	{
		if(Yii::app()->request->isPostRequest) {			
			$criteria=new CDbCriteria;
			$criteria->select = array('ID','Name');
			$criteria->order = 't.Name ASC';
			$model = SyncWorksheets::model()->findAll($criteria);
			
			$data = '';
			if(!empty($model)) {
				foreach($model as $key => $item) {
					$data[] = array(
						'id'=>$item->ID,
						'name'=>$item->Name,
					);
				}
				$data[] = array(
					'id'=>0,
					'name'=>Yii::t('phrase', 'Semua Jenis Bahan'),
				);
				
			} else
				$data = array();
			
			$this->_sendResponse(200, CJSON::encode($this->renderJson($data)));
			
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
		$model = SyncCatalogs::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='sync-catalogs-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
