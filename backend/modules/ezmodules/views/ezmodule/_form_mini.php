<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use backend\modules\ezmodules\classes\ModuleFunc;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\Ezmodule */
/* @var $form yii\bootstrap\ActiveForm */
$user_id = Yii::$app->user->id;
$template = \backend\modules\ezmodules\classes\ModuleQuery::getTemplate($user_id);

$options = isset($model->options)?appxq\sdii\utils\SDUtility::string2Array($model->options):[];
?>

<div class="ezmodule-form">

    <?php
    $form = \backend\modules\ezforms2\classes\EzActiveForm::begin([
        'id' => $model->formName(),
        'options' => ['enctype' => 'multipart/form-data'],
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

    <div class="modal-body">
      
      <div class="row">
          <div class="col-md-3 text-center">
              
          </div>
            <div class="col-md-9">
              
              <?php
                echo $form->field($model, 'picture')->widget(\trntv\filekit\widget\Upload::classname(), [
                    'id' => 'widget_ezm_icon',
                    'url' => ['/core/file-storage/module-upload']
                ])->hint('<a href="http://www.iconarchive.com/show/flatwoken-icons-by-alecive.html#iconlist" target="_blank">'.Yii::t('ezmodule', 'Download icon').'</a>')
                        ->label(Yii::t('ezmodule', '1. Upload Icon'))
                ?>
              
              <?= $form->field($model, 'ezm_short_title')->textInput(['maxlength' => true])->label('<i class="fa fa-asterisk" style="color:#ff0000"></i> 2. Edit Short Name') ?>
              
              <?= $form->field($model, 'ezm_name')->textInput(['maxlength' => true])->label('<i class="fa fa-asterisk" style="color:#ff0000"></i> 3. Edit Long Name') ?>
              
              
              
              <div class="form-group">
                <label class="control-label col-sm-2"> </label>
                <div class="col-sm-6">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
              </div>
          </div>
      </div>
      
      
      <div style="display: none;">
          
      <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
        <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'General Settings')?></a></li>
        <li role="presentation"><a href="#sharing" aria-controls="sharing" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'Sharing Settings')?></a></li>
        <li role="presentation"><a href="#module" aria-controls="module" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'Module Settings')?></a></li>
        <li role="presentation"><a href="#template" aria-controls="template" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'User Interface (For advanced user)')?></a></li>
      </ul>
      
      <!-- Tab panes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="general">
          <div class="row">
          <div class="col-md-6">
          
      </div>
        <div class="col-md-6 sdbox-col">
          <?php
                if ($model->isNewRecord) {
                    echo $form->field($model, 'ezm_type')->dropDownList(ModuleFunc::itemAlias('type'))->label('4. Edit Module Type');
                } else {
                    ?>
                    <div class="form-group">
                    <label class="control-label col-sm-2"> <?= Yii::t('ezmodule', '4. Edit Module Type')?></label>
                    <div class="col-sm-6">
                        <?php echo Html::textInput('ttyp', ($model->ezm_type == 1 ? ModuleFunc::itemAlias('type', 1) : ModuleFunc::itemAlias('type', 0)), ['class' => 'form-control', 'disabled' => true]);?>

                    </div>
                  </div>
              <?php
                    
                    echo $form->field($model, 'ezm_type')->hiddenInput()->label(FALSE);
                }
                ?>
                <div class="div-glink">
                    <?= $form->field($model, 'ezm_link')->textInput(['maxlength' => true])->label('5. Edit Url') ?>
                </div>
        </div>
      </div>

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
//        $lang = Yii::$app->language;
//        if ($lang != 'en-US') {
//            $settings['lang'] = backend\modules\ezforms2\classes\EzfFunc::getLanguage();
//        }
        ?>

        <div class="row">

            <div class="col-md-3 ">
               
            </div>
            <div class="col-md-9 sdbox-col">
                
                <?php
                if (Yii::$app->user->can('administrator')) {
                    echo $form->field($model, 'ezm_system')->checkbox();
                    echo $form->field($model, 'ezm_visible')->checkbox();
                } else {
                    echo $form->field($model, 'ezm_system')->hiddenInput()->label(FALSE);
                    echo $form->field($model, 'ezm_visible')->hiddenInput()->label(FALSE);
                }
                echo $form->field($model, 'ezm_project')->checkbox();
                ?>
                
                
            </div>
        </div>
        
        <?php
        echo $form->field($model, 'ezm_detail')->widget(appxq\sdii\widgets\FroalaEditorWidget::className(), [
        ]);

        echo $form->field($model, 'ezm_devby')->widget(appxq\sdii\widgets\FroalaEditorWidget::className(), [
        ]);
        ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="sharing">
          

        <?php
        $userlist = backend\modules\ezforms2\classes\EzfQuery::getIntUserAll(); //explode(",", $model1->assign);
        ?>

      <div class="form-group">
        <?= Html::label(Yii::t('ezmodule', 'a) Who can be my co-creator (Editing enabled)'))?>
        <?= backend\modules\ezforms2\classes\EzformWidget::radioList('Ezmodule[options][builder_type]', isset($options['builder_type'])?$options['builder_type']:0, ['data'=>[Yii::t('ezmodule', 'Private (Only me)'), Yii::t('ezmodule', 'Restricted, persons specified below')]])?>
      </div>
      
      <div id="builder_box" style="display: <?=(isset($options['builder_type']) && $options['builder_type']==1)?'block':'none'?>;">
      <?=
        $form->field($model, 'ezm_builder')->widget(\kartik\select2\Select2::className(), [
            'options' => ['placeholder' => Yii::t('ezmodule', 'Select members'), 'multiple' => true],
            'data' => \yii\helpers\ArrayHelper::map($userlist, 'id', 'text'),
            'pluginOptions' => [
                'tokenSeparators' => [',', ' '],
            ],
        ])
        ?>
      </div>
      
      <div class="form-group">
        <?= Html::label(Yii::t('ezmodule', 'b) Who can see and use the Module'))?>
        <?= backend\modules\ezforms2\classes\EzformWidget::radioList('Ezmodule[public]', isset($model->public)?$model->public:0, ['data'=>[Yii::t('ezmodule', 'Private (Only me)'), Yii::t('ezmodule', 'Public (Any members of this website)'), Yii::t('ezmodule', 'Restricted, persons specified below')]])?>
      </div>
        <?php
        if ($model->public == 1) {
            if ($model->approved == 1) {
                echo '<code>'.Yii::t('ezmodule', 'Approved.').'</code><br><br>';
            } else {
                echo '<code>'.Yii::t('ezmodule', 'Waiting for approval.').'</code><br><br>';
            }
        }
        ?>
        <?=$form->field($model, 'ezm_template')->radioList(['data'=>[Yii::t('ezmodule', 'No'), Yii::t('ezmodule', 'Yes')]])?>
          
      <div id="share_box" style="display: <?=$model->public==2?'block':'none'?>;">
          <?=
        $form->field($model, 'share')->widget(\kartik\select2\Select2::className(), [
            'options' => ['placeholder' => Yii::t('ezmodule', 'Select members'), 'multiple' => true],
            'data' => \yii\helpers\ArrayHelper::map($userlist, 'id', 'text'),
            'pluginOptions' => [
                'tokenSeparators' => [',', ' '],
            ],
        ])
        ?>
      </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="module">
          <div class="div-custom">

            <?php
            $ezf_initValue = empty($model->ezf_id) ? '' : \backend\modules\ezforms2\models\Ezform::findOne($model->ezf_id)->ezf_name;

            echo $form->field($model, 'ezf_id')->widget(kartik\widgets\Select2::classname(), [
                'initValueText' => $ezf_initValue,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Parent Form (if any)')],
                'pluginOptions' => [
                    'minimumInputLength' => 0,
                    'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to(['/ezforms2/ezform/get-forms']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(result) { return result.text; }'),
                    'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                ],
            ])->label(Yii::t('ezmodule', 'Parent Form'));
            ?>
          <div class="form-group">
          <?= Html::hiddenInput('Ezmodule[options][menu]', 0)?>
          <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('Ezmodule[options][menu]', isset($options['menu'])?$options['menu']:1, ['label'=> Yii::t('ezmodule', 'Enable Main Menu')])?>
          </div>
          
          <div class="form-group">
            <?= Html::label(Yii::t('ezmodule', 'Tab Menu'))?>
            <?= backend\modules\ezforms2\classes\EzformWidget::radioList('Ezmodule[options][module_menu]', isset($options['module_menu'])?$options['module_menu']:1, ['data'=>[Yii::t('ezmodule', 'Disable Tab'), Yii::t('ezmodule', 'Tab to add EzModule only'), Yii::t('ezmodule', 'Tab to add EzModules and any other types of applications')]], ['inline'=>true])?>
            </div>
          
           
        </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="template">
            <div class="div-custom">
              <?= $form->field($model, 'template_id')->dropDownList(ArrayHelper::map($template, 'template_id', 'template_name')) ?>
              
              <div class="form-group pull-right">
            <a id="modal-list-widget" style="cursor: pointer;" class="btn btn-danger btn-xs" data-url="<?= Url::to(['/ezmodules/ezmodule-widget/list-module', 'ezm_id'=>$model->ezm_id])?>"><i class="fa fa-puzzle-piece"></i> <?= Yii::t('ezmodule', 'Widget')?></a>
        
        </div>
      <?php
        echo $form->field($model, 'ezm_html')->widget(appxq\sdii\widgets\FroalaEditorWidget::className(), [
            'options'=>['id'=>'ezmoduletab-template'],
        ])->hint('Default Template <a class="btn btn-warning btn-xs btn-template" data-widget="{tab-widget}">Use Default</a>');

        ?>
               <?=
            $form->field($model, 'ezm_js')->widget('appxq\sdii\widgets\AceEditor', [
                'mode' => 'javascript', // programing language mode. Default "html"
                'id' => 'ezm_js'
            ]);
            ?>
               <?=
            $form->field($model, 'ezm_css')->widget('appxq\sdii\widgets\AceEditor', [
                'mode' => 'css', // programing language mode. Default "html"
                'id' => 'ezm_css'
            ]);
            ?>
          </div>
        </div>
      </div>
      
        
        
        <?= $form->field($model, 'ezm_tag')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'sitecode')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'approved')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'active')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'order_module')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'created_by')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'updated_by')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'updated_at')->hiddenInput()->label(false) ?>

        </div>
    </div>
    

    <?php \backend\modules\ezforms2\classes\EzActiveForm::end(); ?>

</div>

<?php $this->registerJs("
    
$('input[name=\"Ezmodule[public]\"]').change(function(){
    if($(this).val()==2){
        $('#share_box').show();
    } else {
        $('#share_box').hide();
    }
    $('#ezmodule-share').val('').change();
});

$('input[name=\"Ezmodule[options][builder_type]\"]').change(function(){
    if($(this).val()==1){
        $('#builder_box').show();
    } else {
        $('#builder_box').hide();
    }
    $('#ezmodule-ezm_builder').val('').change();
});

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
            reloadNow = 1;
	    " . SDNoty::show('result.message', 'result.status') . "
	    if(result.action == 'create') {
		//$(\$form).trigger('reset');
               $(document).find('#modal-create').modal('hide');
		$.pjax.reload({container:'#ezmodule-grid-pjax'});
	    } else if(result.action == 'update') {
		$(document).find('#modal-create').modal('hide');
		$.pjax.reload({container:'#ezmodule-grid-pjax'});
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

setUi($('#ezmodule-ezm_type').val());

$('#ezmodule-ezm_type').on('change', function(){
    setUi($(this).val());
});

function setUi(val){
    if(val==1){
	$('.div-glink').show();
	$('.div-custom').hide();
    } else {
	$('.div-glink').hide();
	$('.div-custom').show();
    }
}

"); ?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    
    $('#ezmodule-template_id').change(function(){
        $.ajax({
	    method: 'POST',
	    url:'<?=Url::to(['/ezmodules/ezmodule/get-template'])?>',
            data: {id:$(this).val()},    
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    $('#ezmoduletab-template').froalaEditor('html.set', result.html);
                    ezm_js.setValue(result.js);
                    ezm_css.setValue(result.css);
		} else {
		    <?=SDNoty::show('result.message', 'result.status')?>
		}
	    }
        });
    });
    
    $('.btn-template').on('click', function() {
        $('#ezmodule-template_id').trigger('change');
    });
    
    $('.btn-widget').on('click', function() {
        $('#ezmoduletab-template').froalaEditor('html.insert', $(this).attr('data-widget'), false);
    });

    $('#ezmodule-ezm_name').change(function(){
        if($('#ezmodule-ezm_short_title').val()==''){
            $('#ezmodule-ezm_short_title').val($('#ezmodule-ezm_name').val());
        }
    });
    
    $('#modal-list-widget').on('click', function() {
        modalListWidget($(this).attr('data-url'));
        return false;
    });

    function modalListWidget(url) {
        $('#modal-add-widget .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-add-widget').modal('show')
        .find('.modal-content')
        .load(url);
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>