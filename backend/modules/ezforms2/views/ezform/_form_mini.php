<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use kartik\tree\TreeViewInput;
use backend\modules\ezforms2\models\EzformTree;
use kartik\widgets\Select2;
use backend\modules\core\classes\CoreFunc;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Ezform */
/* @var $form yii\bootstrap\ActiveForm */
//\backend\modules\ezforms2\assets\UploadAsset::register($this);
$auto = isset($auto)?(int)$auto:0;
?>

<div class="ezform-form">
    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
               //,'action' => ($model->isNewRecord ? '' : Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id])),
               'layout' => 'horizontal',
		'fieldConfig' => [
			'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
			'horizontalCssClasses' => [
				'label' => 'col-sm-2',
				//'offset' => 'col-sm-offset-3',
				'wrapper' => 'col-sm-6',
				'error' => '',
				'hint' => '',
			],
		],
    ]);
    ?>
    
    <div class="modal-body ">
        <div class="row">
          <div class="col-md-3 text-center">
              <div class="upload-msg">
                <?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($model, 100, ['id'=>'preview_icon',])?>
              </div>
          </div>
            <div class="col-md-9">
              <?=$form->field($model, 'ezf_icon')->widget('appxq\sdii\widgets\SDUploadIcon', [
                                      
                                  ])->label(Yii::t('ezform', '1. Edit Icon'))?>
              
              <?= $form->field($model, 'ezf_name')->textInput(['maxlength' => true])->label('<i class="fa fa-asterisk" style="color:#ff0000"></i> 2. Edit Short Name') ?>
              
              <?= $form->field($model, 'ezf_detail')->textarea(['rows' => 3])->label('3. Edit Long Name') ?>
              
              <div class="form-group">
                <label class="control-label col-sm-2"> </label>
                <div class="col-sm-6">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['id'=>'submit-form', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
              </div>
          </div>
      </div>
      
      <div style="display: none;">
        <div class='row'>
            <div class='col-md-6'>
                
            </div>
            <div class='col-md-6'>
                <?php
                if ($model->isNewRecord) {
                    $userName = Yii::$app->user->identity->profile->attributes;
                } else {
                    $userName = common\modules\user\models\User::findOne(['id' => $model->created_by])->profile->attributes;
                }
                echo $form->field($model, 'created_by')->textInput(['value' => $userName['firstname'] . ' ' . $userName['lastname'], 'disabled' => true]);
                ?>
            </div>
        </div>
        
        <div class="box box-primary" id="ezfBoxSet" >
            <div class="box-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#ezfSetTab1" aria-controls="ezfSetTab1" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'General settings')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab2" aria-controls="ezfSetTab2" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'Properties')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab3" aria-controls="ezfSetTab3" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'Add-on scripts')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab4" aria-controls="ezfSetTab4" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'Form and Data Sharing')?></a></li>
                </ul>

                <!-- Tab panes -->
                <div class="panel-body">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="ezfSetTab1">
                            <div class='row'>
                                <div class='col-md-6'>
                                    <?=
                $form->field($model, 'category_id')->widget(TreeViewInput::classname(), [
                    'name' => 'category_id',
                    'id' => 'category_id',
                    'query' => EzformTree::find()->where('readonly=1 or userid=' . Yii::$app->user->id . ' or id IN (select distinct root from ezform_tree where userid=' . Yii::$app->user->id . ')')->addOrderBy('root, lft'),
                    'headingOptions' => ['label' => 'Categories'],
                    'asDropdown' => true,
                    'multiple' => false,
                    'fontAwesome' => true,
                    'rootOptions' => [
                        'label' => '<i class="fa fa-home"></i> ',
                        'class' => 'text-success',
                        'options' => ['disabled' => false]
                    ],
                ])
                ?>
                                </div>
                                <div class='col-md-6'>
                                    <?php
                                    $model->co_dev = SDUtility::string2Array($model->co_dev); // initial value
                                    $dataCodev = [];
                                    if(!empty($model->co_dev)){
                                        $user_init = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn(implode(',', $model->co_dev));
                                        if($user_init){
                                            $dataCodev = ArrayHelper::map($user_init, 'user_id', 'fullname');
                                        }
                                    }
                                    echo $form->field($model, 'co_dev')->widget(Select2::className(), [
                                        'options' => ['placeholder' => Yii::t('ezform', 'Co-creator'), 'multiple' => true, 'class' => 'form-control ezform-co_dev'],
                                        'data' => $dataCodev,
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
                            </div>
                            <div class='row'>
                                <div class='col-md-6'>
                                    
                                </div>
                                <div class='col-md-6'>
                                    <?php
                                    $model->field_detail = SDUtility::string2Array($model->field_detail); // initial value
                                    $field = ArrayHelper::map($modelFields, 'ezf_field_name', 'ezf_field_label');
                                    foreach ($field as $key=>$value) {
                                        if(empty($value)){
                                            $field[$key] = $key;
                                        }
                                    }
                                    
                                    
                                    echo $form->field($model, 'field_detail')->widget(Select2::classname(), [
                                        'data' => $field,
                                        'options' => ['placeholder' => Yii::t('ezform', 'Display fields'), 'multiple' => true],
                                        'maintainOrder'=>true,
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => Url::to(['/ezforms2/ezform/get-fields', 'ezf_id'=>$model->ezf_id, 'v'=>$model->ezf_version]),
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
                                    
                                    echo Html::activeHiddenInput($model, 'status');
                                    
                                    echo $form->field($model, 'ezf_crf')->checkbox();
                                    
                                    echo $form->field($model, 'ezf_db2')->checkbox();
                                  
                                    if ($model->enable_version) {
                                        echo Html::activeHiddenInput($model, 'enable_version');
                                    } else {
                                        echo $form->field($model, 'enable_version')->hint(Yii::t('ezform', 'Can not be modified when enabled.'))->checkbox();
                                    }
                                    
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    

                                    
                                    
                                    
                                  
                                </div>
                                <div class="col-md-6">
                                    <?php 
                                    if(isset($model->ezf_options['token'])){
                                        
                                        ?>
                                        <div class="form-group field-ezform-ezf_name">
                                            <label class="control-label" ><?= Yii::t('ezfomr', 'Token Unique Record')?></label>
                                            <?= backend\modules\ezforms2\classes\EzformWidget::radioList('Options[token_unique_record]', isset($model->ezf_options['token_unique_record'])?$model->ezf_options['token_unique_record']:0, ['data'=>['Disable', 'Enable']], ['inline'=>true])?>
                                        </div>
                                    
                                        <div class="alert alert-info" role="alert">
                                            <button id="btn-copy-link" type="button" class="close" ><span class="fa fa-clipboard" aria-hidden="true"></span></button>
                                            <div id="link-form">
                                                <?php echo Yii::getAlias('@backendUrl').Url::to(['/ezforms2/ezform-data/index', 'ezf_id'=>$model->ezf_id, 'token'=>$model->ezf_options['token']]);?>
                                            </div>
                                            
                                        </div>
                                   <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="ezfSetTab2">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->field($model, 'query_tools')->radioList([
                                        '1' => Yii::t('ezform', 'Disable'), 
                                        '2' => Yii::t('ezform', 'Enable for check error and no error only to be submitted'), 
                                        '3' => Yii::t('ezform', 'Enable for submission always possible'),
                                        ]); ?>
                                </div>
                                <div class="col-md-4">
                                        <?= $form->field($model, 'unique_record')->radioList([
                                            '1'=>Yii::t('ezform', 'Disable'), 
                                            '2'=>Yii::t('ezform', 'Enable') . ' ('.Yii::t('ezform', 'Add only 1 Record').')', 
                                            '3'=>Yii::t('ezform', 'Enable') . ' ('.Yii::t('ezform', 'Summit only 1 Record').') ',
                                            ]); ?>
                                </div>
                                <div class="col-md-4">
                                        <?= $form->field($model, 'consult_tools')->radioList([
                                            '1' => Yii::t('ezform', 'Disable'), 
                                            '2' => Yii::t('ezform', 'Enable')
                                            ]);
                                        ?>
                                  <div id="consult_tools_setting" >
                                        <?= $form->field($model, 'consult_telegram')->hiddenInput()->label(false); ?>
                                    <?php 
                                    $model->consult_users = SDUtility::string2Array($model->consult_users);
                                    if(!empty($model->consult_users)){
                                        $user_init = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn(implode(',', $model->consult_users));
                                        if($user_init){
                                            $model->consult_users = ArrayHelper::map($user_init, 'user_id', 'fullname');
                                        }
                                    }
                                    ?>
                                        <?=
                                        $form->field($model, 'consult_users')->widget(Select2::className(), [
                                            'options' => ['placeholder' => Yii::t('ezform', 'Consult admin'), 'multiple' => true],
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
                                            ]
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="ezfSetTab3">
                            <div class="row">
                                <div class="col-md-6 ">
                                    <?=
                                    $form->field($model, 'ezf_sql')->widget('appxq\sdii\widgets\AceEditor', [
                                        'mode' => 'mysql', // programing language mode. Default "html"
                                        'id' => 'ezf_sql'
                                    ]);
                                    ?>

                                </div>
                                <div class="col-md-6 sdbox-col">
                            <?=
                            $form->field($model, 'ezf_js')->widget('appxq\sdii\widgets\AceEditor', [
                                'mode' => 'javascript', // programing language mode. Default "html"
                                'id' => 'ezf_js'
                            ]);
                            ?>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="ezfSetTab4">
                          <div class="row">
                              <div class="col-md-4">
                                <?php
                                    if ($model->isNewRecord) {
                                        $model->shared = 0; //default value
                                    }
                                    echo $form->field($model, 'shared')->radioList([
                                        Yii::t('ezform', 'Private'),
                                        Yii::t('ezform', 'Public'),
                                        Yii::t('ezform', 'Assign to'),
                                        Yii::t('ezform', 'Everyone in site'),
                                        Yii::t('ezform', 'System'),
                                    ]);
                                    echo backend\modules\ezforms2\classes\EzformWidget::checkbox('Options[allowed_clone]', isset($model->ezf_options['allowed_clone'])?$model->ezf_options['allowed_clone']:0, ['label'=>'Allowed to clone']);
                                    
                                    ?>
                                    <?php
                                    $model->assign = SDUtility::string2Array($model->assign); // initial value
                                    $dataAssign = [];
                                    if(!empty($model->assign)){
                                        $user_assign = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn(implode(',', $model->assign));
                                        if($user_assign){
                                            $dataAssign = ArrayHelper::map($user_assign, 'user_id', 'fullname');
                                        }
                                    }
                                    echo $form->field($model, 'assign')->widget(Select2::className(), [
                                        'options' => ['placeholder' => Yii::t('ezform', 'Assign to'), 'multiple' => true],
                                        'data' => $dataAssign,
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => Url::to(['/ezforms2/ezform/get-user']),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                            ],
                                            //'initSelection' => new JsExpression('function (element, callback) { console.log(element); }'),
                                            'tags' => true,
                                            'tokenSeparators' => [',', ' '],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                                            'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                                        ],
                                    ])
                                    ?>
                            </div>
                              <div class="col-md-4 sdbox-col">
                                <?php 
                                    echo $form->field($model, 'public_listview')->radioList([
                                        '0' => Yii::t('ezform', 'Private'), 
                                        '1' => Yii::t('ezform', 'Public'), 
                                        '2' => Yii::t('ezform', 'All members within the same site'), 
                                        '3' => Yii::t('ezform', 'All members with the same unit'),
                                        ]);
                                    
                                    
                                    ?>
                                    
                                    <label><?= Yii::t('ezform', 'Data Editing Policy')?></label>
                                    <?php
                                    echo $form->field($model, 'public_edit')->checkbox(['value' => '1']); 
                                    echo $form->field($model, 'public_delete')->checkbox(['value' => '1']); 
                                    ?>
                            </div>
                          </div>
                          
        <?= Html::activeHiddenInput($model, 'xsourcex') ?>
        <?= Html::activeHiddenInput($model, 'ezf_table') ?>
        <?= Html::activeHiddenInput($model, 'ezf_error') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?= Html::activeHiddenInput($model, 'ezf_version') ?>
    <?= Html::activeHiddenInput($model, 'ezf_id') ?>
    <?= Html::activeHiddenInput($model, 'updated_at') ?>
<?= Html::activeHiddenInput($model, 'updated_by') ?>
<?= Html::activeHiddenInput($model, 'created_at') ?>
<?= Html::activeHiddenInput($model, 'created_by') ?>
    </div>
      </div>
  
<?php ActiveForm::end(); ?>

</div>
<?php $this->registerJs("

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    " . SDNoty::show('result.message', 'result.status') . "
	    if(result.action == 'create') {
                $(document).find('#modal-create').modal('hide');
		$.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
	    } else if(result.action == 'update') {
		$(document).find('#modal-create').modal('hide');
		$.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
	    }
	} else {
	    " . SDNoty::show('result.message', 'result.status') . "
	} 
    }).fail(function() {
	" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
    return false;
});


$('.field-ezform-assign').addClass('" . ($model->shared <> '2' ? 'hidden' : '') . "');
$('#consult_tools_setting').addClass('" . ($model->consult_tools <> '2' ? 'hidden' : '') . "');
$(\"input[name='Ezform[consult_tools]']\").on('change',function(){
    var consult_tools = $(\"input[name='Ezform[consult_tools]']:checked\").val();
    if(consult_tools === '2'){
        $('#consult_tools_setting').removeClass('hidden');
    }else {
        $('#consult_tools_setting').addClass('hidden');
    }
});
$(\"input[name='Ezform[shared]']\").on('change',function(){
    var shared = $(\"input[name='Ezform[shared]']:checked\").val();
    if(shared === '2'){
        $('.field-ezform-assign').removeClass('hidden');
    }else {
        $('.field-ezform-assign').addClass('hidden');
    }
    });
    
$('#btn-copy-link').on('click',function(){
    copyToClipboard('link-form');
    });


 function copyToClipboard(elementId) {

  var aux = document.createElement('input');
  aux.setAttribute('value', htmlDecode(document.getElementById(elementId).innerHTML));
  document.body.appendChild(aux);
  aux.select();
  document.execCommand('copy');

  document.body.removeChild(aux);

}

function htmlDecode(value){
  return $('<div/>').html(value).text();
}

"); ?>