<?php
/**
 * Inlis Searchs (inlis-searchs)
 * @var $this SearchController
 * @var $model InlisSearchs
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (opensource.ommu.co)
 * @created date 11 April 2016, 03:25 WIB
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */

	$this->breadcrumbs=array(
		'Inlis Searchs'=>array('manage'),
		$model->search_id,
	);
?>

<?php //begin.Messages ?>
<?php
if(Yii::app()->user->hasFlash('success'))
	echo Utility::flashSuccess(Yii::app()->user->getFlash('success'));
?>
<?php //end.Messages ?>

<?php $this->widget('application.components.system.FDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array(
			'name'=>'search_id',
			'value'=>$model->search_id,
			//'value'=>$model->search_id != '' ? $model->search_id : '-',
		),
		array(
			'name'=>'publish',
			'value'=>$model->publish == '1' ? Chtml::image(Yii::app()->theme->baseUrl.'/images/icons/publish.png') : Chtml::image(Yii::app()->theme->baseUrl.'/images/icons/unpublish.png'),
			//'value'=>$model->publish,
		),
		array(
			'name'=>'user_id',
			'value'=>$model->user_id,
			//'value'=>$model->user_id != '' ? $model->user_id : '-',
		),
		array(
			'name'=>'search_type',
			'value'=>$model->search_type,
			//'value'=>$model->search_type != '' ? $model->search_type : '-',
		),
		array(
			'name'=>'search_key',
			'value'=>$model->search_key != '' ? $model->search_key : '-',
			//'value'=>$model->search_key != '' ? CHtml::link($model->search_key, Yii::app()->request->baseUrl.'/public/visit/'.$model->search_key, array('target' => '_blank')) : '-',
			'type'=>'raw',
		),
		array(
			'name'=>'creation_date',
			'value'=>!in_array($model->creation_date, array('0000-00-00 00:00:00','1970-01-01 00:00:00')) ? Utility::dateFormat($model->creation_date, true) : '-',
		),
		array(
			'name'=>'creation_ip',
			'value'=>$model->creation_ip,
			//'value'=>$model->creation_ip != '' ? $model->creation_ip : '-',
		),
		array(
			'name'=>'deleted_date',
			'value'=>!in_array($model->deleted_date, array('0000-00-00 00:00:00','1970-01-01 00:00:00')) ? Utility::dateFormat($model->deleted_date, true) : '-',
		),
	),
)); ?>

<div class="dialog-content">
</div>
<div class="dialog-submit">
	<?php echo CHtml::button(Yii::t('phrase', 'Close'), array('id'=>'closed')); ?>
</div>
