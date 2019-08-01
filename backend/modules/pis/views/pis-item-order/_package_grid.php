<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$ezf_id = \backend\modules\patient\Module::$formID['pis_package_item'];
$modal_id = 'modal-' . $ezf_id;
echo GridView::widget([
    'id' => 'grid-package-item',
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'striped' => false,
    'hover' => true,
    'showPageSummary' => true,
    'rowOptions' => function ($model) {
        return [
            'data' => [
                'key' => $model['item_id'], 'tmt' => $model['trad_tmt']
            ],
        ];
    },
    'columns' => [
        [
            'attribute' => 'item_name',
            'label' => Yii::t('patient', 'Order Name'),
            'format' => 'html',
            'value' => function ($model) {
                $html = Html::tag('div', '&nbsp;&nbsp;' . $model['order_tran_label'], ['style' => 'color:#999;']);
                if ($model['order_tran_note']) {
                    $html .= Html::tag('div', '&nbsp;&nbsp;หมายเหตุ ' . $model['order_tran_note'], ['style' => 'color:#999;']);
                }

                return $model['trad_itemname'] . $html;
            },
        ],
        [
            'attribute' => 'order_tran_qty',
            'label' => Yii::t('patient', 'Amount'),
            'format' => 'raw',
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
            'pageSummary' => 'รวม',
            'pageSummaryOptions' => ['class' => 'text-right text-warning'],
        ],
        [
            'attribute' => 'trad_price',
            'value' => function ($model) {
                return $model['trad_price'] * $model['order_tran_qty'];
            },
            'format' => ['decimal', 2],
            'label' => Yii::t('patient', 'Price'),
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummaryOptions' => ['class' => 'text-right'],
        ],
        [
            'class' => 'kartik\grid\FormulaColumn',
            'value' => function ($data, $key, $index, $widget)use($ezf_id, $action, $modal_id) {
                $html = '';
                if ($action <> 'SELECT') {
                    $html = backend\modules\ezforms2\classes\BtnBuilder::btn()
                            ->ezf_id($ezf_id)
                            ->reloadDiv('package-item-order')
                            ->label('<span class="glyphicon glyphicon-pencil"></span>')
                            ->modal($modal_id)
                            ->options([
                                'title' => Yii::t('yii', 'Edit'),
                                'class' => 'btn btn-primary btn-xs',
                            ])
                            ->buildBtnEdit($data['item_id']);

                    $html .= ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()
                                    ->ezf_id($ezf_id)
                                    ->reloadDiv('package-item-order')
                                    ->label('<span class="glyphicon glyphicon-trash"></span>')
                                    ->modal($modal_id)
                                    ->options([
                                        'title' => Yii::t('yii', 'Delete'),
                                        'class' => 'btn btn-danger btn-xs',
                                    ])
                                    ->buildBtnDelete($data['item_id']);
                }
                return $html;
            },
            'mergeHeader' => true,
            'hAlign' => 'center',
            'format' => 'raw',
            'width' => '60px',
        ],
    ],
]);
?>
