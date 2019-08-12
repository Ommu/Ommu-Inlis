<?php
/**
 * LoanController
 * @var $this LoanController
 * @var $model InlisViews
 * @var $form CActiveForm
 *
 * Reference start
 * TOC :
 *	Index
 *	Popular
 *
 *	LoadModel
 *	performAjaxValidation
 *
 * @author Putra Sudaryanto <putra@ommu.co>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (www.ommu.co)
 * @created date 3 April 2016, 14:23 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 *
 *----------------------------------------------------------------------------------------------------------
 */

class LoanController extends ControllerApi
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
				'actions'=>array('index','popular'),
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
	public function actionPopular()
	{
		if(Yii::app()->request->isPostRequest) 
		{
			$now = trim($_POST['now']);
			$curdate = trim($_POST['curdate']);
			$query = trim($_POST['query']);
			$pagesize = trim($_POST['pagesize']);
			$token = trim($_POST['token']);
			$apikey = trim($_POST['apikey']);
			
			$criteria=new CDbCriteria;
			$criteria->together = true;
			$criteria->with = array(
				'collection' => array(
					'alias' => 'collection',
				),
			);
			$criteria->select = array('Collection_id, collection.Catalog_id as Catalog_id','COUNT(CollectionLoan_id) as loans');
			if($now != null && $now != '' && $now == 'true') {
				if($curdate != null && $curdate != '') {
					$criteria->condition = 'DATE(t.LoanDate) BETWEEN DATE_SUB(DATE(:curdate), INTERVAL 30 DAY) AND DATE(:curdate)';
					$criteria->params = array(':curdate'=>date('Y-m-d', strtotime($curdate)));
				} else 
					$criteria->condition = 'DATE(t.LoanDate) BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()';
			}
			$criteria->group = 'collection.Catalog_id';
			$criteria->order = 'loans DESC';
			
			if($query != null && $query != '' && $query == 'small') {
				$criteria->limit = $pagesize != null && $pagesize != '' ? $pagesize : 10;
				$model = SyncCollectionloanitems::model()->findAll($criteria);
			
				if(!empty($model)) {
					foreach($model as $key => $item) {
						$path = '/uploaded_files/sampul_koleksi/original/'.$item->collection->catalog->worksheet->Name;
						$cover = Yii::app()->params['inlis_address'].$path.'/'.$item->collection->catalog->CoverURL;
						$title = $item->collection->catalog->Title != null && $item->collection->catalog->Title != '' ? $item->collection->catalog->Title : '-';
						
						$data[] = array(
							'catalog_id'=>$item->collection->Catalog_id,
							'count'=>$item->loans,
							'title'=>$title,
							'author'=>$item->collection->catalog->Author != null && $item->collection->catalog->Author != '' ? $item->collection->catalog->Author : '-',
							'publish_year'=>$item->collection->catalog->PublishYear != null && $item->collection->catalog->PublishYear != '' ? $item->collection->catalog->PublishYear : '-',
							'cover'=>$item->collection->catalog->CoverURL != null && $item->collection->catalog->CoverURL != '' ? (file_exists($cover) ? $cover : '-') : '-',
							'bookmark'=>InlisBookmarks::getBookmark($_POST, $item->collection->Catalog_id),
							'favourite'=>InlisFavourites::getFavourite($_POST, $item->collection->Catalog_id),
							'like'=>InlisLikes::getLike($_POST, $item->collection->Catalog_id),
							'share'=>InlisCatalogs::getShareUrl($item->collection->Catalog_id, $title),
						);
					}
					
				} else
					$data = array();
				$return = $data;
				
			} else {			
				$dataProvider = new CActiveDataProvider('SyncCollectionloanitems', array(
					'criteria'=>$criteria,
					'pagination'=>array(
						'pageSize'=>$pagesize != null && $pagesize != '' ? $pagesize : 20,
						
					),
				));
				$model = $dataProvider->getData();
			
				if(!empty($model)) {
					foreach($model as $key => $item) {	
						$title = $item->collection->catalog->Title != null && $item->collection->catalog->Title != '' ? $item->collection->catalog->Title : '-';
						
						$data[] = array(
							'catalog_id'=>$item->collection->Catalog_id,
							'count'=>$item->loans,
							'title'=>$title,
							'author'=>$item->collection->catalog->Author != null && $item->collection->catalog->Author != '' ? $item->collection->catalog->Author : '-',
							'publish_year'=>$item->collection->catalog->PublishYear != null && $item->collection->catalog->PublishYear != '' ? $item->collection->catalog->PublishYear : '-',
							'publisher'=>$item->collection->catalog->Publisher != null && $item->collection->catalog->Publisher != '' ? $item->collection->catalog->Publisher : '-',
							'publish_location'=>$item->collection->catalog->PublishLocation != null && $item->collection->catalog->PublishLocation != '' ? $item->collection->catalog->PublishLocation : '-',
							'subject'=>$item->collection->catalog->Subject != null && $item->collection->catalog->Subject != '' ? $item->collection->catalog->Subject : '-',
							'bookmark'=>InlisBookmarks::getBookmark($_POST, $item->collection->Catalog_id),
							'favourite'=>InlisFavourites::getFavourite($_POST, $item->collection->Catalog_id),
							'like'=>InlisLikes::getLike($_POST, $item->collection->Catalog_id),
							'share'=>InlisCatalogs::getShareUrl($item->collection->Catalog_id, $title),
						);
					}
					
				} else
					$data = array();
			
				$pager = OFunction::getDataProviderPager($dataProvider);
				$get = array_merge($_GET, array($pager['pageVar']=>$pager['nextPage']));
				$nextPager = $pager['nextPage'] != 0 ? OFunction::validHostURL(Yii::app()->controller->createUrl('popular', $get)) : '-';
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
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) 
	{
		$model = SyncCollectionloans::model()->findByPk($id);
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
