<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use kartik\widgets\Select2;
use yii\web\JsExpression;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="ezf-search" style="padding: 5px;">  
    <?php
    $form = ActiveForm::begin([
                'id' => 'search-' . $model->formName(),
                'action' => ['/reports/report-admin/grid'],
                'method' => 'POST',
                'options' => ['class' => 'form-horizontal'],
    ]);
    ?>
    <div class="form-group">
        <div class="col-md-3">
            <label class="control-label">วันที่</label>
            <?php
            echo \kartik\daterange\DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'create_date',
                'convertFormat' => true,
                //'useWithAddon'=>true,
                'options' => ['id' => 'create_date', 'class' => 'form-control', 'placeholder' => Yii::t('patient', 'Date')],
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'd-m-Y',
                        'separator' => ',',
                    ],
                ]
            ]);
            ?>
        </div>
        <div class="col-md-1">
            <label class="control-label">ค้นหา</label>
            <?=
            Html::submitButton('Search', ['id' => 'btn-search', 'class' => 'form-control btn btn-success'
                , 'data-action' => 'search']);
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
$('form#search-{$model->formName()}').on('beforeSubmit', function(e) {
    let url = $('#search-{$model->formName()}').attr('action');
    actionGet(url,'',$('#search-{$model->formName()}').serialize());
    return false;
});

");
?>