<?php
/**
 * View Inlis Sync Collections (view-inlis-sync-collections)
 * @var $this CollectionController
 * @var $model ViewInlisSyncCollections
 * @var $form CActiveForm
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (opensource.ommu.co)
 * @created date 17 May 2016, 17:25 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
 * @contact (+62)856-299-4114
 *
 */
?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<ul>
		<li>
			<?php echo $model->getAttributeLabel('date_key'); ?><br/>
			<?php echo $form->textField($model,'date_key'); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('collections'); ?><br/>
			<?php echo $form->textField($model,'collections',array('size'=>21,'maxlength'=>21)); ?>
		</li>

		<li class="submit">
			<?php echo CHtml::submitButton(Yii::t('phrase', 'Search')); ?>
		</li>
	</ul>
<?php $this->endWidget(); ?>
