<?php
//use Yii;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\core\models\CoreGenerateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('core', 'Core Generates');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="core-generate-index">

    <div class="sdbox-header">
		<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php	// echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="padding-top: 10px;">
		<span class="label label-primary">Notice</span>
		<?= Yii::t('app', 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.') ?>
    </p>

    <?php  Pjax::begin(['id'=>'core-generate-grid-pjax']);?>
    <?= GridView::widget([
		'id' => 'core-generate-grid',
		'panelBtn' => Html::a(SDHtml::getBtnAdd(), Url::to(['core-generate/create']), ['class' => 'btn btn-success btn-sm']). ' ' .
					  Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['core-generate/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-core-generate', 'disabled'=>true]),
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => [
					'class' => 'selectionCoreGenerateIds'
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
				'attribute'=>'gen_group',
				'value'=>function ($data){return CoreFunc::itemAlias('gen_group', $data['gen_group']);},
				'filter'=>Html::activeDropDownList($searchModel, 'gen_group', CoreFunc::itemAlias('gen_group'), ['class'=>'form-control', 'prompt'=>'All']),
				'contentOptions'=>['style'=>'width:140px;'],
			],
			[
				'attribute'=>'gen_name',
				'contentOptions'=>['style'=>'width:180px;'],
			],
			[
				'attribute'=>'gen_tag',
				'contentOptions'=>['style'=>'width:200px;'],
			],
			'gen_link:ntext',
			// 'gen_process:ntext',
			// 'gen_ui:ntext',
			// 'template_php:ntext',
			// 'template_html:ntext',
			// 'template_js:ntext',

			[
				'class' => 'appxq\sdii\widgets\ActionColumn',
				'contentOptions'=>['style'=>'width:110px;text-align: center;'],
				'template' => '{generate} {clone} {view} {update} {delete}',
				'buttons'=>[
					'update' => function ($url, $data, $key) {
						if(Yii::$app->user->id==$data['created_by']){
							return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
								'data-action' => 'update',
								'title' => Yii::t('yii', 'Update'),
								'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
							]);
						}
					},
					'delete' => function ($url, $data, $key) {
						if(Yii::$app->user->id==$data['created_by']){
							return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
								'data-action' => 'delete',
								'title' => Yii::t('yii', 'Delete'),
								'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
								'data-method' => 'post',
								'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
							]);
						}
					},
					'generate' => function ($url, $data, $key) {
						return Html::a('<span class="glyphicon glyphicon-flash"></span>', Url::to(['core-generate/generate', 'id'=>$data['gen_id']]), [
							'data-action' => 'generate',
							'title' => Yii::t('yii', 'Generate'),
							'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
						]);
					},
					'clone' => function ($url, $data, $key) {
						return Html::a('<span class="glyphicon glyphicon-transfer"></span>', Url::to(['core-generate/clone', 'id'=>$data['gen_id']]), [
							'data-action' => 'clone',
							'title' => Yii::t('yii', 'Clone'),
							'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
						]);
					}
				],
			],
		],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-core-generate',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#core-generate-grid-pjax').on('click', '#modal-addbtn-core-generate', function(){
    modalCoreGenerate($(this).attr('data-url'));
});

$('#core-generate-grid-pjax').on('click', '#modal-delbtn-core-generate', function(){
    selectionCoreGenerateGrid($(this).attr('data-url'));
});

$('#core-generate-grid-pjax').on('click', '.select-on-check-all', function(){
    window.setTimeout(function() {
		var key = $('#core-generate-grid').yiiGridView('getSelectedRows');
		disabledCoreGenerateBtn(key.length);
    },100);
});

$('#core-generate-grid-pjax').on('click', '.selectionCoreGenerateIds', function(){
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledCoreGenerateBtn(key.length);
});

$('#core-generate-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    //window.open('".Url::to(['core-generate/generate', 'id'=>''])."'+id,'_blank');
    location.href = '".Url::to(['core-generate/generate', 'id'=>''])."'+id;
});	

$('#core-generate-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'clone'){
		location.href = url;
    } else if(action === 'generate') {
		//window.open(url,'_blank');
		location.href = url;
    } else if(action === 'view') {
		modalCoreGenerate(url);
    } else if(action === 'delete') {
		yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
			$.post(
				url
			).done(function(result){
				if(result.status == 'success'){
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#core-generate-grid-pjax'});
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

function disabledCoreGenerateBtn(num){
    if(num>0){
		$('#modal-delbtn-core-generate').attr('disabled', false);
    } else{
		$('#modal-delbtn-core-generate').attr('disabled', true);
    }
}

function selectionCoreGenerateGrid(url){
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function(){
		$.ajax({
			method: 'POST',
			url: url,
			data: $('.selectionCoreGenerateIds:checked[name=\"selection[]\"]').serialize(),
			dataType: 'JSON',
			success: function(result, textStatus) {
				if(result.status == 'success') {
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#core-generate-grid-pjax'});
				} else {
					". SDNoty::show('result.message', 'result.status') ."
				}
			}
		})
	
    })
}

function modalCoreGenerate(url) {
    $('#modal-core-generate .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-core-generate').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>