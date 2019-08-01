<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleFunc;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezmodules\models\EzmoduleWidgetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ezmodule', 'Widget');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="ezmodule-widget-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  Pjax::begin(['id'=>'ezmodule-widget-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'ezmodule-widget-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['ezmodule-widget/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-ezmodule-widget']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['ezmodule-widget/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-ezmodule-widget', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionEzmoduleWidgetIds'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            //'widget_id',
            [
		'attribute'=>'widget_name',
		'contentOptions'=>['style'=>'width:250px; '],
            ],
            [
		'attribute'=>'widget_varname',
		'value'=>function ($data){ return "<code>{{$data['widget_varname']}}</code>"; },
                'format'=>'raw',        
		'contentOptions'=>['style'=>'width:200px; '],
            ],
            'widget_detail:ntext',
            'widget_example:ntext',
            // 'options:ntext',
            [
		'attribute'=>'enable',
		'value'=>function ($data){ return ($data['enable']==1?ModuleFunc ::itemAlias('system', 1):ModuleFunc ::itemAlias('system', 0)); },
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:80px; text-align: center;'],
                'filter'=> Html::activeDropDownList($searchModel, 'enable', ModuleFunc ::itemAlias('system'), ['class'=>'form-control', 'prompt'=>Yii::t('ezmodule', 'All')]),
            ],
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
    'id' => 'modal-ezmodule-widget',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>

<?php  $this->registerJs("
$('#ezmodule-widget-grid-pjax').on('click', '#modal-addbtn-ezmodule-widget', function() {
    modalEzmoduleWidget($(this).attr('data-url'));
});

$('#ezmodule-widget-grid-pjax').on('click', '#modal-delbtn-ezmodule-widget', function() {
    selectionEzmoduleWidgetGrid($(this).attr('data-url'));
});

$('#ezmodule-widget-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#ezmodule-widget-grid').yiiGridView('getSelectedRows');
	disabledEzmoduleWidgetBtn(key.length);
    },100);
});

$('#ezmodule-widget-grid-pjax').on('click', '.selectionEzmoduleWidgetIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledEzmoduleWidgetBtn(key.length);
});

$('#ezmodule-widget-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalEzmoduleWidget('".Url::to(['ezmodule-widget/update', 'id'=>''])."'+id);
});	

$('#ezmodule-widget-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalEzmoduleWidget(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezmodule-widget-grid-pjax'});
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

function disabledEzmoduleWidgetBtn(num) {
    if(num>0) {
	$('#modal-delbtn-ezmodule-widget').attr('disabled', false);
    } else {
	$('#modal-delbtn-ezmodule-widget').attr('disabled', true);
    }
}

function selectionEzmoduleWidgetGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionEzmoduleWidgetIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezmodule-widget-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalEzmoduleWidget(url) {
    $('#modal-ezmodule-widget .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule-widget').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>