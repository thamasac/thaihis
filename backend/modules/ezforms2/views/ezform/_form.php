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
                'id' => $model->formName()
                , 'action' => ($model->isNewRecord ? '' : Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id]))
    ]);
    ?>
    <?php if ($model->isNewRecord) { ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel">EzForm</h4>
    </div>
    <?php } ?>
    <div class="modal-body">
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'ezf_name')->textInput(['maxlength' => true])->label('<i class="fa fa-asterisk" style="color:#ff0000"></i> '.$model->getAttributeLabel('ezf_name')) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'ezf_detail')->textInput(['rows' => 8]) ?>
            </div>
        </div>
        
        <div class="box box-primary" id="ezfBoxSet" >
            <div class="box-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#ezfSetTab1" aria-controls="ezfSetTab1" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> <?= Yii::t('ezform', 'General settings')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab2" aria-controls="ezfSetTab2" role="tab" data-toggle="tab"><i class="fa fa-sliders"></i> <?= Yii::t('ezform', 'Properties')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab3" aria-controls="ezfSetTab3" role="tab" data-toggle="tab"><i class="fa fa-code"></i> <?= Yii::t('ezform', 'Add-on scripts')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab4" aria-controls="ezfSetTab4" role="tab" data-toggle="tab"><i class="fa fa-share"></i> <?= Yii::t('ezform', 'Form and Data Sharing')?></a></li>
                    <li role="presentation"><a  href="#event_form" aria-controls="event_form" role="tab" data-toggle="tab"><i class="fa fa-bolt"></i> <?= Yii::t('ezform', 'Form Event')?></a></li>
                </ul>

                <!-- Tab panes -->
                <div class="panel-body">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="ezfSetTab1">
                            <div class='row'>
                                <div class='col-md-6'>
                                    <?=
                                    $form->field($model, 'category_id')->widget(TreeViewInput::classname(), [
                                        //'name' => 'category_id',
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
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                                'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                                                    if(jqXHR.status&&jqXHR.status==403){
                                                        window.location.href = "'. Url::to(['/user/login']).'"
                                                    }
                                                }'),
                                            ],
                                            //'tags' => true,
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
                                    <?php
                                    if ($model->isNewRecord) {
                                        $userName = Yii::$app->user->identity->profile->attributes;
                                    } else {
                                        $userName = common\modules\user\models\User::findOne(['id' => $model->created_by])->profile->attributes;
                                    }
                                    echo $form->field($model, 'created_by')->textInput(['value' => $userName['firstname'] . ' ' . $userName['lastname'], 'disabled' => true]);
                                    ?>
                                    
                                  <?=$form->field($model, 'ezf_icon')->widget('appxq\sdii\widgets\SDUploadIcon', [
                                      
                                  ])->label(Yii::t('ezform', 'Icon'))?>
                                  
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
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                                'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                                                    if(jqXHR.status&&jqXHR.status==403){
                                                        window.location.href = "'. Url::to(['/user/login']).'"
                                                    }
                                                }'),
                                            ],
                                            //'tags' => true,
                                            'tokenSeparators' => [',', ' '],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                                            'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                                        ],
                                        
                                    ]);
                                    
                                    ?>
                                    <div class="form-group ">
                                      <div class="row">
                                              <div class="col-md-12">
                                                <label class="control-label" ><?= Yii::t('ezfrom', 'Displayed Column')?></label>
                                                <?=Html::dropDownList('Options[display_col]', isset($model->ezf_options['display_col'])?$model->ezf_options['display_col']:'0', [0=>'inline', 1=>1, 2=>2, 3=>3, 4=>4, 6=>6, 12=>12], ['class'=>'form-control']);?>
                                            </div>
                                        
                                          </div>
                                      
                                    </div>
                                    
                                    <div class="form-group ">
                                      <label class="control-label" ><?= Yii::t('ezfrom', 'Displayed Template')?> 
                                        <span class="btn btn-xs btn-info btn-tmp" data-tmp="<?= Html::encode('<span class="content_box"><b class="content_label">{label}: </b><span class="content_value">{value}</span> </span>')?>">inline</span> 
                                        <span class="btn btn-xs btn-info btn-tmp" data-tmp="<?= Html::encode('<dl class="content_box" style="margin-bottom: 5px;"><dt class="content_label">{label}</dt><dd class="content_value">{value}</dd></dl>')?>">Vertical</span> 
                                        <span class="btn btn-xs btn-info btn-tmp" data-tmp="<?= Html::encode('<dl class="dl-horizontal content_box" style="margin-bottom: 5px;"><dt class="content_label">{label}</dt><dd class="content_value">{value}</dd></dl>')?>">Horizontal</span>
                                      </label>
                                      <?=Html::textInput('Options[display_tmp]', isset($model->ezf_options['display_tmp'])?$model->ezf_options['display_tmp']:'<span class="content_box"><b class="content_label">{label}: </b><span class="content_value">{value}</span> </span>', ['class'=>'form-control', 'id'=>'ezf_display_tmp']);?>
                                    </div>
                                    
                                    <?php
                                    echo $form->field($model, 'ezf_crf')->checkbox();
                                    
                                    echo backend\modules\ezforms2\classes\EzformWidget::checkbox('Options[lock_data]', isset($model->ezf_options['lock_data'])?$model->ezf_options['lock_data']:0, ['label'=>Yii::t('ezform', 'Lock Data')]);
                                  
                                    echo $form->field($model, 'ezf_db2')->checkbox();
                                  
                                    if ($model->enable_version) {
                                        echo Html::activeHiddenInput($model, 'enable_version');
                                    } else {
                                        echo $form->field($model, 'enable_version')->hint(Yii::t('ezform', 'Can not be modified when enabled.'))->checkbox();
                                    }
                                    
                                    
                                    
                                    echo Html::activeHiddenInput($model, 'status');
                                    ?>
                                  
                                        <div class="form-group ">
                                          <div class="row">
                                              <div class="col-md-4">
                                                <label class="control-label" ><?= Yii::t('ezfrom', 'PopUp Size')?></label>
                                              <div class="input-group">
                                                <?= Html::textInput('Options[popup_size]', isset($model->ezf_options['popup_size'])?$model->ezf_options['popup_size']:'', ['class'=>'form-control', 'type'=>'number', 'min'=>0, 'max'=>100, 'placeholder'=>Yii::t('ezfrom', 'PopUp Size')])?>
                                                <span class="input-group-addon" id="basic-addon2">%</span>
                                              </div>
                                            </div>
                                            
                                          </div>
                                              
                                          </div>
                                  
                                  <?php 
                                    if(isset($model->ezf_options['token'])){
                                        
                                        ?>
                                        <div class="form-group ">
                                            <label class="control-label" ><?= Yii::t('ezfrom', 'Token Unique Record')?></label>
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
                                            '4'=>Yii::t('ezform', 'Enable') . ' ('.Yii::t('ezform', 'Add only 1 Record/Day').') ',
                                            '3'=>Yii::t('ezform', 'Enable') . ' ('.Yii::t('ezform', 'Summit only 1 Record').') ',
                                            ]); ?>
                                  
                                  <div id="perday-box" style=" <?=$model->unique_record==4?'display: block;':'display: none;'?>">
                                      <div class="form-group ">
                                        <div class="row">
                                          <div class="col-md-12 form-group">
                                          <label class="control-label" ><?= Yii::t('ezform', 'Created Date field (if not the system created date)')?></label>
                                            <?php
                                    $create_date_field = isset($model->ezf_options['create_date_field'])?$model->ezf_options['create_date_field']:'';
                                    
                                    echo kartik\select2\Select2::widget([
                                        'name' => 'Options[create_date_field]',
                                        'value' => $create_date_field,
                                        'data' => $field,
                                        'options' => ['placeholder' => Yii::t('ezform', 'Created Date field (if not the system created date)')],
                                        //'maintainOrder'=>true,
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => Url::to(['/ezforms2/ezform/get-fields', 'ezf_id'=>$model->ezf_id, 'v'=>$model->ezf_version]),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                                'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                                                    if(jqXHR.status&&jqXHR.status==403){
                                                        window.location.href = "'. Url::to(['/user/login']).'"
                                                    }
                                                }'),
                                            ],
                                            //'tags' => true,
                                            //'tokenSeparators' => [',', ' '],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                                            'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                                        ],
                                    ]);
                                    ?>
                                        </div>
                                          <div class="col-md-12 form-group">
                                          <label class="control-label" ><?= Yii::t('ezform', 'Working Unit field (if any)')?></label>
                                            <?php
                                    $unit_field = isset($model->ezf_options['unit_field'])?$model->ezf_options['unit_field']:'';
                                    
                                    echo kartik\select2\Select2::widget([
                                        'name' => 'Options[unit_field]',
                                        'value' => $unit_field,
                                        'data' => $field,
                                        'options' => ['placeholder' => Yii::t('ezform', 'Working Unit field (if any)')],
                                        //'maintainOrder'=>true,
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => Url::to(['/ezforms2/ezform/get-fields', 'ezf_id'=>$model->ezf_id, 'v'=>$model->ezf_version]),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                                'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                                                    if(jqXHR.status&&jqXHR.status==403){
                                                        window.location.href = "'. Url::to(['/user/login']).'"
                                                    }
                                                }'),
                                            ],
                                            //'tags' => true,
                                            //'tokenSeparators' => [',', ' '],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                                            'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                                        ],
                                    ]);
                                    ?>
                                        </div>
                                          <div class="col-md-12 form-group">
                                          <label class="control-label" ><?= Yii::t('ezform', 'Enable field after being disabled (if any)')?></label>
                                            <?php
                                    $enable_field = isset($model->ezf_options['enable_field'])?$model->ezf_options['enable_field']:'';
                                    
                                    echo kartik\select2\Select2::widget([
                                        'name' => 'Options[enable_field]',
                                        'value' => $enable_field,
                                        'data' => $field,
                                        'options' => ['placeholder' => Yii::t('ezform', 'Enable field after being disabled (if anys)')],
                                        //'maintainOrder'=>true,
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => Url::to(['/ezforms2/ezform/get-fields', 'ezf_id'=>$model->ezf_id, 'v'=>$model->ezf_version]),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                                'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                                                    if(jqXHR.status&&jqXHR.status==403){
                                                        window.location.href = "'. Url::to(['/user/login']).'"
                                                    }
                                                }'),
                                            ],
                                            //'tags' => true,
                                            //'tokenSeparators' => [',', ' '],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                                            'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                                        ],
                                    ]);
                                    ?>
                                        </div>
                                      </div>
                                  </div>
                                  </div>
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
                                    $data_consult = [];
                                    if(!empty($model->consult_users)){
                                        $user_init = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn(implode(',', $model->consult_users));
                                        
                                        if($user_init){
                                            $data_consult = ArrayHelper::map($user_init, 'user_id', 'fullname');
                                        }
                                    }
                                    ?>
                                        <?=
                                        $form->field($model, 'consult_users')->widget(Select2::className(), [
                                            'options' => ['placeholder' => Yii::t('ezform', 'Consult admin'), 'multiple' => true],
                                            'data' => $data_consult,
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            'ajax' => [
                                                'url' => Url::to(['/ezforms2/ezform/get-user']),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                                'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                                                    if(jqXHR.status&&jqXHR.status==403){
                                                        window.location.href = "'. Url::to(['/user/login']).'"
                                                    }
                                                }'),
                                            ],
                                            //'tags' => true,
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
                                <div class="col-md-12">
                            <?=
                            $form->field($model, 'ezf_js')->widget('appxq\sdii\widgets\AceEditor', [
                                'mode' => 'javascript', // programing language mode. Default "html"
                                'id' => 'ezf_js'
                            ]);
                            ?>
                                </div>
                            </div>
                          <div class="row">
                            <div class="col-md-12">
                              <?php
                                $model->ezf_sql = SDUtility::string2Array($model->ezf_sql); // initial value
                                $dataSql = [];
                                if(!empty($model->ezf_sql)){
                                    $sql_init = \backend\modules\ezforms2\classes\EzfQuery::getEzSqlIn(implode(',', $model->ezf_sql));
                                    if($sql_init){
                                        $dataSql = ArrayHelper::map($sql_init, 'id', 'sql_name');
                                    }
                                }
                              ?>
                                <?=
                                    $form->field($model, 'ezf_sql')->widget('appxq\sdii\widgets\SDSqlBuilder', [
                                        'id' => 'ezf_sql',
                                        'data' => $dataSql,
                                        'options' => ['multiple'=>1],
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
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                                'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                                                    if(jqXHR.status&&jqXHR.status==403){
                                                        window.location.href = "'. Url::to(['/user/login']).'"
                                                    }
                                                }'),
                                            ],
                                            //'initSelection' => new JsExpression('function (element, callback) { console.log(element); }'),
                                            //'tags' => true,
                                            'tokenSeparators' => [',', ' '],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                                            'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                                        ],
                                    ])
                                    ?>
                                <?php
                                    $model->ezf_role = SDUtility::string2Array($model->ezf_role); // initial value
                                    $dataRole = [];
                                    if(!empty($model->ezf_role)){
                                        $roles = \backend\modules\ezforms2\classes\EzfQuery::getRoleAllByEzform($model->ezf_id);
                                        if($roles){
                                            $dataRole = ArrayHelper::map($roles, 'role_name', 'role_desc');
                                        }
                                    }
                                    echo $form->field($model, 'ezf_role')->widget(Select2::className(), [
                                        'options' => ['placeholder' => Yii::t('ezform', 'Role'), 'multiple' => true],
                                        'data' => $dataRole,
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => Url::to(['/ezforms2/ezform/get-role']),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                                'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                                                    if(jqXHR.status&&jqXHR.status==403){
                                                        window.location.href = "'. Url::to(['/user/login']).'"
                                                    }
                                                }'),
                                            ],
                                            //'initSelection' => new JsExpression('function (element, callback) { console.log(element); }'),
                                            //'tags' => true,
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
                      <div role="tabpanel" class="tab-pane" id="event_form">
                        
                        <label><?= Yii::t('ezform', 'Result parameters after saving draft')?> : </label>
                        <?php
                          //EzformFields[ezf_field_options]
                          $options = $model->ezf_options;
                        ?>
                        <span class="btn btn-xs btn-info">result.data</span> 
                        <span class="btn btn-xs btn-info">result.ezf_id</span> 
                        <span class="btn btn-xs btn-info">result.dataid</span> 
                        <span class="btn btn-xs btn-info">result.target</span> 
                        <span class="btn btn-xs btn-info">result.modal</span> 
                        <span class="btn btn-xs btn-info">result.reloadDiv</span> 
                        <span class="btn btn-xs btn-info">result.status</span> 
                        <span class="btn btn-xs btn-info">result.message</span> 
                          <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('Ezform[ezf_options][after_save][enable]', isset($options['after_save']['enable'])?$options['after_save']['enable']:0, ['label'=>'After Event Enable'])?>
<!--                        <div class="form-group" style="<?php // echo Yii::$app->user->can('administrator')?'display: block;':'display: none;'?>">
                            <label><?php // echo Yii::t('ezform', 'PHP Function ($model, $modelFields, $modelEzf)')?></label>
                          <?php // echo Html::textInput('Ezform[ezf_options][after_save][php]', isset($options['after_save']['php'])?$options['after_save']['php']:'', ['class'=>'form-control'])?>

                        </div>-->

                        <div class="form-group">
                            <label><?= Yii::t('ezform', 'After Save JavaScript')?></label>
                              <?= appxq\sdii\widgets\AceEditor::widget([
                                  'name'=>'Ezform[ezf_options][after_save][js]',
                                  'value'=>isset($options['after_save']['js'])?$options['after_save']['js']:'',
                                  'mode' => 'javascript',
                                  'id'=>'after_save_id',
                                  'options'=>['id'=>'after_save_js']
                              ]);?>
                        </div>

                        <div class="form-group">
                            <label><?= Yii::t('ezform', 'After Delete JavaScript')?></label>
                              <?= appxq\sdii\widgets\AceEditor::widget([
                                  'name'=>'Ezform[ezf_options][after_delete][js]',
                                  'value'=>isset($options['after_delete']['js'])?$options['after_delete']['js']:'',
                                  'mode' => 'javascript',
                                  'id'=>'after_delete_id',
                                  'options'=>['id'=>'after_delete_js']
                              ]);?>
                        </div>
                        
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
    <?php if ($model->isNewRecord) { ?>
    <div class="modal-footer">
<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['id'=>'submit-form', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>
    <?php } ?>
<?php ActiveForm::end(); ?>

</div>

<?php
$js = "";
$js_reload = "";
$js_menu = '';
$scriptAuto = '';
$scriptAutoLoading = '';
$scriptAutowaitMe = '';
if($auto){
    $scriptAuto = "
        $('#submit-form').trigger('click');
    ";
    $scriptAutoLoading = "
        window.location.href = '".Url::to(['/ezbuilder/ezform-builder/update', 'id'=>$model->ezf_id])."';
        //$('body').waitMe('hide');
    ";
    $scriptAutowaitMe = "$('body').waitMe('hide');";
}

if (!$model->isNewRecord) {
    $js_menu = "$.pjax.reload({container:'#ezform-grid-pjax',timeout: false});";
    
    $js = "$('form#{$model->formName()}').on('change', function(e) {
    var \$form = $(this);
    formSave(\$form.attr('action'));});";

    $js .= "ezf_js.on('blur', function() {
    var url = '" . \yii\helpers\Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id]) . "';
     formSave(url);});";

//    $js .= "ezf_sql.on('blur', function() {
//    var url = '" . \yii\helpers\Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id]) . "';
//     formSave(url);});";
    
    $js .= "after_save_id.on('blur', function() {
    var url = '" . \yii\helpers\Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id]) . "';
     formSave(url);});";
    
    $js .= "after_delete_id.on('blur', function() {
    var url = '" . \yii\helpers\Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id]) . "';
     formSave(url);});";
    
    $js_reload = "
        location.reload();
     ";
}
?>
<?php $this->registerJs("
" . $js . "    
    
var reload = 0;

$('.btn-tmp').click(function(){
    let tmp = $(this).attr('data-tmp');
    $('#ezf_display_tmp').val(tmp);
    $('#ezf_display_tmp').trigger('change');
});

$('#ezform-enable_version').click(function(){
    yii.confirm('".Yii::t('ezform', 'Can not be modified when enabled.')."', function() {
        $('#ezform-enable_version').prop('checked', true);
        $('#ezform-enable_version').trigger('change');
        reload = 1;
    });
    return false;
});

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
            var cdiv = \$form.parent().parent().parent().parent().parent().attr('id');

	    " . SDNoty::show('result.message', 'result.status') . "
	    if(result.action == 'create') {
                $scriptAutoLoading
                $(document).find('#'+cdiv).modal('hide');
		$.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
	    } else if(result.action == 'update') {
		$(document).find('#'+cdiv).modal('hide');
		$.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
	    }
	} else {
            $scriptAutowaitMe
	    " . SDNoty::show('result.message', 'result.status') . "
	} 
    }).fail(function() {
        $scriptAutowaitMe
	" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
    return false;
});

function formSave(url){
 $.post(url,$('#Ezform').serialize()).done(function(result) {
         if(reload){
            $js_reload
         }
         $('#ezf_name_label').html(result.data.ezf_name);
    }).fail(function() {
	" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
}

$('.field-ezform-assign').addClass('" . ($model->shared <> '2' ? 'hidden' : '') . "');
$('.field-ezform-ezf_role').addClass('" . ($model->shared <> '2' ? 'hidden' : '') . "');
$('#consult_tools_setting').addClass('" . ($model->consult_tools <> '2' ? 'hidden' : '') . "');
$(\"input[name='Ezform[consult_tools]']\").on('change',function(){
    let consult_tools = $(\"input[name='Ezform[consult_tools]']:checked\").val();
    if(consult_tools == '2'){
        $('#consult_tools_setting').removeClass('hidden');
    }else {
        $('#consult_tools_setting').addClass('hidden');
    }
});

$(\"input[name='Ezform[unique_record]']\").on('change',function(){

    let unique_record = $(\"input[name='Ezform[unique_record]']:checked\").val();
    if(unique_record == '4'){
        $('#perday-box').show();
    }else {
        $('#perday-box').hide();
    }
});

$(\"input[name='Ezform[shared]']\").on('change',function(){
    var shared = $(\"input[name='Ezform[shared]']:checked\").val();
    if(shared === '2'){
        $('.field-ezform-assign, .field-ezform-ezf_role').removeClass('hidden');
    }else {
        $('.field-ezform-assign, .field-ezform-ezf_role').addClass('hidden');
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

$scriptAuto
"); ?>