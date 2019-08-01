<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\SystemErrorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ezform', 'System Errors');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="system-error-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  Pjax::begin(['id'=>'system-error-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'system-error-grid',
	'panelBtn' => 
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['system-error/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-system-error', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionSystemErrorIds'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            //'id',
            'code',
            'name',
            //'file:ntext',
            'line',
            'message:ntext',
            // 'name',
            // 'trace_string:ntext',
            // 'created_by',
            // 'created_at',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y']
            ],
	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
		'template' => '{view} {delete}',
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-system-error',
    'size'=>'modal-xxl',
]);
?>

<?php  $this->registerJs("
$('#system-error-grid-pjax').on('click', '#modal-addbtn-system-error', function() {
    modalSystemError($(this).attr('data-url'));
});

$('#system-error-grid-pjax').on('click', '#modal-delbtn-system-error', function() {
    selectionSystemErrorGrid($(this).attr('data-url'));
});

$('#system-error-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#system-error-grid').yiiGridView('getSelectedRows');
	disabledSystemErrorBtn(key.length);
    },100);
});

$('#system-error-grid-pjax').on('click', '.selectionSystemErrorIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledSystemErrorBtn(key.length);
});

$('#system-error-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalSystemError('".Url::to(['system-error/view', 'id'=>''])."'+id);
});	

$('#system-error-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalSystemError(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#system-error-grid-pjax'});
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

function disabledSystemErrorBtn(num) {
    if(num>0) {
	$('#modal-delbtn-system-error').attr('disabled', false);
    } else {
	$('#modal-delbtn-system-error').attr('disabled', true);
    }
}

function selectionSystemErrorGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionSystemErrorIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#system-error-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalSystemError(url) {
    $('#modal-system-error .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-system-error').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>