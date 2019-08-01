<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\core\models\CoreFieldsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('core', 'Core Fields');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-fields-index">

    <div class="sdbox-header">
		<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="padding-top: 10px;">
		<span class="label label-primary">Notice</span>
		<?= Yii::t('app', 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.') ?>
    </p>
	
    <?php  Pjax::begin(['id'=>'core-fields-grid-pjax']);?>
    <?= GridView::widget([
		'id' => 'core-fields-grid',
		'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['core-fields/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-core-fields']). ' ' .
					  Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['core-fields/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-core-fields', 'disabled'=>true]),
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => [
					'class' => 'selectionCoreFieldIds'
				],
				'headerOptions' => ['style'=>'text-align: center;'],
				'contentOptions' => ['style'=>'width:40px;text-align: center;'],
			],
			[
				'class' => 'yii\grid\SerialColumn',
				'headerOptions' => ['style'=>'text-align: center;'],
				'contentOptions' => ['style'=>'width:60px;text-align: center;'],
			],
			[
				'attribute'=>'field_code',
				'contentOptions'=>['style'=>'width:160px;'],
			],
			[
				'attribute'=>'field_internal',
				'value'=>function ($data){return ($data['field_internal']===1)?'Yes':'No';},
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:60px;text-align: center;'],
				'filter'=>Html::activeDropDownList($searchModel, 'field_internal', [1=>'Yes', 0=>'No'], ['class'=>'form-control', 'prompt'=>'All']),
			],
			'field_class',
			'field_name',
			[
				'class' => 'appxq\sdii\widgets\ActionColumn',
				'contentOptions'=>['style'=>'width:80px;text-align: center;'],
			],
		],
	]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-core-fields',
    //'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#core-fields-grid-pjax').on('click', '#modal-addbtn-core-fields', function(){
    modalCoreField($(this).attr('data-url'));
});

$('#core-fields-grid-pjax').on('click', '#modal-delbtn-core-fields', function(){
    selectionCoreFieldGrid($(this).attr('data-url'));
});

$('#core-fields-grid-pjax').on('click', '.select-on-check-all', function(){
    window.setTimeout(function() {
		var key = $('#core-fields-grid').yiiGridView('getSelectedRows');
		disabledCoreFieldBtn(key.length);
    },100);
});

$('#core-fields-grid-pjax').on('click', '.selectionCoreFieldIds', function(){
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledCoreFieldBtn(key.length);
});

$('#core-fields-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalCoreField('".Url::to(['core-fields/update', 'id'=>''])."'+id);
});	

$('#core-fields-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action == 'view'){
		modalCoreField(url);
    } else if(action === 'delete') {
		yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
			$.post(
				url
			).done(function(result){
			if(result.status == 'success'){
				". SDNoty::show('result.message', 'result.status') ."
				$.pjax.reload({container:'#core-fields-grid-pjax'});
			} else {
				". SDNoty::show('result.message', 'result.status') ."
			}
			}).fail(function(){
				". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
				console.log('server error');
			});
		})
    }
    return false;
});

function disabledCoreFieldBtn(num){
    if(num>0){
		$('#modal-delbtn-core-fields').attr('disabled', false);
    } else{
		$('#modal-delbtn-core-fields').attr('disabled', true);
    }
}

function selectionCoreFieldGrid(url){
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function(){
		$.ajax({
			type: 'POST',
			url: url,
			data: $('.selectionCoreFieldIds:checked[name=\"selection[]\"]').serialize(),
			dataType: 'JSON',
			success: function(result) {
				if(result.status == 'success') {
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#core-fields-grid-pjax'});
				} else {
					". SDNoty::show('result.message', 'result.status') ."
				}
			}
		})
	
    })
}

function modalCoreField(url) {
    $('#modal-core-fields .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-core-fields').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>