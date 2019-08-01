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
/* @var $searchModel backend\modules\ezmodules\models\EzmoduleTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ezmodule', 'Template');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="ezmodule-template-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php  Pjax::begin(['id'=>'ezmodule-template-grid-pjax']);?>
    
    <?= GridView::widget([
	'id' => 'ezmodule-template-grid',
	'panelBtn' => Html::a(SDHtml::getBtnAdd(), Url::to(['ezmodule-template/create']), ['class' => 'btn btn-success btn-sm']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['ezmodule-template/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-ezmodule-template', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionEzmoduleTemplateIds'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            //'template_id',
            'template_name',
            //'template_html:ntext',
            //'template_js:ntext',
            [
		'attribute'=>'template_system',
		'value'=>function ($data){ return ($data['template_system']==1?ModuleFunc ::itemAlias('system', 1):ModuleFunc ::itemAlias('system', 0)); },
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:150px; text-align: center;'],
                'visible'=> Yii::$app->user->can('administrator'),
                'filter'=> Html::activeDropDownList($searchModel, 'template_system', ModuleFunc ::itemAlias('system'), ['class'=>'form-control', 'prompt'=>Yii::t('ezmodule', 'All')]),
            ],
            [
		'attribute'=>'public',
		'value'=>function ($data){ return ($data['public']==1?ModuleFunc ::itemAlias('public',1):ModuleFunc ::itemAlias('public',0)); },
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:100px; text-align: center;'],
                'filter'=> Html::activeDropDownList($searchModel, 'public', ModuleFunc ::itemAlias('public'), ['class'=>'form-control', 'prompt'=>Yii::t('ezmodule', 'All')]),
            ],
            // 'sitecode',
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',
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
    'id' => 'modal-ezmodule-template',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("

$('#ezmodule-template-grid-pjax').on('click', '#modal-delbtn-ezmodule-template', function() {
    selectionEzmoduleTemplateGrid($(this).attr('data-url'));
});

$('#ezmodule-template-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#ezmodule-template-grid').yiiGridView('getSelectedRows');
	disabledEzmoduleTemplateBtn(key.length);
    },100);
});

$('#ezmodule-template-grid-pjax').on('click', '.selectionEzmoduleTemplateIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledEzmoduleTemplateBtn(key.length);
});

$('#ezmodule-template-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    location.href = '".Url::to(['ezmodule-template/update', 'id'=>''])."'+id;
});	

$('#ezmodule-template-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	location.href = url;
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezmodule-template-grid-pjax'});
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

function disabledEzmoduleTemplateBtn(num) {
    if(num>0) {
	$('#modal-delbtn-ezmodule-template').attr('disabled', false);
    } else {
	$('#modal-delbtn-ezmodule-template').attr('disabled', true);
    }
}

function selectionEzmoduleTemplateGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionEzmoduleTemplateIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezmodule-template-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalEzmoduleTemplate(url) {
    $('#modal-ezmodule-template .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule-template').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>