<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

echo GridView::widget([
    'id' => 'grid-report',
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
        return [
            //'data' => ['date' => $model['visit_date'], 'hn' => $model['pt_hn'], 'right-code' => $model['right_code']]
            'data' => ['url' => Url::to(['/customer/main-report/detail-item',
                    'right_code' => $model['right_code'],
                    'hn' => $model['pt_hn'],
                    'visit_date' => $model['visit_date'],
                    'receipt_mas_id' => $model['receipt_ids'],
                    'receipt_visit_id' => $model['receipt_visit_id'],
                    'status_bill' => $model['status_bill']
                ]),]
        ];
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
                    'attribute' => 'sumnotpay',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                    'label' => Yii::t('patient', 'Not pay'),
                ],
                [
                    'attribute' => 'sumpay',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                    'label' => Yii::t('patient', 'Pay'),
                ],
                [
                    'attribute' => 'drug_sumnotpay',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                    'label' => 'ค่ายา เบิกได้',
                ],
                [
                    'attribute' => 'drug_sumpay',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                    'label' => 'ค่ายา ชำระเอง',
                ],
                [
                    'attribute' => 'visit_date',
                    'format' => ['date', 'php:d/m/Y'],
                    'label' => 'Visit Date',
                ],
                [
                    'class' => 'appxq\sdii\widgets\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $data, $key)use($searchModel) {
                            $html = Html::a('<i class="fa fa-print"></i> ' . Yii::t('patient', 'Print'), '#', [
                                        /* 'data-hn' => $data['pt_hn'],
                                          'data-date' => $data['visit_date'],
                                          'data-right-code' => $data['right_code'], */
                                        'data-url' => Url::to(['/customer/main-report/print-detail',
                                            'right_code' => $searchModel['order_tran_code'],
                                            'project_id' => $searchModel['order_tran_dept'],
                                            'hn' => $data['pt_hn'],
                                            'visit_date' => $data['visit_date'],
                                        ]),
                                        'class' => 'btn btn-warning btn-xs btn-block',
                            ]);

                            return $html;
                        },
                            ],
                            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
                        ]
                    ]
                ]);
?>