<?php

use yii\grid\GridView;
use yii\helpers\Html;

echo GridView::widget([
    'id' => 'grid-drug-trans',
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    'rowOptions' => function ($model) {
        return [
            'data' => ['id' => $model['cpoe_id'], 'ids' => $model['cpoe_ids']],
        ];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ], [
            'attribute' => 'itemdetail',
            'label' => Yii::t('patient', 'Order Name'),
            'format' => 'html',
            'value' => function ($model) {
                $html = Html::tag('div', '&nbsp;&nbsp;' . $model['sig_decs'], ['style' => 'color:#999;']);
                return $model['itemdetail'] . $html;
            },
        ], [
            'attribute' => 'item_qty',
            'label' => Yii::t('patient', 'Amount'),
            'format' => ['decimal'],
            'contentOptions' => ['style' => 'width:70px;text-align: center;'],
        ],
        [
            'attribute' => 'use_time',
            'label' => Yii::t('patient', 'Time'),
            'format' => ['date', 'php:d/m/y H:s'],
//            'format' => ['html'],
//            'value' => function ($model) {
//                return appxq\sdii\utils\SDdate::mysql2phpThDateTime($model['use_time']);
//            },
            'contentOptions' => ['style' => 'width:75px;text-align: center;'],
        ],
    ]
]);
?>
<?php

$this->registerJs("
    $('#grid-drug-trans').on('click', '.pagination li a', function() { //Next 
        var url = $(this).attr('href');
        getUiAjax(url, '$reloadDiv');
        return false;
    });
");
?>