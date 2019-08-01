<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$options = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options);
echo GridView::widget([
    'id' => 'grid-order-trans-' . rand(),
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'striped' => false,
    'hover' => true,
    'showPageSummary' => true,
    'rowOptions' => function ($model) {
        return [
            'data' => [
                'code' => $model['order_tran_code'],
            ]
            , 'class' => isset($model['order_tran_doctor']) ? 'success' : ''
        ];
    },
    'columns' => [
        [
            'attribute' => 'order_type_name',
            'label' => Yii::t('thaihis', 'Type'),
            'group' => true, // enable grouping,
            'groupedRow' => true, // move grouped column to a single grouped row
            'groupOddCssClass' => 'kv-grouped-row', // configure odd group cell css class
            'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class   
//            'contentOptions' => ['class' => 'danger', 'style' => 'font-weight:bold;']
            'contentOptions' => ['class' => 'danger']
        ],
        [
            'attribute' => 'order_group_name',
            'label' => Yii::t('thaihis', 'Group'),
            'group' => true, // enable grouping
            'subGroupOf' => 1, // supplier column index is the parent group
            'groupOddCssClass' => 'kv-group-even', // configure odd group cell css class
            'groupEvenCssClass' => 'kv-group-even', // configure even group cell css class
        ],
        [
            'attribute' => 'order_name',
            'format' => 'raw',
            'label' => Yii::t('thaihis', 'Order Name'),
            'value' => function ($model) {
                if ($model['doc_fullname']) {
                    $html = Html::a($model['order_name'], '#'
                                    , ['title' => $model['doc_fullname']
                                , 'onclick' => "showDocname('{$model['doc_fullname']}')"]);
                } else {
                    $html = $model['order_name'] . ($model['external_flag'] == 'Y' ? ' (External Lab)' : '');
                }
                return $html;
            },
            'pageSummary' => 'รวม',
            'pageSummaryOptions' => ['class' => 'text-right text-warning'],
        ],
        [
            'attribute' => 'order_qty',
            'format' => 'raw',
            'label' => Yii::t('thaihis', 'Amount'),
            'value' => function ($model)use($btnDisabled) {
                if ($model['order_tran_status'] == '1' and $model['order_tran_cashier_status'] == '1' && $btnDisabled == 0) {
                    $result = Html::textInput('order_qty', $model['order_qty'], ['class' => 'form-control text-center', 'data-key' => $model['id']]);
                } else {
                    $result = $model['order_qty'];
                }
                return $result;
            },
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ],
        [
            'attribute' => 'order_tran_notpay',
            'contentOptions' => ['class' => 'text-right'],
            'format' => ['decimal', 2],
            'label' => Yii::t('thaihis', 'Not pay'),
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummaryOptions' => ['class' => 'text-right'],
        ], [
            'attribute' => 'order_tran_pay',
            'contentOptions' => ['class' => 'text-right'],
            'format' => ['decimal', 2],
            'label' => Yii::t('thaihis', 'Pay'),
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummaryOptions' => ['class' => 'text-right'],
        ],
        [
            'class' => 'kartik\grid\FormulaColumn',
            'value' => function ($data, $key, $index, $widget)use($visit_id, $btnDisabled, $options) {
                if ($data['order_tran_status'] == '1' && $data['order_tran_cashier_status'] == 1 && $btnDisabled == 0) :
                    $html = Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/thaihis/order/order-delete',
                                        'dataid' => $data['id'], 'options' => $options
                                    ]), [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn btn-danger btn-xs action-delete',
                    ]);
                elseif ($data['order_tran_status'] == '2') :
                    $html = Html::tag('div', Html::button(' <span class="fa fa-flask"></span> ', ['class' => 'btn btn-info btn-xs dropdown-toggle',
                                        'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'false'])
                                    . Html::tag('div', Html::tag('div', '<i class="fa fa-refresh"></i> ' . Yii::t('patient', 'In process'), ['style' => 'margin: 5px 5px;']), ['class' => 'dropdown-menu dropdown-menu-right'])
                                   );
                elseif ($data['order_tran_status'] == '3') :
                    $html = Html::tag('div', Html::button(' <span class="fa fa-check-square"></span> ', ['class' => 'btn btn-success btn-xs',]));
                elseif ($data['order_tran_cashier_status'] == 2) :
                    $html = Html::tag('div', Html::button(' <span class="fa fa-money"></span> ', ['class' => 'btn btn-warning btn-xs dropdown-toggle',
                                        'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'false'])
                                    . Html::tag('div', Html::tag('div', '<i class="fa fa-money"></i> ชำระเงินแล้ว', ['style' => 'margin: 5px 5px;']), ['class' => 'dropdown-menu dropdown-menu-right'])
                                    );
                else :
                    $html = '';
                endif;
                return $html.'<div class="clearfix"></div>';
            },
            'mergeHeader' => true,
            //'width' => '30px',
            'hAlign' => 'center',
            'format' => 'raw',
            'pageSummary' => true
        ],
//        [
//            'class' => 'kartik\grid\CheckboxColumn',
////            'headerOptions' => ['class' => 'kartik-sheet-style'],
//            'checkboxOptions' => function($model) {
//                if ($model['order_tran_status'] == '1' && empty($model['order_tran_cashier_status'])) {
//                    return [];
//                } else {
//                    return ['disabled' => true];
//                }
//            },
//        ],
    ],
]);
$txtDelete = Yii::t('yii', 'Are you sure you want to delete this item?');
$urlSaveQty = Url::to(['/thaihis/order/grid-order-change', 'options' => $options]);
$urlreload = Url::to(['/thaihis/order/grid-order', 'ezf_id' => $ezf_id, 'visitid' => $visit_id
            , 'reloadDiv' => $reloadDiv, 'options' => $options]);
$this->registerJs("
function showDocname(name){
    yii.confirm(name);
}    

$('#$reloadDiv .grid-view').on('click', 'thead tr th a', function() { //Sort
    var url = $(this).attr('href');
    getUiAjax(url, '$reloadDiv');
    return false;
});

$('#$reloadDiv .grid-view').on('click', '.pagination li a', function() { //Next 
    var url = $(this).attr('href');
    getUiAjax(url, '$reloadDiv');
    return false;
});
    
$('#$reloadDiv .grid-view').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
//    var action = $(this).attr('data-action');
    if($(this).hasClass('action-delete')) {
        var txtConfirm = '$txtDelete';
	yii.confirm(txtConfirm, function() {
	    $.post(url).done(function(result) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
		     getUiAjax('$urlreload', '$reloadDiv');
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }).fail(function() {
		" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
		console.log('server error');
	    });
	});
    }else{    
//        modalOrdercounter(url);
    }
    return false;
});

function modalOrdercounter(url) {
    $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-main').modal('show')
    .find('.modal-content')
    .load(url);
}

function getUiAjax(url, divid) {
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }

$('#$reloadDiv .grid-view').on('change', 'tbody tr td input[type=\"text\"]', function() {
    //var url = '$urlSaveQty' + '?dataid=' + $(this).attr('data-key') + '&qty=' + $(this).val();    
    $.get('$urlSaveQty',{dataid:$(this).attr('data-key'),qty:$(this).val()}).done(function(result) {
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