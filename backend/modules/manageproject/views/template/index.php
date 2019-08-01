<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><b><i class="fa fa-table"></i> <?= \yii\helpers\Html::encode("Create New Project") ?></b></h4>
</div>
 
<div class="col-md-12" style=" border-radius: 3px; padding: 10px;margin-bottom:10px">
        <strong><i class="fa fa-clone"></i> Clone from existing project templates</strong>
    </div>
<div class="container-fluid">
    
    <?php 
    $options = [
        ['icon' => '', 'title' => Yii::t('chanpan', 'System Templates'), 'url' => yii\helpers\Url::to(['/manageproject/template/get-template']), 'active' => true],
        ['icon' => '', 'title' => Yii::t('chanpan', 'User\'s Generated Templates'), 'url' => yii\helpers\Url::to(['/manageproject/template/get-user-template'])],
    ];

    echo cpn\chanpan\widgets\CNTabWidget::widget([
        'options'=>$options,
        'id'=>'project-template',
        'script'=>"
            
        "
    ]);
?>
</div>
<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    
</script>
<?php \richardfan\widget\JSRegister::end();?>
