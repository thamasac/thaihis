<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$id = \appxq\sdii\utils\SDUtility::getMillisecTime();


echo Html::beginTag('div', ['class' => 'alert alert-warning']);

echo Html::tag('div', 'สรุปยอดข้อมูล ณ <b>' . \appxq\sdii\utils\SDdate::mysql2phpThDate(date('Y-m-d')) . '	เวลา ' . date('h:i:s') . ' น. </b>');

?>

<ul>
    <li>
        ทั้งหมด <span style="color: red" ><?=isset($dataWorkingAll['nall']) && $dataWorkingAll['nall'] != '' ? number_format($dataWorkingAll['nall']) : '0'?></span> ราย
        ปีนี้ <span style="color: red" ><?=isset($dataWorkingAll['thisYear']) && $dataWorkingAll['thisYear'] != '' ? number_format($dataWorkingAll['thisYear']) : '0'?></span> ราย
        เดือนนี้ <span style="color: red" ><?=isset($dataWorkingAll['thisMonth']) && $dataWorkingAll['thisMonth'] != '' ? number_format($dataWorkingAll['thisMonth']) : '0'?></span> ราย
        สัปดาห์นี้ <span style="color: red" ><?=isset($dataWorkingAll['thisWeek']) && $dataWorkingAll['thisWeek'] != '' ? number_format($dataWorkingAll['thisWeek']) : '0'?></span> ราย
        วันนี้ <span style="color: red" ><?=isset($dataWorkingAll['today']) && $dataWorkingAll['today'] != '' ? number_format($dataWorkingAll['today']) : '0'?></span> ราย
    </li>
</ul>

<?php

echo Html::endTag('div');

echo GridView::widget([
    'id' => 'grid-hos-monitor-' . $id,
    'dataProvider' => $dataProvider,
//    'filterModel' => true,
    'layout' => '{items}',
    'columns' => [

        [
            'attribute' => 'unit_name',
            'label' => 'แผนก',
            'pageSummary' => 'รวมทั้งหมด'
        ],
        [
            'attribute' => 'nall',
            'label' => 'ทั้งหมด',
            'format' => 'decimal',
            'mergeHeader' => true,
            'pageSummary' => true,
            'footer' => true
        ],
        [
            'attribute' => 'thisYear',
            'label' => 'ปีนี้',
            'format' => 'decimal',
            'mergeHeader' => true,
            'pageSummary' => true,
            'footer' => true
        ],
        [
            'attribute' => 'thisMonth',
            'label' => 'เดือนนี้',
            'format' => 'decimal',
            'mergeHeader' => true,
            'pageSummary' => true,
            'footer' => true
        ],
        [
            'attribute' => 'thisWeek',
            'label' => 'สัปดาห์นี้',
            'format' => 'decimal',
            'mergeHeader' => true,
            'pageSummary' => true,
            'footer' => true
        ],
        [
            'attribute' => 'today',
            'label' => 'วันนี้',
            'format' => 'decimal',
            'mergeHeader' => true,
            'pageSummary' => true,
            'footer' => true
        ]
    ],
    'tableOptions' => [
        'class' => 'table table-hover'
    ],
//    'summary' => false,
    'showPageSummary' => true,
]);