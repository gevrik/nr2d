<?php
/* @var $this EntityController */
/* @var $model Entity */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'entity-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'userId'); ?>
		<?php echo $form->textField($model,'userId'); ?>
		<?php echo $form->error($model,'userId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'roomId'); ?>
		<?php echo $form->textField($model,'roomId'); ?>
		<?php echo $form->error($model,'roomId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model,'type',array('size'=>7,'maxlength'=>7)); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'attack'); ?>
		<?php echo $form->textField($model,'attack'); ?>
		<?php echo $form->error($model,'attack'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'defend'); ?>
		<?php echo $form->textField($model,'defend'); ?>
		<?php echo $form->error($model,'defend'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'stealth'); ?>
		<?php echo $form->textField($model,'stealth'); ?>
		<?php echo $form->error($model,'stealth'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'detect'); ?>
		<?php echo $form->textField($model,'detect'); ?>
		<?php echo $form->error($model,'detect'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'eeg'); ?>
		<?php echo $form->textField($model,'eeg'); ?>
		<?php echo $form->error($model,'eeg'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'x'); ?>
		<?php echo $form->textField($model,'x'); ?>
		<?php echo $form->error($model,'x'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'y'); ?>
		<?php echo $form->textField($model,'y'); ?>
		<?php echo $form->error($model,'y'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'credits'); ?>
		<?php echo $form->textField($model,'credits'); ?>
		<?php echo $form->error($model,'credits'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->