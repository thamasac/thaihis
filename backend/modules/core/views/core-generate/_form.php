<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreGenerate */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="core-generate-form">

    <?php $form = ActiveForm::begin(['id'=>$model->formName()]); ?>
	<div class="modal-header">
	    <h4 class="modal-title" id="itemModalLabel">Core Generate</h4>
	</div>

	<div class="modal-body">
	    <div class="row">
			<div class="col-md-2">
				<?= $form->field($model, 'gen_group')->dropDownList(CoreFunc::itemAlias('gen_group')) ?>
			</div>
			<div class="col-md-3 sdbox-col">
				<?= $form->field($model, 'gen_name')->textInput(['maxlength' => true]) ?>
			</div>
			<div class="col-md-3 sdbox-col">
				<?= $form->field($model, 'gen_tag')->textInput() ?>
			</div>
			<div class="col-md-4 sdbox-col">
				<?= $form->field($model, 'gen_link')->textInput() ?>
			</div>
	    </div>
	    
	    <?php  Pjax::begin(['id'=>'generate-fields-grid-pjax']);?>
	    <?= GridView::widget([
			'id' => 'generate-fields-grid',
			'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['core-generate/create-field']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-generate-fields']).' '.
						  Html::button(SDHtml::getBtnRepeat(), ['data-url'=>Url::to(['core-generate/reset-field']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-resetbtn-generate-fields']),
			'dataProvider' => $modelUi->fieldTmp(),
			'columns' => [
				[
					'class' => 'yii\grid\SerialColumn',
					'headerOptions'=>['style'=>'text-align: center;'],
					'contentOptions'=>['style'=>'width:50px;text-align: center;'],
				],
				[
					'attribute'=>'option_name',
					'label'=>Yii::t('core', 'Name'),
					'contentOptions'=>['style'=>'width:140px;'],
				],
				[
					'attribute'=>'input_field',
					'label'=>Yii::t('core', 'Field'),
					'contentOptions'=>['style'=>'width:140px;'],
				],
				[
					'attribute'=>'option_value',
					'label'=>Yii::t('core', 'Value'),
					'options'=>['style'=>'width:120px;'],
				],
				[
					'attribute'=>'input_data',
					'label'=>Yii::t('core', 'Data'),
				],
				[
					'attribute'=>'input_required',
					'label'=>Yii::t('core', 'Required'),
					'value'=>function ($data){return ($data['input_required']==1)?'True':'False';},
					'contentOptions'=>['style'=>'width:60px;text-align: center;'],
				],
				[
					'attribute'=>'input_order',
					'label'=>Yii::t('core', 'Order'),
					'contentOptions'=>['style'=>'width:60px;text-align: center;'],
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'contentOptions'=>['style'=>'width:40px;text-align: center;'],
					'template'=>'{delete}',
					'buttons'=>[
						'delete' => function ($url, $data, $key) {
							return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['core-generate/delete-field', 'id'=>$data['option_name']]), [
								'data-action' => 'delete',
								'title' => Yii::t('yii', 'Delete'),
								'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
								'data-method' => 'post',
								'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
							]);
						}
					],

				],
			],  
	    ]);	?>
	    <?php  Pjax::end();?>

	    <?= Html::activeHiddenInput($model, 'updated_at') ?>
		<?= Html::activeHiddenInput($model, 'updated_by') ?>
		<?= Html::activeHiddenInput($model, 'created_at') ?>
		<?= Html::activeHiddenInput($model, 'created_by') ?>
	    
	    <div class="row">
			<div class="col-md-6">
                          <?=
                            $form->field($model, 'template_html')->widget('appxq\sdii\widgets\AceEditor', [
                                'mode' => 'html', // programing language mode. Default "html"
                                'id' => 'template_html',
                                'options'=>['rows' => 3, 'placeholder'=>'Template HTML.']
                            ]);
                            ?>
                          <?=
                            $form->field($model, 'template_php')->widget('appxq\sdii\widgets\AceEditor', [
                                'mode' => 'php', // programing language mode. Default "html"
                                'id' => 'template_php',
                                'options'=>['rows' => 3, 'placeholder'=>'Template PHP.']
                            ]);
                            ?>
			</div>
			<div class="col-md-6 sdbox-col">
                            <?=
                            $form->field($model, 'template_js')->widget('appxq\sdii\widgets\AceEditor', [
                                'mode' => 'javascript', // programing language mode. Default "html"
                                'id' => 'template_js',
                                'options'=>['rows' => 3, 'placeholder'=>'Template JavaScript.']
                            ]);
                            ?>
                             <?=
                            $form->field($model, 'gen_process')->widget('appxq\sdii\widgets\AceEditor', [
                                'mode' => 'php', // programing language mode. Default "html"
                                'id' => 'gen_process',
                                'options'=>['rows' => 3, 'placeholder'=>'PHP Code.']
                            ])->hint('Use <b>$fields</b> set property.');
                            ?>
			</div>
	    </div>
	    
	</div>
	<div class="modal-footer">
	    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-generate-fields',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("

$('#generate-fields-grid-pjax').on('click', '#modal-resetbtn-generate-fields', function(){
    var url = $(this).attr('data-url');
    yii.confirm('".Yii::t('app', 'Are you sure you want to reset this item?')."', function(){
		$.post(
			url
		).done(function(result){
			if(result.status == 'success'){
				". SDNoty::show('result.message', 'result.status') ."
				$.pjax.reload({container:'#generate-fields-grid-pjax'});
			} else {
				". SDNoty::show('result.message', 'result.status') ."
			}
		}).fail(function(){
			". SDNoty::show('"<strong><i class=\"glyphicon glyphicon-warning-sign\"></i> Error!</strong> "+e.responseJSON.message', '"error"') ."
			console.log('server error');
		});
    })
});

$('#generate-fields-grid-pjax').on('click', '#modal-addbtn-generate-fields', function(){
    modalGenerateFields($(this).attr('data-url'));
});

$('#generate-fields-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalGenerateFields('".Url::to(['core-generate/update-field', 'id'=>''])."'+id);
});

$('#generate-fields-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action == 'view'){
		modalGenerateFields(url);
    } else if(action === 'delete') {
		yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
			$.post(
				url
			).done(function(result){
				if(result.status == 'success'){
					". SDNoty::show('result.message', 'result.status') ."
					$.pjax.reload({container:'#generate-fields-grid-pjax'});
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

function modalGenerateFields(url) {
    $('#modal-generate-fields .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-generate-fields').modal('show')
    .find('.modal-content')
    .load(url);
}
");?>