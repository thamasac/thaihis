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
    'rowOptions' => function ($model)use($user_id) {
        return [
            'data' => [
                'key' => ($model['order_doctor_id'] == $user_id && $model['order_tran_status'] == '1') ? $model['order_tran_id'] : ''
                , 'tmt' => $model['trad_tmt']
                , 'order-tran' => $model['order_status']
            ],
        ];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ], [
            'attribute' => 'item_name',
            'label' => Yii::t('patient', 'Order Name'),
            'format' => 'html',
            'value' => function ($model)use($ezf_id) {
                $html = Html::tag('div', '&nbsp;&nbsp;' . $model['order_tran_label'], ['style' => 'color:#999;']);

                if ($model['order_tran_ned']) {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_tran_ned', ':ezf_id' => $ezf_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }
                    $txtNed = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                    $html .= Html::tag('div', '&nbsp;&nbsp;' . $model['order_tran_ned'] . ' : ' . $txtNed, ['style' => 'color:#999;']);
                }
                return $model['item_name'] . $html;
            },
        ], [
            'attribute' => 'order_tran_qty',
            'label' => Yii::t('patient', 'Amount'),
            'format' => 'raw',
            'value' => function ($model)use($user_id) {
                if ($model['order_tran_status'] == '1' && $model['order_status'] == '1' && $model['order_doctor_id'] == $user_id) {
                    $result = Html::textInput('order_tran_qty', $model['order_tran_qty'], ['class' => 'form-control text-center', 'data-key' => $model['order_tran_id']]);
                } else {
                    $result = $model['order_tran_qty'];
                }
                return $result;
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
                'view' => function ($url, $data, $key)use($user_id) {
                    if ($data['order_doctor_id'] <> $user_id) :
                        $html = Html::tag('div', Html::button(' <span class="fa fa-user-md"></span> ', ['class' => 'btn btn-danger btn-xs dropdown-toggle',
                                            'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'false'])
                                        . Html::tag('div', Html::tag('div', '<i class="fa fa-user"></i> ' . $data['fullname_doctor'], ['style' => 'margin: 5px 5px;']), ['class' => 'dropdown-menu dropdown-menu-right'])
                                        , ['style' => 'position: absolute;']);
                    elseif ($data['order_tran_status'] == '1' && $data['order_status'] == '1') :
                        $html = Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/pis/pis-item-order/order-delete',
                                            'order_tran_id' => $data['order_tran_id'],
                                        ]), [
                                    'data-action' => 'delete',
                                    'title' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'class' => 'btn btn-danger btn-xs',
                        ]);
                    elseif ($data['order_tran_status'] == '2') :
                        $html = Html::tag('div', Html::button(' <span class="fa fa-angellist"></span> ', ['class' => 'btn btn-info btn-xs']), ['style' => 'position: absolute;']);
                    elseif ($data['order_status'] == '2') :
                        $html = Html::tag('div', Html::button(' <span class="fa fa-stop-circle-o"></span> ', ['class' => 'btn btn-info btn-xs']), ['style' => 'position: absolute;']);
                    endif;

                    return $html;
                },
            ],
            'contentOptions' => ['style' => 'width:30px;text-align: center;'],
        ]
    ]
]);
?>
<div class="modal-footer">
    <?php
    if ($model['order_status'] == '1') {
        ?>
      <button type="submit" class="btn btn-primary btn-submit" name="submit" value="1" data-loading-text="Loading...">Approve order</button>    
  <?php } elseif ($model['order_status'] == '2') { ?>
      <button type="submit" class="btn btn-danger btn-submit" name="submit" value="1" data-loading-text="Loading...">Cancel Approve</button>    
      <button type="submit" class="btn btn-warning print-label" name="print" value="1">Print Order</button>    
  <?php } ?>
  <!--  <button type="button" class="btn btn-default" data-dismiss="modal">
      <i class="glyphicon glyphicon-remove"></i> ปิด
    </button>    -->
</div>
<?php
$modal = 'modal-' . $ezfOrder_id;
if ($page == 'ordermain') {
    $modal = 'modal-ezform-main';
}
$urlApprove = Url::to(['/pis/pis-item-order/order-approve', 'order_id' => $model['id'], 'order_status' => ($model['order_status'] == '1' ? '2' : '1')]);
$url = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => $modal, 'reloadDiv' => $reloadDiv,]);
$urlSaveQty = Url::to(['/pis/pis-item-order/order-change']);
$urlPrint = Url::to(['/pis/pis-item-order/print-order-approve', 'order_id' => $model['id']]);
$this->registerJs("
$('.btn-submit').on('click', function(){
        $.get('$urlApprove').done(function(result) {
            if(result.status == 'success') {
            " . SDNoty::show('result.message', 'result.status') . "
                if($('.modal').is(':visible')){
                    $('.modal.in').modal('hide');
                }else{
                    let url = $('#$reloadDiv').attr('data-url');
                    getUiAjax(url, '$reloadDiv');
                }
                if(result.data.order_status === '2'){
                    myWindow = window.open('$urlPrint', '_blank');
                    myWindow.focus();
                    myWindow.print();
                }
            } else {
            " . SDNoty::show('result.message', 'result.status') . "
            }
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
        });
});    

$('.print-label').on('click', function () {
      myWindow = window.open('$urlPrint', '_blank');
      myWindow.focus();
      myWindow.print();
});

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
    let data_key = $(this).attr('data-key');
    if(data_key){
        if($(this).attr('data-order-tran') == '1'){
            var url = '$url' + '&dataid=' + data_key; 
            modalEzformMain2(url,'#$modal');  
        }else{
           let txt = 'ต้องการแก้ไข รายการใช่หรือไม่ ?';
           yii.confirm(txt, function() {           
                var url = '$url' + '&dataid=' + data_key; 
                modalEzformMain2(url,'#$modal');   
           });
        }
    }
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