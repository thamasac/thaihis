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
                'action' => ['/thaihis/multiple-grid/grid',],
                'method' => 'get',
                'options' => ['data-pjax' => true, 'style' => 'margin-bottom: 0px;', 'data-url' => yii\helpers\Url::to(['/thaihis/multiple-grid/grid', 'reloadDiv' => $reloadDiv, 'options' => $options,'modal'=>$modal])],
                'enableClientValidation' => false,
    ]);
    ?>
    <div class="col-md-4">
        <?php
        echo kartik\widgets\DatePicker::widget([
            'model' => $model,
            'attribute' => 'create_date',
            'type' => kartik\widgets\DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd-mm-yyyy'
            ]
        ]);
        ?>
    </div>
    <div class="col-md-8">
        <div class="input-group">
            <div class="input-group-addon">
                <span><i class="fa fa-user"></i> </span>
            </div>
            <?=
            Html::activeInput('text', $model, 'ptid', ['id' => 'search_drgcounter', 'class' => 'form-control'
                , 'placeholder' => Yii::t('patient', 'Find the form name or hn.')]);
            ?>

            <div class="input-group-btn">
                <?= Html::button(SDHtml::getBtnSearch(), ['class' => 'btn btn-default', 'type' => 'submit']); ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
    $('form#{$model->formName()} input[type=\"text\"]').on('change',function(){
        var url = $('form#{$model->formName()}').attr('data-url');
        var div_content = $('#subcontent-multiple-grid');
        //div_content.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
        $.post(url,{model:$('form#{$model->formName()}').serializeArray()},function(result){
            div_content.html(result);
        });   
    });
    
    $('form#{$model->formName()}').submit(function(){
        var url = $('form#{$model->formName()}').attr('data-url');
        var div_content = $('#subcontent-multiple-grid');
        //div_content.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
        $.post(url,{model:$('form#{$model->formName()}').serializeArray()},function(result){
            div_content.html(result);
        });   
        
        return false;
    });

    
");
?>
