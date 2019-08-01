<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-title"><i class="fa fa-globe"></i> <label style="font-size:16px;"><?= Yii::t('app','Site Navigator')?></label>
        <?php if(Yii::$app->user->can('administrator')): ?>
            <a target="_BLANK" class="btn btn-primary btn-xs" href="<?= yii\helpers\Url::to(['/core/core-options/config?term=project_setup_site_navigator_label'])?>"><i class="fa fa-pencil"></i></a> 
        <?php endif; ?>
    </div>
</div>
<div class="modal-body">
   <?php if(Yii::$app->user->can('administrator')): ?>
    <div class="pull-right">
         <a target="_BLANK" class="btn  btn-primary btn-xs" href="<?= yii\helpers\Url::to(['/core/core-options/config?term=project_setup_site_navigator'])?>"><i class="fa fa-pencil"></i></a> 
    </div>
    <?php endif; ?>
    
   <?php echo isset(Yii::$app->params['project_setup_site_navigator'])?Yii::$app->params['project_setup_site_navigator']:''?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

