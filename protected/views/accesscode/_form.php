<?php
/* @var $this AccesscodeController */
/* @var $model Accesscode */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'accesscode-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'roomId'); ?>
		<?php echo $form->textField($model,'roomId'); ?>
		<?php echo $form->error($model,'roomId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'userId'); ?>
		<?php echo $form->textField($model,'userId'); ?>
		<?php echo $form->error($model,'userId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'expires'); ?>
		<?php echo $form->textField($model,'expires'); ?>
		<?php echo $form->error($model,'expires'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'condition'); ?>
		<?php echo $form->textField($model,'condition'); ?>
		<?php echo $form->error($model,'condition'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->