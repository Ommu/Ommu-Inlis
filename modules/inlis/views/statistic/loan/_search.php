<?php
/**
 * View Inlis Sync Collectionloans (view-inlis-sync-collectionloans)
 * @var $this LoanController
 * @var $model ViewInlisSyncCollectionloans
 * @var $form CActiveForm
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (opensource.ommu.co)
 * @created date 17 May 2016, 17:24 WIB
 * @link http://company.ommu.co
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
			<?php echo $model->getAttributeLabel('loans'); ?><br/>
			<?php echo $form->textField($model,'loans',array('size'=>21,'maxlength'=>21)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('loan_collection'); ?><br/>
			<?php echo $form->textField($model,'loan_collection',array('size'=>32,'maxlength'=>32)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('returns'); ?><br/>
			<?php echo $form->textField($model,'returns',array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('return_late'); ?><br/>
			<?php echo $form->textField($model,'return_late',array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('return_collection'); ?><br/>
			<?php echo $form->textField($model,'return_collection',array('size'=>32,'maxlength'=>32)); ?>
		</li>

		<li class="submit">
			<?php echo CHtml::submitButton(Yii::t('phrase', 'Search')); ?>
		</li>
	</ul>
<?php $this->endWidget(); ?>
