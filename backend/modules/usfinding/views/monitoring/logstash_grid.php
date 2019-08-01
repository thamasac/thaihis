<?php

use kartik\daterange\DateRangePicker;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use Yii;
use yii\bootstrap\Collapse;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\web\JsExpression;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$dataProvider = new ActiveDataProvider([
    'query' => $dataQuery,
    'pagination' => [
        'pageSize' => 20,
    ],

    'sort' => [
        'defaultOrder' => [
            'create_date' => SORT_DESC,
        ]
    ],
]);

$onlinePanel = Html::tag("div",
    "<div class=\"panel-heading\">
                   <h3> จำนวนผู้ใช้งาน </h3>
                 </div>
                 <div class=\"panel-body\">
                    <h3>ผู้ใช้งานขณะนี้ <span>$totalOnline คน.</span></h3> 
                    <h4>ผู้ใช้งานทั้งหมด <span>$totalUsers คน.</span></h4> 
                 </div>",
    ["class" => "panel panel-default", "style" => "float:right;margin-right:15px;width:20%;display:inline-block;"]);

try {

    $findUserInput = Select2::widget([
        'name' => 'user',
        'id' => 'user',
        'value' => isset($userId) ? $userId : "",
        'data' => \yii\helpers\ArrayHelper::map($dataUser, "id", "name"),
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => '/usfinding/default/finduser',
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {search:params.term}; }'),
                //                            'delay' => 50,
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(city) { return city.text; }'),
            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
        ],
        'options' => ['class'=>"float-left mr-1",'placeholder' => 'กรุณาเลือกผู้มีผู้ใช้งาน'],

    ]);
} catch (Exception $e) {
}


try {
    if($startDate != null && $endDate !=null){
       $currentValue = $startDate.' to '.$endDate;
    }
    $dateFilterWidget = DateRangePicker::widget([
            'name' => 'date_range_3',
            'id' => 'dateRange',
            'hideInput' => true,
            'value' => $currentValue,
            'convertFormat' => true,
            'useWithAddon' => true,
            'pluginEvents' => [
                'apply.daterangepicker' => 'function(ev, picker) { start_date = picker.startDate.format(\'YYYY-MM-DD HH:mm:ss\');end_date = picker.endDate.format(\'YYYY-MM-DD HH:mm:ss\');console.log(picker)}'
            ],
            'pluginOptions' => [
                'timePicker' => true,
                'timePickerIncrement' => 10,
                'locale' => ['format' => 'Y-m-d H:i',
                    'separator' => ' to ',
                ]
            ]
        ]);
    $dateFilter = HTML::tag('div', "$dateFilterWidget", ['class' => 'input-group drp-container']);
} catch (Exception $e) {
    echo "DATERANGE NOT WORKING.";
}


$buttonFind = Html::button("Search",["id"=>'searchButton']);
$findUserPanel = Html::tag("div",
    "<div class=\"panel-heading\">
                    <h3>Find User Activity </h3>
                 </div>
                 <div class=\"panel-body\">
                    <h4>เลือกช่วงเวลา</h4>
                    $dateFilter
                    <h4>เลือก User</h4>
                    $findUserInput
                    $buttonFind
                 </div>",
    ["class" => "panel panel-default ", "style" => "float:left;margin-right:15px;width:40%;display: inline-block;"]);

try {
    echo Html::tag("div",
        "<div class=\"panel-heading\">
                    <h1>Monitoring Panel</h1>
                 </div>
                 <div class=\"panel-body\">
                    $onlinePanel
                    $findUserPanel
                 </div>",
        ["class" => "panel panel-default"]);
} catch (Exception $e) {
    echo $e;
}

if (isset($userId)) {
    echo Html::tag(h1, $lastSync);
}


try {
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['attribute' => 'create_date', 'label' => 'Log Date', 'headerOptions' => ['style' => 'width:5%;']],
            ['attribute' => 'name', 'label' => 'Log Name', 'headerOptions' => ['style' => 'width:5%;']],
            ['attribute' => 'input',
                'label' => 'INPUT',
                'headerOptions' => ['style' => 'width:30%;'],
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    return str_replace("\n", "<br>", $model->input);
                }],
            ['attribute' => 'result', 'label' => 'Response', 'headerOptions' => ['style' => 'width:8%;']],
            ['attribute' => 'user_id', 'label' => 'User', 'headerOptions' => ['style' => 'width:8%;'],
                'value' => function ($model, $key, $index, $column) {
                    try {
                        $res = \common\models\UserProfile::find()->where(["user_id" => $model->user_id])->one();
                        return $res ? $res->firstname : "NONE";
                    } catch (Exception $e) {
                        return $model->user_id;
                    }
                }],
            ['attribute' => 'type', 'label' => 'Type', 'headerOptions' => ['style' => 'width:3%;']],
        ],
    ]);
} catch (Exception $e) {
    echo $e;
}

$this->registerJs(<<< JS

var start_date = '$startDate';var end_date = '$endDate';
$('#searchButton').click( function(){
    window.location.href='/usfinding/monitoring/log-stash?user_id='+$('#user').val()+'&start_date='+start_date+'&end_date='+end_date;
});
JS
);
