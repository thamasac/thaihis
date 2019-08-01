<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzformWidget;
use appxq\sdii\utils\SDUtility;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\linebot\models\LineFunctions */
/* @var $form yii\bootstrap\ActiveForm */
$role = \appxq\sdii\utils\SDUtility::string2Array($model->role);
$api = \appxq\sdii\utils\SDUtility::string2Array($model->api);
?>

<div class="line-functions-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">Line Functions</h4>
    </div>

    <div class="modal-body">
	
      <div class="row">
            <div class="col-md-6 "><?= $form->field($model, 'channel_id')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-6 sdbox-col"><?= $form->field($model, 'command')->textInput(['maxlength' => true]) ?></div>
      </div>
      
      <div class="form-group">
        <label class="control-label" >Api <?=$api['type']==2?></label>
        <?= EzformWidget::radioList('LineFunctions[api][type]', isset($api['type'])?$api['type']:0, ['data'=>['None', 'API']], ['inline'=>true])?>
    </div>
      <div id="box-api" style="<?=isset($api['type']) && $api['type']==1?'display: block;':'display: none;'?>">
          <div class="form-group">
            <label class="control-label" >URL</label>
            <?= Html::textInput('LineFunctions[api][url]', isset($api['url'])?$api['url']:'', ['class'=>'form-control', 'id'=>'api_url'])?>
        </div>
      </div>
      <div id="box-ezform" style="<?=isset($api['type']) && $api['type']==2?'display: block;':'display: none;'?>">
          <?php
          $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();
          ?>
          <div class="form-group">
            <div class="row">
                <div class="col-md-6 ">
                    <?php
                    $attrname_ezf_id = 'LineFunctions[api][ezf_id]';
                    $value_ezf_id = isset($api['ezf_id'])?$api['ezf_id']:'';
                    ?>
                    <?= Html::label(Yii::t('ezform', 'Form to work'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                    <?php 
                    echo kartik\select2\Select2::widget([
                        'name' => $attrname_ezf_id,
                        'value'=> $value_ezf_id,
                        'options' => ['placeholder' => Yii::t('ezform', 'Select Form'), 'id'=>'config_line_ezf_id'],
                        'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
              </div>
                <div class="col-md-6 sdbox-col">
                    <?php
                    $attrname_fields = 'LineFunctions[api][fields]';
                    $value_fields_json = isset($api['fields']) && is_array($api['fields'])?\appxq\sdii\utils\SDUtility::array2String($api['fields']):'{}';
                    ?>
                      <?= Html::label(Yii::t('ezform', 'Fields to display'), $attrname_fields, ['class' => 'control-label']) ?>
                      <div id="ref_field_box">

                      </div>
              </div>
          </div>
            
        </div>
        
      </div>
       

	<?= $form->field($model, 'template')->textarea(['rows' => 6])->hint(Html::a('Message Simulator', 'https://developers.line.me/console/fx/', ['target'=>'_blank']).' / '. Html::a('Bot Designer', 'https://developers.line.me/en/services/bot-designer/', ['target'=>'_blank'])) ?>

      <div class="form-group">
            <label class="control-label" for="linefunctions-role">Share</label>
            <?= EzformWidget::radioList('LineFunctions[role][public]', isset($role['public'])?$role['public']:0, ['data'=>['Private', 'Public']], ['inline'=>true])?>
        </div>
        <div class="form-group">
          <div class="row">
          <div class="col-md-6 ">
                <label class="control-label" for="linefunctions-role">User</label>
            <?php
            $user_role = [];
            if(isset($role['user'])){
                $user_role = $role['user']; // initial value
            }
            $dataUser = [];
            if(!empty($user_role)){
                $user_init = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn(implode(',', $user_role));
                if($user_init){
                    $dataUser = ArrayHelper::map($user_init, 'user_id', 'fullname');
                }
            }
            
            echo kartik\widgets\Select2::widget([
                'id'=>'line_user_role',
                'name'=>'LineFunctions[role][user]',
                'value'=>$user_role,
                'options' => ['placeholder' => Yii::t('ezform', 'Select User'), 'multiple' => true, 'class' => 'form-control'],
                'data' => $dataUser,
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to(['/ezforms2/ezform/get-user']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'tags' => true,
                    'tokenSeparators' => [',', ' '],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(result) { return result.text; }'),
                    'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                ],
            ]);
            
            ?>
        </div>
          <div class="col-md-6 sdbox-col">
                <label class="control-label" for="linefunctions-role">Role</label>
            <?php
            $role_value = [];
            if(isset($role['role'])){
                $role_value = $role['role']; // initial value
            }
            $Roles = Yii::$app->authManager->getRoles();
            $dataRole = ArrayHelper::map($Roles, 'name', 'description');
            
            echo kartik\widgets\Select2::widget([
                'id'=>'line_role_role',
                'name'=>'LineFunctions[role][role]',
                'value'=>$role_value,
                'options' => ['placeholder' => Yii::t('ezform', 'Select Role'), 'multiple' => true, 'class' => 'form-control'],
                'data' => $dataRole,
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                    'tokenSeparators' => [',', ' '],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(result) { return result.text; }'),
                    'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                ],
            ]);
            
            ?>
        </div>
      </div>
      </div>
      
	<?= $form->field($model, 'active')->checkbox() ?>
      
        <?= $form->field($model, 'options')->hiddenInput()->label(FALSE) ?>
      
        <?= $form->field($model, 'id')->hiddenInput()->label(FALSE) ?>
      
	<?= $form->field($model, 'updated_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_at')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_at')->hiddenInput()->label(FALSE) ?>

    </div>
    <div class="modal-footer">
	<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
// JS script
fields($('#config_line_ezf_id').val());

$('#config_line_ezf_id').on('change',function(){
    var ezf_id = $(this).val();
    fields(ezf_id);
});
    
function fields(ezf_id){
    var value = <?=$value_fields_json?>;
    $.post('<?=Url::to(['/ezforms2/target/get-fields'])?>',{ ezf_id: ezf_id, multiple:1, name: '<?=$attrname_fields?>', value: value ,id:'config_line_fields'}
      ).done(function(result){
         $('#ref_field_box').html(result);
      }).fail(function(){
          <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"')?>
          console.log('server error');
      });
}
    
$('input[type="radio"][name="LineFunctions[api][type]"]').change(function() {
    if (this.value == 1) {
        $('#box-api').show();
        $('#box-ezform').hide();
        $('#config_line_ezf_id').val('');
        $('#config_line_ezf_id').trigger('change');
    } else if (this.value == 2) {
        $('#box-api').hide();
        $('#box-ezform').show();
        $('#api_url').val('');
    } else {
        $('#box-api').hide();
        $('#box-ezform').hide();
        $('#api_url').val('');
        $('#config_line_ezf_id').val('');
        $('#config_line_ezf_id').trigger('change');
    }
});
    
$('form#<?= $model->formName()?>').on('beforeSubmit', function(e) {
    var $form = $(this);
    $.post(
        $form.attr('action'), //serialize Yii2 form
        $form.serialize()
    ).done(function(result) {
        if(result.status == 'success') {
            <?= SDNoty::show('result.message', 'result.status')?>
            if(result.action == 'create') {
                //$(\$form).trigger('reset');
                $(document).find('#modal-line-functions').modal('hide');
                $.pjax.reload({container:'#line-functions-grid-pjax'});
            } else if(result.action == 'update') {
                $(document).find('#modal-line-functions').modal('hide');
                $.pjax.reload({container:'#line-functions-grid-pjax'});
            }
        } else {
            <?= SDNoty::show('result.message', 'result.status')?>
        } 
    }).fail(function() {
        <?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
        console.log('server error');
    });
    return false;
});
</script>
<?php  \richardfan\widget\JSRegister::end(); ?>