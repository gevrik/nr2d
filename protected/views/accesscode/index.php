<?php
/* @var $this AccesscodeController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Accesscodes',
);

$this->menu=array(
	array('label'=>'Create Accesscode', 'url'=>array('create')),
	array('label'=>'Manage Accesscode', 'url'=>array('admin')),
);
?>

<h1>Accesscodes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
