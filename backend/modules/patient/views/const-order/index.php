<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\patient\models\ConstOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Const Orders';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="const-order-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  Pjax::begin(['id'=>'const-order-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'const-order-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['const-order/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-const-order']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['const-order/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-const-order', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionConstOrderIds'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            'order_code',
            'order_name',
            'group_code',
            'group_type',
            'fin_item_code',
            // 'sks_code',
            // 'full_price',
            // 'order_status',

	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
		'template' => '{view} {update} {delete}',
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-const-order',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#const-order-grid-pjax').on('click', '#modal-addbtn-const-order', function() {
    modalConstOrder($(this).attr('data-url'));
});

$('#const-order-grid-pjax').on('click', '#modal-delbtn-const-order', function() {
    selectionConstOrderGrid($(this).attr('data-url'));
});

$('#const-order-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#const-order-grid').yiiGridView('getSelectedRows');
	disabledConstOrderBtn(key.length);
    },100);
});

$('#const-order-grid-pjax').on('click', '.selectionConstOrderIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledConstOrderBtn(key.length);
});

$('#const-order-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalConstOrder('".Url::to(['const-order/update', 'id'=>''])."'+id);
});	

$('#const-order-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalConstOrder(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#const-order-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
    }
    return false;
});

function disabledConstOrderBtn(num) {
    if(num>0) {
	$('#modal-delbtn-const-order').attr('disabled', false);
    } else {
	$('#modal-delbtn-const-order').attr('disabled', true);
    }
}

function selectionConstOrderGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionConstOrderIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#const-order-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalConstOrder(url) {
    $('#modal-const-order .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-const-order').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>