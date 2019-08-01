<?php

use kartik\widgets\Select2;
use kartik\helpers\Html;
use yii\web\JsExpression;
use appxq\sdii\utils\SDUtility;

$ids = SDUtility::getMillisecTime();
?>
<div class="row" id="row-<?= $ids ?>">
    <div class="col-md-1 text-right">
        <div id="group-<?= $ids ?>"></div>
    </div>
    <div class="col-md-5"> 
        <?php
        $url = \yii\helpers\Url::to(['main-report/get-list-order']);
        echo Select2::widget([
            'id' => "order-$ids",
            'name' => 'order_code[]',
            'options' => ['placeholder' => 'เลือกค่าใช้จ่าย'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'กำลังค้นหา...'; }"),
                ],
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(city) {
                         return city.text; }'),
                'templateSelection' => new JsExpression('function (city) {return city.text; }'),
            ],
        ]);
        ?>
    </div>
    <div class="col-md-1">
        <?= Html::input('text', 'qty[]', '1', ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-3">
        <?= Html::input('text', 'sumnotpay[]', '', ['class' => 'form-control t1', 'id' => "sumnotpay-$ids"]) ?>
        <input type="hidden" class="form-control" id="sks-<?= $ids ?>" name="sks_code[]" value="">
        <input type="hidden" name="item_group[]" id="group<?= $ids ?>" >
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-link" onclick="remove('row-<?= $ids ?>')"><i class="glyphicon glyphicon-remove" style="color:#ff0000;font-size: 20px"></i></button>
    </div>
    <div class="col-md-4 text-right">
      <span class="text-danger">***กรณีเพิ่มยา</span> เลขที่ใบยา
    </div>
    <div class="col-md-2">
        <input type="number" class="form-control" name="item_reqno[]" value="">
    </div>
    <div class="col-md-1">
ว.แพทย์
    </div>
    <div class="col-md-3">
        <?php
        $urldoctor = \yii\helpers\Url::to(['main-report/get-list-doctor']);
        echo Select2::widget([
            'id' => "doctorcode-$ids",
            'name' => 'doctorcode[]',
            'options' => ['placeholder' => 'ค้นหาแพทย์'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'กำลังค้นหา...'; }"),
                ],
                'ajax' => [
                    'url' => $urldoctor,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(city) {
                         return city.text; }'),
                'templateSelection' => new JsExpression('function (city) {return city.text; }'),
            ],
        ]);
        ?>
    </div>
    <div class="col-md-2">

    </div>
</div>
<script>
    $('#order-<?= $ids ?>').change(function () {
        var res = $(this).text().split("|");
        $("#sumnotpay-<?= $ids ?>").val(res[1]);
        $("#group-<?= $ids ?>").html(res[4]);
        $("#group<?= $ids ?>").val(res[4]);
        $("#sks-<?= $ids ?>").val(res[3]);
        sumprice();
    });
</script>