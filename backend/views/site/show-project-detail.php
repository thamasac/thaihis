<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h1 class="modal-title"><i class="fa fa-info-circle" aria-hidden="true"></i> Project Details</h1>
</div>
<div class="modal-body">
    <?php  $dataCreate = $model['data_create'];?>
    <?php if(!empty($dataCreate)): ?>
        <h2><?= Yii::t('project', 'Project Summary')?></h2>
        <div>
            <?= $dataCreate['briefsummary']?>
        </div>
        <hr />

        <h2><?= Yii::t('project', 'Project Details')?></h2>
        <div><?= $dataCreate['detail']?></div>
    <?php else: ?>
        <h2 class="alert alert-info"><?= Yii::t('project','Not founc details.')?></h2>
    <?php endif; ?>    
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
