<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\ArrayHelper;
use backend\modules\core\models\CoreFields;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\core\models\TablesFieldsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('core', 'Tables Fields');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="tables-fields-index">

    <div class="sdbox-header">
		<h3><?=  Html::encode($this->title).' #'. CoreFunc::itemAlias('tables_fields', $table) ?></h3>
    </div>
    
    <p style="padding-top: 10px;">
		<span class="label label-primary">Notice</span>
		<?= Yii::t('app', 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.') ?>
    </p>

    <?php  Pjax::begin(['id'=>'tables-fields-grid-pjax']);?>
    <?= GridView::widget([
		'id' => 'tables-fields-grid',
		'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['tables-fields/create', 'table'=>$table]), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-tables-fields']). ' ' .
					  Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['tables-fields/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-tables-fields', 'disabled'=>true]),
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => [
					'class' => 'selectionTablesFieldIds'
				],
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:40px;text-align: center;'],
			],
			[
				'class' => 'yii\grid\SerialColumn',
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:60px;text-align: center;'],
			],
			[
				'attribute'=>'input_label',
				'contentOptions'=>['style'=>'width:150px;'],
			],
			[
				'attribute'=>'table_varname',
				'contentOptions'=>['style'=>'width:150px;'],
			],
			[
				'attribute'=>'table_field_type',
				'contentOptions'=>['style'=>'width:120px;'],
				'filter'=>Html::activeDropDownList($searchModel, 'table_field_type', CoreFunc ::itemAlias('field_type'), ['class'=>'form-control', 'prompt'=>'All']),
			],
			[
				'attribute'=>'table_length',
				'contentOptions'=>['style'=>'width:100px;'],
			],
			'table_default',
			[
				'attribute'=>'input_field',
				'contentOptions'=>['style'=>'width:160px;'],
				'filter'=>Html::activeDropDownList($searchModel, 'input_field', ArrayHelper::map(CoreFields::find()->all(), 'field_code', 'field_code'), ['class'=>'form-control', 'prompt'=>'All']),
			],
			[
				'attribute'=>'input_required',
				'value'=>function ($data){return ($data['input_required']===1)?'Yes':'No';},
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:60px;text-align: center;'],
				'filter'=>Html::activeDropDownList($searchModel, 'input_required', [1=>'Yes', 0=>'No'], ['class'=>'form-control', 'prompt'=>'All']),
			],
			[
				'attribute'=>'input_order',
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:60px;text-align: center;'],
			],
			[
				'attribute'=>'updated_at',
				'value'=>function ($data){return Yii::$app->formatter->asDate($data['updated_at'], 'd/M/Y');},
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:100px;text-align: center;'],
				'filter'=>'',
			],
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
    'id' => 'modal-tables-fields',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#tables-fields-grid-pjax').on('click', '#modal-addbtn-tables-fields', function(){
    modalTablesField($(this).attr('data-url'));
});

$('#tables-fields-grid-pjax').on('click', '#modal-delbtn-tables-fields', function(){
    selectionTablesFieldGrid($(this).attr('data-url'));
});

$('#tables-fields-grid-pjax').on('click', '.select-on-check-all', function(){
    window.setTimeout(function() {
		var key = $('#tables-fields-grid').yiiGridView('getSelectedRows');
		disabledTablesFieldBtn(key.length);
    },100);
});

$('#tables-fields-grid-pjax').on('click', '.selectionTablesFieldIds', function(){
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledTablesFieldBtn(key.length);
});

$('#tables-fields-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalTablesField('".Url::to(['tables-fields/update', 'id'=>''])."'+id);
});	

$('#tables-fields-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view'){
		modalTablesField(url);
    } else if(action === 'delete') {
		yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
			$.post(
				url
			).done(function(result){
				if(result.status == 'success'){
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#tables-fields-grid-pjax'});
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

function disabledTablesFieldBtn(num){
    if(num>0){
		$('#modal-delbtn-tables-fields').attr('disabled', false);
    } else{
		$('#modal-delbtn-tables-fields').attr('disabled', true);
    }
}

function selectionTablesFieldGrid(url){
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function(){
		$.ajax({
			method: 'POST',
			url: url,
			data: $('.selectionTablesFieldIds:checked[name=\"selection[]\"]').serialize(),
			dataType: 'JSON',
			success: function(result, textStatus) {
				if(result.status == 'success') {
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#tables-fields-grid-pjax'});
				} else {
					". SDNoty::show('result.message', 'result.status') ."
				}
			}
		})
	
    })
}

function modalTablesField(url) {
    $('#modal-tables-fields .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-tables-fields').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>