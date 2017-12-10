<?php
/**
 * View Inlis Sync Checkpoints (view-inlis-sync-checkpoints)
 * @var $this CheckpointController
 * @var $model ViewInlisSyncCheckpoints
 * @var $form CActiveForm
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (opensource.ommu.co)
 * @created date 17 May 2016, 17:24 WIB
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
			<?php echo $model->getAttributeLabel('checkpoints'); ?><br/>
			<?php echo $form->textField($model,'checkpoints',array('size'=>21,'maxlength'=>21)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('checkpoint_member'); ?><br/>
			<?php echo $form->textField($model,'checkpoint_member',array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('checkpoint_non_member'); ?><br/>
			<?php echo $form->textField($model,'checkpoint_non_member',array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li class="submit">
			<?php echo CHtml::submitButton(Yii::t('phrase', 'Search')); ?>
		</li>
	</ul>
<?php $this->endWidget(); ?>
