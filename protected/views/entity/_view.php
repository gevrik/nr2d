<?php
/* @var $this EntityController */
/* @var $data Entity */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('userId')); ?>:</b>
	<?php echo CHtml::encode($data->userId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('roomId')); ?>:</b>
	<?php echo CHtml::encode($data->roomId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('attack')); ?>:</b>
	<?php echo CHtml::encode($data->attack); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('defend')); ?>:</b>
	<?php echo CHtml::encode($data->defend); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('stealth')); ?>:</b>
	<?php echo CHtml::encode($data->stealth); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('detect')); ?>:</b>
	<?php echo CHtml::encode($data->detect); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('eeg')); ?>:</b>
	<?php echo CHtml::encode($data->eeg); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('x')); ?>:</b>
	<?php echo CHtml::encode($data->x); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('y')); ?>:</b>
	<?php echo CHtml::encode($data->y); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('credits')); ?>:</b>
	<?php echo CHtml::encode($data->credits); ?>
	<br />

	*/ ?>

</div>