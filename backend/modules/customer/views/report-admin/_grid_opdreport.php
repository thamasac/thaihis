<?php

use kartik\grid\GridView;

echo GridView::widget([
    'id' => 'grid-report',
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'autoXlFormat' => true,
    'export' => [
        'fontAwesome' => true,
        'showConfirmAlert' => true,
        'target' => GridView::TARGET_BLANK
    ],
    'pjax' => true,
    'showPageSummary' => true,
    'panel' => [
        'type' => 'primary',
//        'heading' => 'Products'
        'footer' => false,
    ],
]);
?>