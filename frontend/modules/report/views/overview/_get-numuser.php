<?php
use yii\grid\GridView;


$modelField = \backend\modules\ezforms2\models\EzformFields::findOne(['ezf_field_id' => '1523071383096379200']);

$data_choice = \appxq\sdii\utils\SDUtility::string2Array($modelField['ezf_field_data']);
$txt_type = $type == '99' ? 'Unclassifiable' : isset($data_choice['items'][$type]) ? $data_choice['items'][$type] : 'All Type';

echo \yii\helpers\Html::beginTag('div',['id'=>'div-overview-user']);
echo \yii\helpers\Html::tag('div','<h4 class="modal-title">Number of member by Project ('.$txt_type.')</h4>',['class' => 'modal-header','style'=>'background-color:#cccccc']);
$total = 0;
$nall = 0;
$year = 0;
$month = 0;
$week = 0;
$today = 0;
//    \appxq\sdii\utils\VarDumper::dump($dataProviderUser);
if (isset($dataProvider->allModels) && is_array($dataProvider->allModels) && !empty($dataProvider->allModels)) {
    foreach ($dataProvider->allModels as $vDataUser) {
        $nall += (int)$vDataUser['nall'];
        $year += (int)$vDataUser['thisYear'];
        $month += (int)$vDataUser['thisMonth'];
        $week += (int)$vDataUser['thisWeek'];
        $today += (int)$vDataUser['today'];
        $total += (int)$vDataUser['nall'];


    }
}

echo GridView::widget([
    'id' => 'grid-overview-user',
    'layout' => '{items}{pager}',
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    'columns' => [
        [
            'attribute' => 'project_name',
            'label' => Yii::t('report', 'Project Name'),
            'footer' => '<b>Total</b>',
//            'value' => function ($model) use ($modelField,$dataInput) {
//                if($model['studydesign'] == '99'){
//                    $dataText = 'Unclassifiable';
//                }else{
//                    $model['id'] = \appxq\sdii\utils\SDUtility::getMillisecTime();
//                    $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelField, $model);
//                }
//                return $dataText;
//            },
//            'pageSummary' => Yii::t('report', 'Total')
        ],
        [
            'attribute' => 'nall',
            'label' => Yii::t('report', 'Total'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($total).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
        [
            'attribute' => 'thisYear',
            'label' => Yii::t('report', 'This Year'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($year).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
        [
            'attribute' => 'thisMonth',
            'label' => Yii::t('report', 'This Month'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($month).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
        [
            'attribute' => 'thisWeek',
            'label' => Yii::t('report', 'This Week'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($week).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
        [
            'attribute' => 'today',
            'label' => Yii::t('report', 'Today'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($today).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
    ],
    'tableOptions' => [
        'class' => 'table table-hover'
    ],
//    'summary' => false,
//    'showPageSummary' => true,
]);
echo \yii\helpers\Html::endTag('div');