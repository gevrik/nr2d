<?php
/* @var $this EntityController */
/* @var $model Entity */

$this->breadcrumbs=array(
	'Entities'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Entity', 'url'=>array('index')),
	array('label'=>'Create Entity', 'url'=>array('create')),
	array('label'=>'Update Entity', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Entity', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Entity', 'url'=>array('admin')),
);
?>

<h1>View Entity #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'userId',
		'roomId',
		'type',
		'created',
		'attack',
		'defend',
		'stealth',
		'detect',
		'eeg',
		'x',
		'y',
		'credits',
	),
)); ?>
