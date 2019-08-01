<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

echo GridView::widget([
    'id' => 'grid-order-trans',
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    'rowOptions' => function ($model) {
        return [
            'data' => ['key' => $model['order_tran_id']],
        ];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ], [
            'attribute' => 'trad_name',
            'label' => Yii::t('patient', 'Order Name'),
            'format' => 'html',
            'value' => function ($model) {
                $html = Html::tag('div', '&nbsp;&nbsp;'.$model['order_tran_label'], ['style' => 'color:#999;']);
                return $model['trad_name'] . $html;
            },
        ], [
            'attribute' => 'order_tran_qty',
            'label' => Yii::t('patient', 'Amount'),
            'format' => 'raw',
            'value' => function ($model) {
                return Html::textInput('order_tran_qty', $model['order_tran_qty'], ['class' => 'form-control text-center', 'data-key' => $model['order_tran_id']]);
            },
            'contentOptions' => ['style' => 'width:70px;text-align: center;'],
            'footer' => 'รวม',
            'footerOptions' => ['class' => 'text-right'],
        ], [
            'attribute' => 'order_tran_notpay',
            'contentOptions' => ['class' => 'text-right'],
            'format' => ['decimal', 2],
            'label' => Yii::t('patient', 'Not pay'),
            'footer' => number_format(backend\modules\patient\classes\PatientFunc::getTotal($dataProvider->models, 'order_tran_notpay'), 2),
            'footerOptions' => ['class' => 'text-right'],
        ], [
            'attribute' => 'order_tran_pay',
            'contentOptions' => ['class' => 'text-right'],
            'format' => ['decimal', 2],
            'label' => Yii::t('patient', 'Pay'),
            'footer' => number_format(backend\modules\patient\classes\PatientFunc::getTotal($dataProvider->models, 'order_tran_pay'), 2),
            'footerOptions' => ['class' => 'text-right'],
        ], [
            'class' => 'appxq\sdii\widgets\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $data, $key) {
                    $html = Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/pis/pis-item-order/order-delete',
                                        'order_tran_id' => $data['order_tran_id'],
                                    ]), [
                                'data-action' => 'delete',
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'class' => 'btn btn-danger btn-xs',
                    ]);

                    return $html;
                },
            ],
            'contentOptions' => ['style' => 'width:30px;text-align: center;'],
        ]
    ]
]);
$url = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => $reloadDiv,]);
$urlSaveQty = Url::to(['/pis/pis-item-order/order-change']);
$this->registerJs("
$('#$reloadDiv .grid-view').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    if(action === 'delete') {
        var txtConfirm = $(this).attr('data-confirm');
	yii.confirm(txtConfirm, function() {
	    $.post(url).done(function(result) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
                    url = $('#$reloadDiv').attr('data-url');
		    getUiAjax(url, '$reloadDiv');
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }).fail(function() {
		" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
		console.log('server error');
	    });
	});
    }
    return false;
});

$('#$reloadDiv .grid-view').on('dblclick', 'tbody tr', function() {
        var url = '$url' + '&dataid=' + $(this).attr('data-key'); 
        modalEzformMain2(url,'#modal-ezform-main');  
});

function modalEzformMain2(url,modal) {
    $(modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $(modal).modal('show')
    .find('.modal-content')
    .load(url);
}

$('#$reloadDiv .grid-view').on('change', 'tbody tr td input', function() {
    var url = '$urlSaveQty' + '?dataid=' + $(this).attr('data-key') + '&qty=' + $(this).val();    
    $.get(url).done(function(result) {
	if(result.status == 'success') {
            " . SDNoty::show('result.message', 'result.status') . "
            url = $('#$reloadDiv').attr('data-url');
            getUiAjax(url, '$reloadDiv');
	} else {
            " . SDNoty::show('result.message', 'result.status') . "
	}
    }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
    });
});
");
?>