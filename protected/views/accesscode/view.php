<?php
/* @var $this AccesscodeController */
/* @var $model Accesscode */

$this->breadcrumbs=array(
	'Accesscodes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Accesscode', 'url'=>array('index')),
	array('label'=>'Create Accesscode', 'url'=>array('create')),
	array('label'=>'Update Accesscode', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Accesscode', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Accesscode', 'url'=>array('admin')),
);
?>

<h1>View Accesscode #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'roomId',
		'userId',
		'created',
		'expires',
		'condition',
	),
)); ?>
