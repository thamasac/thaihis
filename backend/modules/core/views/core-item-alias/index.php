<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\core\models\CoreItemAliasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('core', 'Core Item Aliases');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-item-alias-index">

    <div class="sdbox-header">
		<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="padding-top: 10px;">
		<span class="label label-primary">Notice</span>
		<?= Yii::t('app', 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.') ?>
    </p>

    <?php  Pjax::begin(['id'=>'core-item-alias-grid-pjax']);?>
    <?= GridView::widget([
		'id' => 'core-item-alias-grid',
		'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['core-item-alias/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-core-item-alias']). ' ' .
					  Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['core-item-alias/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-core-item-alias', 'disabled'=>true]),
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => [
					'class' => 'selectionCoreItemAliasIds'
				],
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:40px;text-align: center;'],
			],
			[
				'class' => 'yii\grid\SerialColumn',
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:60px;text-align: center;'],
			],

			'item_code',
			'item_name',
			'item_data:ntext',

			[
				'class' => 'appxq\sdii\widgets\ActionColumn',
				'contentOptions'=>['style'=>'width:80px;text-align: center;'],
				'template' => '{view} {update} {delete}',
			],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-core-item-alias',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#core-item-alias-grid-pjax').on('click', '#modal-addbtn-core-item-alias', function(){
    modalCoreItemAlias($(this).attr('data-url'));
});

$('#core-item-alias-grid-pjax').on('click', '#modal-delbtn-core-item-alias', function(){
    selectionCoreItemAliasGrid($(this).attr('data-url'));
});

$('#core-item-alias-grid-pjax').on('click', '.select-on-check-all', function(){
    window.setTimeout(function() {
		var key = $('#core-item-alias-grid').yiiGridView('getSelectedRows');
		disabledCoreItemAliasBtn(key.length);
    },100);
});

$('#core-item-alias-grid-pjax').on('click', '.selectionCoreItemAliasIds', function(){
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledCoreItemAliasBtn(key.length);
});

$('#core-item-alias-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalCoreItemAlias('".Url::to(['core-item-alias/update', 'id'=>''])."'+id);
});	

$('#core-item-alias-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view'){
		modalCoreItemAlias(url);
    } else if(action === 'delete') {
		yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
			$.post(
				url
			).done(function(result){
				if(result.status == 'success'){
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#core-item-alias-grid-pjax'});
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

function disabledCoreItemAliasBtn(num){
    if(num>0){
		$('#modal-delbtn-core-item-alias').attr('disabled', false);
    } else{
		$('#modal-delbtn-core-item-alias').attr('disabled', true);
    }
}

function selectionCoreItemAliasGrid(url){
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function(){
		$.ajax({
			method: 'POST',
			url: url,
			data: $('.selectionCoreItemAliasIds:checked[name=\"selection[]\"]').serialize(),
			dataType: 'JSON',
			success: function(result, textStatus) {
				if(result.status == 'success') {
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#core-item-alias-grid-pjax'});
				} else {
					". SDNoty::show('result.message', 'result.status') ."
				}
			}
		})
	
    })
}

function modalCoreItemAlias(url) {
    $('#modal-core-item-alias .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-core-item-alias').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>