<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;

$referrer = Yii::$app->request->referrer;
$this->params['breadcrumbs'][] = ['label' => Yii::t('graphconfig', 'Module'), 'url' => $referrer];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="inv-gen-form">

    <?php $form = ActiveForm::begin([
	'id'=>'config_form',
	'options'=>['enctype'=>'multipart/form-data'],
    ]); ?>

    <div class="modal-body">
	
	<div id="div-custom">
         
	<div class="panel panel-default" style="border-color: #2e6da4;">
	    <div class="panel-heading" style="background-color: #337ab7; color: #FFF;"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?= $this->title ?></div>
	    <div class="panel-body">
                
		<div id="dad-box" class="forms-items">
		    <?php
                   
                    if($data){
                        //#9CFAFA #CCFAFA
                        $bgcolor = '#fcf8e3';
			$border_color = '#faebcc';
                        foreach ($data as $datarow) {
                            echo $this->renderAjax('_config_widget', ['data'=>$datarow, 'bgcolor'=>$bgcolor, 'border_color'=>$border_color, 'id'=>Yii::$app->request->get('id',''), 
                                'conftype'=>0, 'list'=>[] ]);
                            if($bgcolor=='#fcf8e3'){
				    $bgcolor = '#dff0d8';
				    $border_color = '#d6e9c6';
				} else {
				    $bgcolor = '#fcf8e3';
				    $border_color = '#faebcc';
				}
                        }
                    }
		   ?>
		</div>
		    
		<div class="row add-forms">
		    <div class="col-md-10"></div>
		    <div class="col-md-2 sdbox-col" style="text-align: right;">
                        <button type="button" data-url="<?=  Url::to(['/graphconfig/graphconfig/get-widget', 'view'=>'_config_widget'])?>" class="forms-items-add btn btn-success">
                                <i class="glyphicon glyphicon-plus"></i> <?= Yii::t('app', 'New'); ?>
                        </button>
                    </div>
		</div>
	    </div>
	</div>
        </div>
    </div>
    <div class="modal-footer">
        <?= Html::hiddenInput('forms[referrer]', $referrer); ?>
	<?= Html::submitButton( Yii::t('app', 'Update'), ['class' => 'btn btn-primary', 'name'=>'action_submit', 'value'=>'submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  


$this->registerJs("

$('.add-forms').on('click', '.forms-items-add', function(){
    getWidget($(this).attr('data-url') ,$('.forms-items'));
});
function getWidget(url, appendId, id=0) {
    var widgetnum = $('.report-widget-box').length;
    $.ajax({
	method: 'POST',
	url: url,
	data: {id:id, widgetnum : widgetnum+1},
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $(appendId).append(result);
	}
    });
}
/*
$('#dad-box').dad({
    draggable:'.draggable',
    callback:function(e){
	
    }
});

*/
$( '#dad-box' ).sortable({
      revert: true
    });
");?>