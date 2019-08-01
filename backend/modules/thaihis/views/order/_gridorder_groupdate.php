<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

//$options = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options);
echo GridView::widget([
    'id' => 'grid-order-trans-' . rand(),
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'striped' => false,
    'hover' => true,
//    'showPageSummary' => true,
    'columns' => [
        [
            'class' => 'kartik\grid\SerialColumn',
            'contentOptions' => ['class' => 'kartik-sheet-style'],
            'width' => '36px',
            'header' => '',
            'headerOptions' => ['class' => 'kartik-sheet-style']
        ],
        [
            'attribute' => 'order_date',
            'format' => 'raw',
            'label' => Yii::t('thaihis', 'Date'),
            'value' => function ($model) {
                $date = '<label class="text-primary">' . \appxq\sdii\utils\SDdate::mysql2phpThDate($model['order_date']) . '</h4>';
                return $date;
            },
        ],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model)use($options, $visit_id) {
                return Yii::$app->controller->renderPartial('_ipd_gengrid', ['model' => $model,
                            'options' => $options,
                            'visit_id' => $visit_id
                ]);
            },
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'expandOneOnly' => true
        ],
    ],
]);
?>