<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css" />
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/bootstrap.js'); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/site.css" />
  	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/site.js'); ?>
  	<?php Yii::app()->clientScript->registerScript('init', 'nr.init();', CClientScript::POS_READY); ?>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

	<div class="container">
		<?php echo $content; ?>
	</div>

</body>
</html>
