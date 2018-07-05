<?php
/**
 * View Inlis Sync Members (view-inlis-sync-members)
 * @var $this MemberController
 * @var $model ViewInlisSyncMembers
 * @var $form CActiveForm
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (www.ommu.co)
 * @created date 17 May 2016, 17:25 WIB
 * @link https://github.com/ommu/ommu-inlis-sso
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
			<?php echo $model->getAttributeLabel('members'); ?><br/>
			<?php echo $form->textField($model,'members', array('size'=>21,'maxlength'=>21)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('member_siswa'); ?><br/>
			<?php echo $form->textField($model,'member_siswa', array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('member_pelajar'); ?><br/>
			<?php echo $form->textField($model,'member_pelajar', array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('member_mahasiswa'); ?><br/>
			<?php echo $form->textField($model,'member_mahasiswa', array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('member_karyawan'); ?><br/>
			<?php echo $form->textField($model,'member_karyawan', array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('member_pegawai'); ?><br/>
			<?php echo $form->textField($model,'member_pegawai', array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('member_umum'); ?><br/>
			<?php echo $form->textField($model,'member_umum', array('size'=>23,'maxlength'=>23)); ?>
		</li>

		<li class="submit">
			<?php echo CHtml::submitButton(Yii::t('phrase', 'Search')); ?>
		</li>
	</ul>
<?php $this->endWidget(); ?>
