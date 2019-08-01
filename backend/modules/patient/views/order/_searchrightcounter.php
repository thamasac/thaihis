<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDHtml;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?> 
<div class="ordercounter-search col-md-12">
    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
                'action' => ['/patient/order/right-counter', 'reloadDiv' => $reloadDiv],
                'method' => 'post',
                'options' => ['style' => 'margin-bottom: 0px;'],
                'enableClientValidation' => false,
    ]);
    
    ?>
  <div class="row">
	<div class="col-md-4 ">
          <div class="form-group">
                <div class='input-group'>
                    
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                  <?php
            echo trntv\yii\datetimepicker\DatetimepickerWidget::widget([
                'name'=>'date',
                'value'=>$date,
                'options'=>['id'=>'date_filter'],
                'phpDatetimeFormat' => 'yyyy-MM-dd',
                'momentDatetimeFormat' => 'YYYY-MM-DD',
                'clientOptions' => [
                    'format' => 'YYYY-MM-DD',
                    'sideBySide' => true,
                ],
            ]);
          ?>
                </div>
            </div>
          
        </div>
          <div class="col-md-8 sdbox-col">
            <div class="input-group">
        <div class="input-group-addon">
            <span><i class="fa fa-user"></i> <?= Yii::t('patient', 'Find') ?> </span>
        </div>
        <?=
        Html::activeInput('text', $model, 'right_code', ['id' => 'search_rightcounter', 'class' => 'form-control'
            , 'placeholder' => Yii::t('patient', 'Find the form name or hn.')]);
        ?>

        <div class="input-group-btn">
            <?= Html::button(SDHtml::getBtnSearch(), ['id'=>'submit_search', 'class' => 'btn btn-default', 'type' => 'submit']); ?>
        </div>
    </div>
        </div>
</div>
    
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
     $('#date_filter').on('dp.change' ,function(){
        $('#submit_search').click();
    });
    
    $('#search_rightcounter').select();
    
    $('#modal-ezform-main').on('hidden.bs.modal', function (e) {
        $('#search_rightcounter').select();
    });

    $('form#{$model->formName()}').on('beforeSubmit', function(e) {
        var \$form = $(this);
        $.post(
            \$form.attr('action'), //serialize Yii2 form
            \$form.serialize()
        ).done(function(result) {
            $('#$reloadDiv').html(result);
        }).fail(function() {
            console.log('server error');
        });
        return false;
    });
");
?>
