<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\EzformInputSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ezform Inputs');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="ezform-input-index">

    <?php  Pjax::begin(['id'=>'ezform-input-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'ezform-input-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['ezform-input/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-ezform-input']),//. ' ' .
		      //Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['ezform-input/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-ezform-input', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
//	    [
//		'class' => 'yii\grid\CheckboxColumn',
//		'checkboxOptions' => [
//		    'class' => 'selectionEzformInputIds'
//		],
//		'headerOptions' => ['style'=>'text-align: center;'],
//		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
//	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            'input_id',
            'input_name',
            'input_class',
            'input_function',
            'system_class',
            // 'input_function_validate',
            // 'input_specific:ntext',
            // 'input_option:ntext',
             'table_field_type',
             'table_field_length',
            'input_active',
            // 'input_version',
            // 'input_order',

	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
		'template' => '{view} {update} ',//{delete}
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>
</div>

<?=  ModalForm::widget([
    'id' => 'modal-ezform-input',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#ezform-input-grid-pjax').on('click', '#modal-addbtn-ezform-input', function() {
    modalEzformInput($(this).attr('data-url'));
});

$('#ezform-input-grid-pjax').on('click', '#modal-delbtn-ezform-input', function() {
    selectionEzformInputGrid($(this).attr('data-url'));
});

$('#ezform-input-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#ezform-input-grid').yiiGridView('getSelectedRows');
	disabledEzformInputBtn(key.length);
    },100);
});

$('#ezform-input-grid-pjax').on('click', '.selectionEzformInputIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledEzformInputBtn(key.length);
});

$('#ezform-input-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalEzformInput('".Url::to(['ezform-input/update', 'id'=>''])."'+id);
});	

$('#ezform-input-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalEzformInput(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-input-grid-pjax'});
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

function disabledEzformInputBtn(num) {
    if(num>0) {
	$('#modal-delbtn-ezform-input').attr('disabled', false);
    } else {
	$('#modal-delbtn-ezform-input').attr('disabled', true);
    }
}

function selectionEzformInputGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionEzformInputIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-input-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalEzformInput(url) {
    $('#modal-ezform-input .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-input').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>