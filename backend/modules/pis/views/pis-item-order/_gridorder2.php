<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

echo GridView::widget([
    'id' => 'grid-order-trans-' . appxq\sdii\utils\SDUtility::getMillisecTime(),
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'striped' => false,
    'hover' => true,
    'showPageSummary' => true,
    'rowOptions' => function ($model)use($user_id) {
        return [
            'data' => [
                'key' => ($model['order_doctor_id'] == $user_id && $model['order_tran_status'] == '1') ? $model['id'] : ''
                , 'tmt' => $model['trad_tmt']
                , 'order-tran' => $model['order_status']
            ],
        ];
    },
    'columns' => [
//        [
//            'attribute' => 'order_tran_use_type',
//            'label' => Yii::t('patient', 'Type'),
//            'group' => true, // enable grouping,
//            'groupedRow' => true, // move grouped column to a single grouped row
//            'groupOddCssClass' => 'kv-grouped-row', // configure odd group cell css class
//            'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class   
//            'value' => function ($model)use($ezf_id) {
//
//                $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_tran_use_type', ':ezf_id' => $ezf_id])->one();
//                if (isset(Yii::$app->session['ezf_input'])) {
//                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
//                }
//
//                return \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
//            },
//            'contentOptions' => ['class' => 'danger']
//        ],
        [
            'attribute' => 'item_name',
            'label' => Yii::t('patient', 'Order Name'),
            'format' => 'html',
            'value' => function ($model)use($ezf_id) {
                $html = '';
                if ($model['order_tran_chemo_amount'] && $model['order_tran_chemo_result']) {
                    $txt = '&nbsp;&nbsp;สูตร ' . $model['order_tran_chemo_cal'] . ' ปริมาณ ' . $model['order_tran_chemo_amount'] . ' ผลลัพธ์ ' . $model['order_tran_chemo_result'];
                    $html .= Html::tag('div', $txt, ['style' => 'color:#999;']);
                }
                
                $html .= Html::tag('div', '&nbsp;&nbsp;' . $model['order_tran_label'], ['style' => 'color:#999;']);
                
                if ($model['order_tran_note']) {
                    $html .= Html::tag('div', '&nbsp;&nbsp;หมายเหตุ ' . $model['order_tran_note'], ['style' => 'color:#999;']);
                }

                if ($model['order_tran_ned']) {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_tran_ned', ':ezf_id' => $ezf_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }
                    $txtNed = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                    $html .= Html::tag('div', '&nbsp;&nbsp;' . $model['order_tran_ned'] . ' : ' . $txtNed, ['style' => 'color:#999;']);
                }

                return $model['trad_itemname'] . $html;
            },
            'pageSummary' => 'รวม',
            'pageSummaryOptions' => ['class' => 'text-right text-warning'],
        ],
        [
            'attribute' => 'order_tran_qty',
            'label' => Yii::t('patient', 'Amount'),
            'format' => 'raw',
            'value' => function ($model)use($user_id) {
                if ($model['order_tran_status'] == '1' && $model['order_status'] == '1' && $model['order_doctor_id'] == $user_id) {
                    $result = Html::textInput('order_tran_qty', $model['order_tran_qty'], ['class' => 'form-control text-center', 'data-key' => $model['id']]);
                } else {
                    $result = $model['order_tran_qty'];
                }
                return $result;
            },
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ],
        [
            'attribute' => 'order_tran_notpay',
            'format' => ['decimal', 2],
            'label' => Yii::t('patient', 'Not pay'),
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummaryOptions' => ['class' => 'text-right'],
//            'contentOptions' => ['class' => 'text-right'],
//            'contentOptions' => ['style' => 'width:80px;text-align: right;'],
        ], [
            'attribute' => 'order_tran_pay',
            'format' => ['decimal', 2],
            'label' => Yii::t('patient', 'Pay'),
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummaryOptions' => ['class' => 'text-right'],
//            'contentOptions' => ['class' => 'text-right'],
//            'contentOptions' => ['style' => 'width:110px;text-align: right;'],
        ],
        [
            'class' => 'kartik\grid\FormulaColumn',
            'value' => function ($data, $key, $index, $widget)use($user_id, $ezfOrder_id) {
                if ($data['order_doctor_id'] <> $user_id) :

                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_doctor_id', ':ezf_id' => $ezfOrder_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    $doc_name = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $data);
                    $html = Html::button(' <span class="fa fa-user-md"></span> ', ['class' => 'btn btn-danger btn-xs',
                                'data-toggle' => 'tooltip', 'title' => $doc_name]); elseif ($data['order_tran_status'] == '1' && $data['order_status'] == '1') :
                    $html = Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/pis/pis-item-order/order-delete',
                                        'order_tran_id' => $data['id'],
                                    ]), [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn btn-danger btn-xs action-delete',
                    ]);
                elseif ($data['order_tran_status'] == '2') :
                    $html = Html::button(' <span class="fa fa-angellist"></span> ', ['class' => 'btn btn-info btn-xs',
                                'data-toggle' => 'tooltip', 'title' => "จ่ายยาแล้ว"]);
                elseif ($data['order_status'] == '2') :
                    $html = Html::button(' <span class="fa fa-stop-circle-o"></span> ', ['class' => 'btn btn-info btn-xs',
                                'data-toggle' => 'tooltip', 'title' => "Approve"]);
                endif;

                return $html;
            },
            'mergeHeader' => true,
//            'width' => '30px',
            'hAlign' => 'center',
            'format' => 'raw',
//            'pageSummary' => true
        ],
    ],
]);
//if (!isset($options['action_counter'])) {
?>
<div class="modal-footer">
    <?php
    if ($model['order_status'] == '1' && $dataProvider->totalCount > 0) {
        if (Yii::$app->user->can('doctor') || Yii::$app->user->can('pharmacy')) {
            ?>
          <button type="submit" class="btn btn-primary btn-submit" name="submit" value="1" data-loading-text="Loading...">Approve order</button>    
          <?php
      }
  } elseif ($model['order_status'] == '2') {
      if (Yii::$app->user->can('doctor') || Yii::$app->user->can('pharmacy')) {
          ?>
          <button type="submit" class="btn btn-danger btn-submit" name="submit" value="1" data-loading-text="Loading...">Cancel Approve</button>    
          <?php
      }
      ?>
      <button type="submit" class="btn btn-warning print-label" name="print" value="1">Print Order</button>    
  <?php } ?>
  <!--  <button type="button" class="btn btn-default" data-dismiss="modal">
      <i class="glyphicon glyphicon-remove"></i> ปิด
    </button>    -->
</div>
<?php
//}
$modal = 'modal-grid-' . $ezf_id;

$urlApprove = Url::to(['/pis/pis-item-order/order-approve', 'order_id' => $model['id'], 'order_status' => ($model['order_status'] == '1' ? '2' : '1')]);
$editInit = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['order_tran_flagstatus' => '2']);
$url = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => $modal, 'reloadDiv' => $reloadDiv, 'initdata' => $editInit]);
$urlSaveQty = Url::to(['/pis/pis-item-order/order-change']);
$urlPrint = Url::to(['/pis/pis-item-order/print-order-approve', 'order_id' => $model['id']]);
$txtDelete = Yii::t('yii', 'Are you sure you want to delete this item?');
$this->registerJs("
$('#ezf-fix-modal-box').append('<div id=\"$modal\" class=\"fade modal\" role=\"dialog\"><div class=\"modal-dialog modal-xxl\"><div class=\"modal-content\"></div></div></div>');    

 $('[data-toggle=\"tooltip\"]').tooltip(); 
$('.btn-submit').on('click', function(){
        $.get('$urlApprove').done(function(result) {
            if(result.status == 'success') {
            " . SDNoty::show('result.message', 'result.status') . ";
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
            " . SDNoty::show('result.message', 'result.status') . ";
            }
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . ";
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
//    var action = $(this).attr('data-action');
    if($(this).hasClass('action-delete')) {
        var txtConfirm = '$txtDelete';
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
    var url = '$urlSaveQty' + '?dataid=' + $(this).attr('data-key') + '&qty=' + $(this).val() +'&right_code={$right_code}';    
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