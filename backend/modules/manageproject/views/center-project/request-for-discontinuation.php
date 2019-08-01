<?php
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\bootstrap\ActiveForm;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4><?= Yii::t('project', 'Request for Discontinuation') ?> <?= $project_name?></h4>
</div>

<?php
$form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => $model->formName(),
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-md-2',
                    'offset' => 'col-sm-offset-2',
                    'wrapper' => 'col-md-8',
                ],
            ],
        ]);
?>
<div class="modal-body">
    <div>
         
        <div class="form-group field-discontinuatios-descriptions">
            <label class="control-label col-md-2" for="discontinuatios-project_name"><?= Yii::t('project','Project Name')?></label>
            <div class="col-md-8">
              <?= $project_name; ?>
            </div>

        </div>
        <div class="form-group field-discontinuatios-descriptions">
            <label class="control-label col-md-2" for="discontinuatios-project_name"><?= Yii::t('project','Project Aconym')?></label>
            <div class="col-md-8">
              <?= $project_aconym; ?>
            </div>

        </div>
        <?php 
            $model->descriptions = isset($model->descriptions) ? $model->descriptions : Yii::t('project', 'Unspecified description');
            $model->project_type = $project_type;
            $model->project_id   = $project_id;
         ?>
        <?= $form->field($model, 'descriptions')->textarea(['rows' => '5', 'autofocus' => true]) ?>
        <?= $form->field($model, 'project_type')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'project_id')->hiddenInput()->label(false) ?>
    </div>
</div> 
<div class="modal-footer">
    <div class="col-md-6 col-md-offset-3">
        <?= \yii\bootstrap\Html::submitInput(Yii::t('project', 'Submit'), ['class'=>'btn btn-block btn-lg btn-primary'])?>
    </div>
</div>
<?php ActiveForm::end() ?>  
<?php $modalf = 'discon-modal';?>
<?php richardfan\widget\JSRegister::begin(); ?>
<script>
    $('form#<?= $model->formName()?>').on('beforeSubmit', function(e) {
        var $form = $(this);
        $.post(
            $form.attr('action'), //serialize Yii2 form
            $form.serialize()
        ).done(function(result) {
            if(result.status == 'success') {
                <?= SDNoty::show('result.message', 'result.status')?>
                $(document).find('#<?=$modalf?>').modal('hide');
                //$.pjax.reload({container:'#order-grid-pjax'});
            } else {
                <?= SDNoty::show('result.message', 'result.status')?>
            } 
        }).fail(function() {
            <?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');
        });
        return false;
    });
</script>
<?php richardfan\widget\JSRegister::end(); ?> 

<?php appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    @media (min-width: 768px){
        .form-horizontal .control-label {
            font-size: 12pt;
        }
    }
 
</style>
<?php appxq\sdii\widgets\CSSRegister::end();?>

