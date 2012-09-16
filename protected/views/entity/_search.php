<?php
/* @var $this EntityController */
/* @var $model Entity */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'userId'); ?>
		<?php echo $form->textField($model,'userId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'roomId'); ?>
		<?php echo $form->textField($model,'roomId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'type'); ?>
		<?php echo $form->textField($model,'type',array('size'=>7,'maxlength'=>7)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'attack'); ?>
		<?php echo $form->textField($model,'attack'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'defend'); ?>
		<?php echo $form->textField($model,'defend'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'stealth'); ?>
		<?php echo $form->textField($model,'stealth'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'detect'); ?>
		<?php echo $form->textField($model,'detect'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'eeg'); ?>
		<?php echo $form->textField($model,'eeg'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'x'); ?>
		<?php echo $form->textField($model,'x'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'y'); ?>
		<?php echo $form->textField($model,'y'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'credits'); ?>
		<?php echo $form->textField($model,'credits'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->