<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use yii\web\JsExpression;
use appxq\sdii\utils\SDUtility;
use kartik\select2\Select2;
use backend\modules\ezforms2\classes\EzfFunc;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformCommunity */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-community-form">

    <?php $form = EzActiveForm::begin([
	'id'=>$model->formName().'-q-'.$model->object_id.'-'.$model->query_tool,
    ]); ?>
  
        <?php
        //echo $form->field($model, 'send_to')->hiddenInput()->label(FALSE);
            //$model->send_to = explode(',', $model->send_to); // initial value
            $itemInit = [];
            if(!empty($model->send_to)){
                $user_init = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn(implode(',', $model->send_to));
                if($user_init){
                    foreach ($user_init as $key => $value) {
                        $itemInit[] = ['id'=>$value['user_id'], 'text'=>$value['fullname']];
                    }
                    //$model->send_to = $itemInit;
                }
            }
            
            echo $form->field($model, 'send_to')->widget(Select2::className(), [
                'options' => ['placeholder' => Yii::t('ezform', 'Send To'), 'multiple' => true, 'class' => 'form-control', 'id'=>"qsend_to-{$model->object_id}-{$model->query_tool}"],
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to(['/ezforms2/ezform/get-user']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'tags' => true,
                    'tokenSeparators' => [',', ' '],
                    'initSelection' => new JsExpression('function (element, callback) { callback('.\yii\helpers\Json::encode($itemInit).'); }'),
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(result) { return result.text; }'),
                    'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                ],
            ]);
            ?>
  
        <?php 
                        $settings = [
                                'minHeight' => 30,
                                'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
                                'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
                                'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
                                'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
                                'plugins' => [
                                    'fontcolor',
                                    'fontfamily',
                                    'fontsize',
                                    'textdirection',
                                    'textexpander',
                                    'counter',
                                    'table',
                                    'definedlinks',
                                    'video',
                                    'imagemanager',
                                    'filemanager',
                                    'limiter',
                                    'fullscreen',
                                ],
                                'paragraphize'=>false,
                                'replaceDivs'=>false,
                        ];
                        $lang = Yii::$app->language;
                        if($lang!='en-US'){
                            $settings['lang'] = backend\modules\ezforms2\classes\EzfFunc::getLanguage();
                        }

                        echo $form->field($model, 'content')->widget(vova07\imperavi\Widget::className(), [
                        'settings' => $settings,
                            'options' => ['id'=>"qcontent-{$model->object_id}-{$model->query_tool}"]
                    ]);
                            
                            ?>
                    <?php
                    $consult_users = SDUtility::string2Array($modelEzf->consult_users);
                    $auth_approv = in_array(Yii::$app->user->id, $consult_users);
                    ?>
  
                    <?= $form->field($model, 'approv_status')->radioList(['data'=>[4=>'Remark', 0=>'Waiting', 1=>'Resolve with out any change.', 2=>'Resolve with some change.', 3=>'Unresolvable']], ['id'=>"approv_status-{$model->object_id}-{$model->query_tool}", 'style'=>$auth_approv?'':'display: none;'])->label($auth_approv) ?>
  <div id="change_value" class="row" style="display: none;">
    <div class="col-md-12">
    <?php
    $field = $modelFields;
    $field['ezf_field_name'] = 'value_old';
    
    if ($field['ezf_field_type'] > 0) {
                if (isset($field['ezf_field_ref']) && $field['ezf_field_ref'] > 0) {
                    $cloneRefField = EzfFunc::cloneRefField($field);
                    $field = $cloneRefField['field'];
                    $disabled = $cloneRefField['disabled'];
                    if($disabled){
                        $inputDisable[$field['ezf_field_name']]=$disabled;
                    }
                }

                $dataInput;
                if (isset($ezf_input)) {
                    $dataInput = EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                }

                $disabled = isset($inputDisable[$field['ezf_field_name']]) ? 2 : 0;
                
                $html = EzfFunc::generateInput($form, $model, $field, $dataInput, $disabled, $modelEzf, 0);
                echo $html;
            }
            
    ?>
      <?php
    $field = $modelFields;
    $field['ezf_field_name'] = 'value_new';
    if ($field['ezf_field_type'] > 0) {
                if (isset($field['ezf_field_ref']) && $field['ezf_field_ref'] > 0) {
                    $cloneRefField = EzfFunc::cloneRefField($field);
                    $field = $cloneRefField['field'];
                    $disabled = $cloneRefField['disabled'];
                    if($disabled){
                        $inputDisable[$field['ezf_field_name']]=$disabled;
                    }
                }

                $dataInput;
                if (isset($ezf_input)) {
                    $dataInput = EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                }

                $disabled = isset($inputDisable[$field['ezf_field_name']]) ? 2 : 0;

                $html = EzfFunc::generateInput($form, $model, $field, $dataInput, $disabled, $modelEzf, 0);
                echo $html;
            }
            
    ?>
      </div>
  </div>
                    
  
  
	<?= $form->field($model, 'id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'parent_id')->hiddenInput()->label(FALSE) ?>

	

	<?= $form->field($model, 'type')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'object_id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'dataid')->hiddenInput()->label(FALSE) ?>


	<?= $form->field($model, 'query_tool')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'field')->hiddenInput()->label(FALSE) ?>

	

	<?= $form->field($model, 'approv_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'approv_date')->hiddenInput()->label(FALSE) ?>

	

	<?= $form->field($model, 'status')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_at')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_at')->hiddenInput()->label(FALSE) ?>

    <div class="text-right">
	<?= Html::submitButton('<i class="glyphicon glyphicon-comment"></i> '. Yii::t('app', 'Post'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php EzActiveForm::end(); ?>

</div>

<?php  $this->registerJs("
    var status = $('input[type=radio][name=\"EzformCommunity[approv_status]\"]:checked').val();
if(status==2){
$('#change_value').show();
}
    $('input[type=radio][name=\"EzformCommunity[approv_status]\"]').change(function() {
   if (this.value == 2) {
    $('#change_value').show();
    }
  else {
    $('#change_value').hide();
 }
 });
 
$('form#{$model->formName()}-q-{$model->object_id}-{$model->query_tool}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    ". SDNoty::show('result.message', 'result.status') ."
	    //เพิ่มรายการ
            //load form ใหม่
            
            var count = Number($('#qcount-$object_id-$query_tool').html())+1;
            $('#qcount-$object_id-$query_tool').html(count);
            $('#qcomment-list-$object_id-$query_tool').append(result.html);
            getqComment();
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
	} 
    }).fail(function() {
	". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
	console.log('server error');
    });
    return false;
});

function getqComment() {
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/ezforms2/ezform-community/qcomment', 
                    'dataid' => $dataid,
                    'object_id' => $object_id,
                    'query_tool' => $query_tool,
                    'parent_id' => $parent_id,
                    'field' => $field,
                    'type' => $type,
                    'limit' => $limit,
                ]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#qcomment-box-$object_id-$query_tool').html(result);
                }
            });
        }
");?>
<?php \appxq\sdii\widgets\CSSRegister::begin([
    //'key' => 'bootstrap-modal',
    //'position' => []
]); ?>
<style>
    /*CSS script*/
    #ezformcommunity-value_old{
        pointer-events: none;
        opacity: 0.6;
    }
</style>
<?php \appxq\sdii\widgets\CSSRegister::end(); ?>