<?php
/* @var $this AccesscodeController */
/* @var $model Accesscode */

$this->breadcrumbs=array(
	'Accesscodes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Accesscode', 'url'=>array('index')),
	array('label'=>'Create Accesscode', 'url'=>array('create')),
	array('label'=>'View Accesscode', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Accesscode', 'url'=>array('admin')),
);
?>

<h1>Update Accesscode <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>