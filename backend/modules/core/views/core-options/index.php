<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\ArrayHelper;
use backend\modules\core\models\CoreFields;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\core\models\CoreOptionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('core', 'Core Options');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-options-index">

    <div class="sdbox-header">
		<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="padding-top: 10px;">
		<span class="label label-primary">Notice</span>
		<?= Yii::t('app', 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.') ?>
    </p>
    
    <?php  Pjax::begin(['id'=>'core-options-grid-pjax']);?>
    <?= GridView::widget([
		'id' => 'core-options-grid',
		'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['core-options/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-core-options']). ' ' .
					  Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['core-options/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-core-options', 'disabled'=>true]),
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => [
					'class' => 'selectionCoreOptionIds'
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
				'attribute'=>'option_name',
				'contentOptions'=>['style'=>'width:200px;'],
			],
			[
				'attribute'=>'input_label',
				//'contentOptions'=>['style'=>'width:160px;'],
			],
//			[
//				'attribute'=>'option_value',
//				'contentOptions'=>['style'=>'width:200px;'],
//			],
			//'input_data:ntext',
			//'input_hint:ntext',
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
				'attribute'=>'autoload',
				'value'=>function ($data){return ($data['autoload']===1)?'Yes':'No';},
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:60px;text-align: center;'],
				'filter'=>Html::activeDropDownList($searchModel, 'autoload', [1=>'Yes', 0=>'No'], ['class'=>'form-control', 'prompt'=>'All']),
			],
			[
				'attribute'=>'input_order',
				'headerOptions'=>['style'=>'text-align: center;'],
				'contentOptions'=>['style'=>'width:60px;text-align: center;'],
			],
			// 
			// 'input_required',
			// 'input_validate:ntext',
			// 'input_meta:ntext',
			// 'input_order',

			[
				'class' => 'appxq\sdii\widgets\ActionColumn',
				'contentOptions'=>['style'=>'min-width:150px;width:150px;text-align: center;'],
                                'template' => '{config} {view} {update} {delete}',
                                'buttons' => [
                                    'config' => function ($url, $data, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-cog"></span> ' . Yii::t('yii', 'Config'), Url::to(['/core/core-options/config',
                                                            'term' => $data->option_name,
                                                        ]), [
                                                    'data-action' => 'config',
                                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                                    'class' => 'btn btn-warning btn-xs',
                                        ]);
                                    },
                                    
                                ],
			],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-core-options',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#core-options-grid-pjax').on('click', '#modal-addbtn-core-options', function(){
    modalCoreOption($(this).attr('data-url'));
});

$('#core-options-grid-pjax').on('click', '#modal-delbtn-core-options', function(){
    selectionCoreOptionGrid($(this).attr('data-url'));
});

$('#core-options-grid-pjax').on('click', '.select-on-check-all', function(){
    window.setTimeout(function() {
		var key = $('#core-options-grid').yiiGridView('getSelectedRows');
		disabledCoreOptionBtn(key.length);
    },100);
});

$('#core-options-grid-pjax').on('click', '.selectionCoreOptionIds', function(){
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledCoreOptionBtn(key.length);
});

$('#core-options-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalCoreOption('".Url::to(['core-options/update', 'id'=>''])."'+id);
});	

$('#core-options-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action == 'view'){
		modalCoreOption(url);
                return false;
    } else if(action === 'delete') {
		yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
			$.post(
				url
			).done(function(result){
				if(result.status == 'success'){
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#core-options-grid-pjax'});
				} else {
					". SDNoty::show('result.message', 'result.status') ."
				}
			}).fail(function(){
				". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
				console.log('server error');
			});
		});
              return false;  
    }
    
});

function disabledCoreOptionBtn(num){
    if(num>0){
		$('#modal-delbtn-core-options').attr('disabled', false);
    } else{
		$('#modal-delbtn-core-options').attr('disabled', true);
    }
}

function selectionCoreOptionGrid(url){
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function(){
		$.ajax({
			type: 'POST',
			url: url,
			data: $('.selectionCoreOptionIds:checked[name=\"selection[]\"]').serialize(),
			dataType: 'JSON',
			success: function(result) {
				if(result.status == 'success') {
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#core-options-grid-pjax'});
				} else {
					". SDNoty::show('result.message', 'result.status') ."
				}
			}
		})
	
    })
}

function modalCoreOption(url) {
    $('#modal-core-options .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-core-options').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>