<?php

//use kartik\grid\GridView;
use yii\grid\GridView;

?>

<div class="alert alert-warning"
     style="max-height: 230px; text-align: justify; padding-top: 4px; color: #000 !important; background-color: #FFFF00 !important; ">
    <?= Yii::t('report', 'Stat at a glace as of now') ?> <?= date('D-M-Y') ?>  <?= date('h:i:s') ?>

    <ul>
        <li><?= Yii::t('report', 'Total number of projects') ?> : <b
                    style="color: red"><?= isset($data_sum_project_type['nall']) ? number_format($data_sum_project_type['nall']) : '0' ?></b>
            (<?= Yii::t('report', 'This year') ?> = <b
                    style="color: red"><?= isset($data_sum_project_type['thisYear']) ? number_format($data_sum_project_type['thisYear']) : '0' ?></b>
            ;
            <?= Yii::t('report', 'This month') ?> = <b
                    style="color: red"><?= isset($data_sum_project_type['thisMonth']) ? number_format($data_sum_project_type['thisMonth']) : '0' ?></b>
            ;
            <?= Yii::t('report', 'This week') ?> = <b
                    style="color: red"><?= isset($data_sum_project_type['thisWeek']) ? number_format($data_sum_project_type['thisWeek']) : '0' ?></b>
            ;
            <?= Yii::t('report', 'Today') ?> = <b
                    style="color: red"><?= isset($data_sum_project_type['today']) ? number_format($data_sum_project_type['today']) : '0' ?></b>
            )
            <?php

            ?>
        </li>
        <li><?= Yii::t('report', 'Total number of members') ?> : <b
                    style="color: red"><?= isset($data_sum_user['nall']) ? number_format($data_sum_user['nall']) : '0' ?></b>

            (<?= Yii::t('report', 'This year') ?> = <b
                    style="color: red"><?= isset($data_sum_user['thisYear']) ? number_format($data_sum_user['thisYear']) : '0' ?></b>
            ;
            <?= Yii::t('report', 'This month') ?> = <b
                    style="color: red"><?= isset($data_sum_user['thisMonth']) ? number_format($data_sum_user['thisMonth']) : '0' ?></b>
            ;
            <?= Yii::t('report', 'This week') ?> = <b
                    style="color: red"><?= isset($data_sum_user['thisWeek']) ? number_format($data_sum_user['thisWeek']) : '0' ?></b>
            ;
            <?= Yii::t('report', 'Today') ?> = <b
                    style="color: red"><?= isset($data_sum_user['today']) ? number_format($data_sum_user['today']) : '0' ?></b>
            )
        </li>
    </ul>
</div>
<?php

$modelField = \backend\modules\ezforms2\models\EzformFields::findOne(['ezf_field_id' => '1523071383096379200']);

$data_choice = \appxq\sdii\utils\SDUtility::string2Array($modelField['ezf_field_data']);

$dataInput = null;
$ezf_input = null;
if (!isset(Yii::$app->session['ezf_input'])) {
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}
if (isset(Yii::$app->session['ezf_input'])) {
    $ezf_input = Yii::$app->session['ezf_input'];
    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelField['ezf_field_type'], Yii::$app->session['ezf_input']);
}
//\appxq\sdii\utils\VarDumper::dump($dataInput);


echo \yii\helpers\Html::tag('div', '<h4 class="modal-title">' . Yii::t('report', 'Number of Project Types') . '</h4>', ['class' => 'modal-header', 'style' => 'background-color:#cccccc']);


echo GridView::widget([
    'id' => 'grid-overview-project',
    'layout' => '{items}',
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    'columns' => [
        [
            'attribute' => 'studydesign',
            'label' => 'Project Type',
            'format' => 'raw',
            'value' => function ($model) use ($modelField, $dataInput) {
                if ($model['studydesign'] == '99') {
                    $dataText = 'Unclassifiable';
                } else {
                    $model['id'] = \appxq\sdii\utils\SDUtility::getMillisecTime();
                    $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelField, $model);
                }
                return \yii\helpers\Html::a($dataText, '#', ['class' => 'btn-get-numuser', 'style' => 'text-decoration:none', 'data-type' => $model['studydesign']]);
            },
            'footer' => \yii\helpers\Html::a('<b>'.Yii::t('report', 'Total').'</b>', '#', ['class' => 'btn-get-numuser', 'style' => 'text-decoration:none', 'data-type' => ''])
        ],
        [
            'attribute' => 'nall',
            'label' => Yii::t('report', 'Total'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($data_sum_project_type['nall']).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
        [
            'attribute' => 'thisYear',
            'label' => Yii::t('report', 'This Year'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($data_sum_project_type['thisYear']).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
        [
            'attribute' => 'thisMonth',
            'label' => Yii::t('report', 'This Month'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($data_sum_project_type['thisMonth']).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
        [
            'attribute' => 'thisWeek',
            'label' => Yii::t('report', 'This Week'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($data_sum_project_type['thisWeek']).'</b></div>',
            'headerOptions' => ['style' => "text-align: right;"],
            'contentOptions' => ['style' => "min-width:100px; text-align: right;"],
        ],
        [
            'attribute' => 'today',
            'label' => Yii::t('report', 'Today'),
            'format' => 'decimal',
//            'mergeHeader' => true,
//            'pageSummary' => true,
            'footer' => '<div style="float: right"><b>'.number_format($data_sum_project_type['today']).'</b></div>',
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

echo "<br>";


echo \yii\helpers\Html::beginTag('div', ['id' => 'div-overview-user']);
echo \yii\helpers\Html::tag('div', '<h4 class="modal-title">Number of member by Project (All Type)</h4>', ['class' => 'modal-header', 'style' => 'background-color:#cccccc']);
?>

<?php
$total = 0;
$nall = 0;
$year = 0;
$month = 0;
$week = 0;
$today = 0;
//    \appxq\sdii\utils\VarDumper::dump($dataProviderUser);
if (isset($dataProviderUser->allModels) && is_array($dataProviderUser->allModels) && !empty($dataProviderUser->allModels)) {
    foreach ($dataProviderUser->allModels as $vDataUser) {
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
    'dataProvider' => $dataProviderUser,
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
?>

<?php
\richardfan\widget\JSRegister::begin();
?>
<script>
    $('.btn-get-numuser').click(function () {
        $('html,body').animate({
            scrollTop: $('#div-overview-user').offset().top
        }, 200);
        $('#div-overview-user').html('<div class="text-center"><h4>Loading....</h4></div>');
        $.get('/report/overview/get-num-user?type=' + $(this).attr('data-type'), function (data) {
            $('#div-overview-user').html(data);
        });

    });
</script>
<?php
\richardfan\widget\JSRegister::end();
?>
