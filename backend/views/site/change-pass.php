<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$this->title = Yii::t('chanpan', 'Change password'); 
//\appxq\sdii\utils\VarDumper::dump($model);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><i class='mdi mdi-lock-outline'></i> <?= Html::encode($this->title) ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?php
            $form = ActiveForm::begin([
                        'id' => 'frmChangePassword',
                        'layout' => 'horizontal',
                        'enableAjaxValidation' => false,
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-sm-offset-4 col-lg-8\">{error}\n{hint}</div>",
                            'labelOptions' => ['class' => 'col-lg-4 control-label'],
                        ],
                    ])
            ?>
            <div>
                <?= $form->field($model, 'new_password')->passwordInput(['autofocus'=>true]); ?>
            </div>
            <div>
                <?= $form->field($model, 'confirm_password')->passwordInput(); ?>
            </div>
            <div class="pull-right">
                <button type="submit" class="btn btn-primary btn-block"><?= Yii::t('chanpan', 'Change') ?></button>                    
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php richardfan\widget\JSRegister::begin();?>
<script>
    $('#frmChangePassword').on('beforeSubmit', function(e){
        let url = '<?= yii\helpers\Url::to(['/site/change-password'])?>';
        let form = $(this);
        $.post(url,form.serialize(), function(result){
            //console.log(result);
            if(result.status == 'success'){
                $('#modal-change-password').modal('hide');
                <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
            }
            return false;
        }).fail(function(){
            
        });
        return false;
    });
</script>
<?php richardfan\widget\JSRegister::end(); ?>
<?php appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    .modal-header {
        padding: 10px;
        border-bottom: 1px solid #a49d9d52;
        background: #cccccc3b;
    }
</style>
<?php appxq\sdii\widgets\CSSRegister::end();?>
 