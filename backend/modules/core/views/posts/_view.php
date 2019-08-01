<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?php echo CoreFunc::t('Preview'); ?></h4>
</div>
<div class="modal-body" style="margin-top: -15px;">
	<h3><?php echo CHtml::encode($model->post_title);?></h3>
	<?php echo $model->post_content;?>
</div>
<div class="modal-footer">
	<?php echo SDHtml::SDButton(Yii::t('app', 'Close'), '', '', array('icon' => 'remove', 'data-dismiss' => 'modal')); ?>
</div>