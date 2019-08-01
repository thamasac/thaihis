<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\EzformChangeLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $modelEzf->ezf_name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'EzForms'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = Yii::t('ezform', 'Change Log');

?>
<div class="ezform-change-log-index">

    <?= $this->render('//../modules/ezbuilder/views/ezform-builder/_menu', [
	'model' => $modelEzf,
	'ezf_id' => $ezf_id,
	'modelFields' => $modelFields,
        'modelVersion'=>$modelVersion,
    ]);?>
  <div class="modal-header" style="margin-bottom: 15px;">
  <h3 class="modal-title"><?=Yii::t('ezform', 'Change Log')?> <small><?= Yii::t('ezform', 'Record history of all changes as an audit trail')?></small></h3>
</div>
    <?php  Pjax::begin(['id'=>'ezform-change-log-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'ezform-change-log-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['ezform-change-log/create', 'ezf_id'=>$ezf_id]), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-ezform-change-log']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['ezform-change-log/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-ezform-change-log', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionEzformChangeLogIds'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            //'log_id',
            //'ezf_id',
            //'ezf_field_id',
            'ezf_version',
            'log_event',
            'log_type:ntext',
             
            // 'log_count',
            // 'log_ref_id',
            [
                'attribute' => 'log_detail',
                'format' => 'raw'
            ],
            // 'log_ref_table',
             'log_ref_version',
             'log_ref_varname',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y']
            ],
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',

	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
		'template' => '{update} {delete}',
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-ezform-change-log',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#ezform-change-log-grid-pjax').on('click', '#modal-addbtn-ezform-change-log', function() {
    modalEzformChangeLog($(this).attr('data-url'));
});

$('#ezform-change-log-grid-pjax').on('click', '#modal-delbtn-ezform-change-log', function() {
    selectionEzformChangeLogGrid($(this).attr('data-url'));
});

$('#ezform-change-log-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#ezform-change-log-grid').yiiGridView('getSelectedRows');
	disabledEzformChangeLogBtn(key.length);
    },100);
});

$('#ezform-change-log-grid-pjax').on('click', '.selectionEzformChangeLogIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledEzformChangeLogBtn(key.length);
});

$('#ezform-change-log-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalEzformChangeLog('".Url::to(['ezform-change-log/update', 'id'=>''])."'+id);
});	

$('#ezform-change-log-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalEzformChangeLog(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-change-log-grid-pjax'});
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

function disabledEzformChangeLogBtn(num) {
    if(num>0) {
	$('#modal-delbtn-ezform-change-log').attr('disabled', false);
    } else {
	$('#modal-delbtn-ezform-change-log').attr('disabled', true);
    }
}

function selectionEzformChangeLogGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionEzformChangeLogIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-change-log-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalEzformChangeLog(url) {
    $('#modal-ezform-change-log .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-change-log').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>