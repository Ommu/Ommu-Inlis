<?php
/**
 * Inlis Locations (inlis-locations)
 * @var $this LocationController
 * @var $model InlisLocations
 * @var $dataProvider CActiveDataProvider
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 29 March 2016, 11:07 WIB
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */

	$this->breadcrumbs=array(
		'Inlis Locations',
	);
?>

<?php $this->widget('application.components.system.FListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'pager' => array(
		'header' => '',
	), 
	'summaryText' => '',
	'itemsCssClass' => 'items clearfix',
	'pagerCssClass'=>'pager clearfix',
)); ?>
