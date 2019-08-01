<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

echo GridView::widget([
    'id' => 'grid-report-admin',
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
//        return [
//            //'data' => ['date' => $model['visit_date'], 'hn' => $model['pt_hn'], 'right-code' => $model['right_code']]
//            'data' => ['url' => Url::to(['/customer/main-report/detail-item',
//                    'right_code' => $model['right_code'],
//                    'hn' => $model['pt_hn'],
//                    
//                ]),]
//        ];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ], [
            'attribute' => 'pt_hn',
            'label' => Yii::t('patient', 'HN')
        ],
        [
            'attribute' => 'pt_cid',
            'label' => Yii::t('patient', 'Citizen ID'),
        ],
        [
            'attribute' => 'fullname',
            'label' => Yii::t('patient', 'Name'),
        ],
        [
            'attribute' => 'sect_name',
//                    'contentOptions' => ['class' => 'text-right'],
//                    'format' => ['decimal', 2],
//                    'label' => Yii::t('patient', 'Not pay'),
        ],
//                [
//                    'attribute' => 'sumpay',
//                    'contentOptions' => ['class' => 'text-right'],
//                    'format' => ['decimal', 2],
//                    'label' => Yii::t('patient', 'Pay'),
//                ],
//                [
//                    'attribute' => 'drug_sumnotpay',
//                    'contentOptions' => ['class' => 'text-right'],
//                    'format' => ['decimal', 2],
//                    'label' => 'ค่ายา เบิกได้',
//                ],
//                [
//                    'attribute' => 'drug_sumpay',
//                    'contentOptions' => ['class' => 'text-right'],
//                    'format' => ['decimal', 2],
//                    'label' => 'ค่ายา ชำระเอง',
//                ],
        [
            'attribute' => 'visit_date',
            'format' => ['date', 'php:d/m/Y'],
            'label' => 'Visit Date',
        ],
        [
            'attribute' => 'visit_regis_type',
            'value' => function ($model, $key, $index, $widget) {
                if ($model['visit_regis_type'] == 2) {
                    return "<span class='badge' style='background-color:green'> มารับบริการแล้ว </span>  ";
                } else {
                    return "<span class='badge' style='background-color:red'> ยังรับบริการ </span>  ";
                }
            },
            'format' => 'raw',
        ],
        [
            'attribute'=>'pt_phone',
             'label' => 'เบอร์โทรศัพท์',
        ]            
    ]
]);
?>