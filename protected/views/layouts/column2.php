<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>


<div class="row">
	<div class="span3">
		<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Information',
			));
			$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->information,
			'htmlOptions'=>array('class'=>'operations'),
			));
			$this->endWidget();
		?>
	</div>
	<div class="span6 mainContent"><h1 style="text-align: center">NEOCORTEX V2.0</h1><?php echo $content; ?></div>
	<div class="span3">
		<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
			));
			$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
			));
			$this->endWidget();
		?>
	</div>
</div>

<?php $this->endContent(); ?>