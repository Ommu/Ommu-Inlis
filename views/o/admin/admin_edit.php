<?php
/**
 * Inlis Catalogs (inlis-catalogs)
 * @var $this AdminController
 * @var $model InlisCatalogs
 * @var $form CActiveForm
 *
 * @author Putra Sudaryanto <putra@ommu.co>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (www.ommu.co)
 * @created date 29 March 2016, 15:15 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 *
 */

	$this->breadcrumbs=array(
		'Inlis Catalogs'=>array('manage'),
		$model->id=>array('view','id'=>$model->id),
		Yii::t('phrase', 'Update'),
	);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>