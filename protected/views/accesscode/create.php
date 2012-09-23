<?php
/* @var $this AccesscodeController */
/* @var $model Accesscode */

$this->breadcrumbs=array(
	'Accesscodes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Accesscode', 'url'=>array('index')),
	array('label'=>'Manage Accesscode', 'url'=>array('admin')),
);
?>

<h1>Create Accesscode</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>