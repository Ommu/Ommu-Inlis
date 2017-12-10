<?php
/**
 * Inlis Catalogs (inlis-catalogs)
 * @var $this AdminController
 * @var $model InlisCatalogs
 * @var $form CActiveForm
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (opensource.ommu.co)
 * @created date 29 March 2016, 15:15 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 * @contact (+62)856-299-4114
 *
 */

	$this->breadcrumbs=array(
		'Inlis Catalogs'=>array('manage'),
		$model->id=>array('view','id'=>$model->id),
		'Update',
	);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>