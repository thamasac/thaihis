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
    ]);
    ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Ezmodule Settings')?></h4>
    </div>

    <div class="modal-body">

      <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
        <li role="presentation" class="<?=$tab==1?'active':''?>"><a href="#general" aria-controls="general" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'General Settings')?></a></li>
        <li role="presentation" class="<?=$tab==2?'active':''?>"><a href="#sharing" aria-controls="sharing" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'Sharing Settings')?></a></li>
        <li role="presentation" class="<?=$tab==3?'active':''?>"><a href="#module" aria-controls="module" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'Module Settings')?></a></li>
        <li role="presentation" class="<?=$tab==4?'active':''?>"><a href="#template" aria-controls="template" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'User Interface (For advanced user)')?></a></li>
      </ul>
      
      <!-- Tab panes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?=$tab==1?'active':''?>" id="general">
          <div class="row">
          <div class="col-md-6">
          <?= $form->field($model, 'ezm_name')->textInput(['maxlength' => true])->label('<i class="fa fa-asterisk" style="color:#ff0000"></i> '.$model->getAttributeLabel('ezm_name')) ?>
      </div>
        <div class="col-md-6 sdbox-col">
          <?= $form->field($model, 'ezm_short_title')->textInput(['maxlength' => true])->label('<i class="fa fa-asterisk" style="color:#ff0000"></i> '.$model->getAttributeLabel('ezm_short_title')) ?>
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
                <?php
                echo $form->field($model, 'picture')->widget(\trntv\filekit\widget\Upload::classname(), [
                    'id' => 'widget_ezm_icon',
                    'url' => ['/core/file-storage/module-upload']
                ])->hint('<a href="http://www.iconarchive.com/show/flatwoken-icons-by-alecive.html#iconlist" target="_blank">'.Yii::t('ezmodule', 'Download icon').'</a>')
                ?>
            </div>
            <div class="col-md-9 sdbox-col">
                <?php
                if ($model->isNewRecord) {
                    echo $form->field($model, 'ezm_type')->dropDownList(ModuleFunc::itemAlias('type'));
                } else {
                    echo Html::label($model->getAttributeLabel('ezm_type'));
                    echo Html::textInput('ttyp', ($model->ezm_type == 1 ? ModuleFunc::itemAlias('type', 1) : ModuleFunc::itemAlias('type', 0)), ['class' => 'form-control', 'disabled' => true]);
                    echo $form->field($model, 'ezm_type')->hiddenInput()->label(FALSE);
                }
                ?>
                <div class="div-glink">
                    <?= $form->field($model, 'ezm_link')->textInput(['maxlength' => true]) ?>
                </div>
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
        <div role="tabpanel" class="tab-pane <?=$tab==2?'active':''?>" id="sharing">
          

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
        <?php
            $model->ezm_role = \appxq\sdii\utils\SDUtility::string2Array($model->ezm_role); // initial value
            $dataRole = [];
            if(!empty($model->ezm_role)){
                $roles = \backend\modules\ezforms2\classes\EzfQuery::getRoleAllByEzmodule($model->ezm_id);
                if($roles){
                    $dataRole = ArrayHelper::map($roles, 'role_name', 'role_desc');
                }
            }
            echo $form->field($model, 'ezm_role')->widget(\kartik\select2\Select2::className(), [
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
          <?=$form->field($model, 'ezm_template')->radioList(['data'=>[Yii::t('ezmodule', 'No'), Yii::t('ezmodule', 'Yes')]])?>
          
        </div>
        <div role="tabpanel" class="tab-pane <?=$tab==3?'active':''?>" id="module">
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
        <div role="tabpanel" class="tab-pane <?=$tab==4?'active':''?>" id="template">
            <div class="div-custom">
              <?= $form->field($model, 'template_id')->dropDownList(ArrayHelper::map($template, 'template_id', 'template_name'), ['prompt'=>Yii::t('module', 'Select template ...')]) ?>
              
              <div class="form-group pull-right">
            <a id="modal-list-widget" style="cursor: pointer;" class="btn btn-danger btn-xs" data-url="<?= Url::to(['/ezmodules/ezmodule-widget/list-module', 'ezm_id'=>$model->ezm_id])?>"><i class="fa fa-puzzle-piece"></i> <?= Yii::t('ezmodule', 'Widget')?></a>
        
        </div>
      <?php
      
        echo $form->field($model, 'ezm_html')->widget(appxq\sdii\widgets\FroalaEditorWidget::className(), [
            'options'=>['class'=>'eztemplate'],
        ])->hint('Default Template <a class="btn btn-warning btn-xs btn-template" data-widget="{tab-widget}">Use Default</a>')
                ->label($model->getAttributeLabel('ezm_html').'
                    <span class="btn btn-xs btn-info btn-widget-tab">Tab</span>
                    <span class="btn btn-xs btn-info btn-widget-cardtab">Card Tab</span> 
                    <span class="btn btn-xs btn-info btn-widget-panel">Panel</span>
                    <span class="btn btn-xs btn-info btn-widget-accordion">Accordion</span>
                    <span class="btn btn-xs btn-info btn-widget-slideshow">Slide Show</span>
                    <span class="btn btn-xs btn-info btn-widget-gridrow">Grid Row</span>
                    <span class="btn btn-xs btn-info btn-widget-navbar">Toolbar</span>
                    ');

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
      
        
        <?= $form->field($model, 'ezm_id')->hiddenInput()->label(false) ?>
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
    <div class="modal-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php \backend\modules\ezforms2\classes\EzActiveForm::end(); ?>

</div>

<?php 
$tab_str = '<ul class="nav nav-tabs" id="myTabs" role="tablist"> <li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Home</a></li> <li role="presentation"><a href="#profile" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">Profile</a></li> <li role="presentation" class="dropdown"> <a href="#" class="dropdown-toggle" id="myTabDrop1" data-toggle="dropdown" aria-controls="myTabDrop1-contents">Dropdown <span class="caret"></span></a> <ul class="dropdown-menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents"> <li><a href="#dropdown1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">@fat</a></li> <li><a href="#dropdown2" role="tab" id="dropdown2-tab" data-toggle="tab" aria-controls="dropdown2">@mdo</a></li> </ul> </li> </ul> <div class="tab-content" id="myTabContent"> <div class="tab-pane fade in active" role="tabpanel" id="home" aria-labelledby="home-tab"> <p>Raw denim you probably haven heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica. Reprehenderit butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terry richardson ex squid. Aliquip placeat salvia cillum iphone. Seitan aliquip quis cardigan american apparel, butcher voluptate nisi qui.</p> </div> <div class="tab-pane fade" role="tabpanel" id="profile" aria-labelledby="profile-tab"> <p>Food truck fixie locavore, accusamus mcsweeney marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit. Keytar helvetica VHS salvia yr, vero magna velit sapiente labore stumptown. Vegan fanny pack odio cillum wes anderson 8-bit, sustainable jean shorts beard ut DIY ethical culpa terry richardson biodiesel. Art party scenester stumptown, tumblr butcher vero sint qui sapiente accusamus tattooed echo park.</p> </div> <div class="tab-pane fade" role="tabpanel" id="dropdown1" aria-labelledby="dropdown1-tab"> <p>Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg banh mi whatever gluten-free, carles pitchfork biodiesel fixie etsy retro mlkshk vice blog. Scenester cred you probably haven heard of them, vinyl craft beer blog stumptown. Pitchfork sustainable tofu synth chambray yr.</p> </div> <div class="tab-pane fade" role="tabpanel" id="dropdown2" aria-labelledby="dropdown2-tab"> <p>Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater. Fanny pack portland seitan DIY, art party locavore wolf cliche high life echo park Austin. Cred vinyl keffiyeh DIY salvia PBR, banh mi before they sold out farm-to-table VHS viral locavore cosby sweater. Lomo wolf viral, mustache readymade thundercats keffiyeh craft beer marfa ethical. Wolf salvia freegan, sartorial keffiyeh echo park vegan.</p> </div> </div>';
$accordion_str = '<div class="panel-group" role="tablist" id="accordion" aria-multiselectable="true"> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="headingOne"> <h4 class="panel-title"> <a href="#collapseOne" role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="true" aria-controls="collapseOne" class=""> Collapsible Group Item #1 </a> </h4> </div> <div class="panel-collapse collapse in" role="tabpanel" id="collapseOne" aria-labelledby="headingOne" aria-expanded="true" style=""> <div class="panel-body"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven heard of them accusamus labore sustainable VHS. </div> </div> </div> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="headingTwo"> <h4 class="panel-title"> <a href="#collapseTwo" class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="false" aria-controls="collapseTwo"> Collapsible Group Item #2 </a> </h4> </div> <div class="panel-collapse collapse" role="tabpanel" id="collapseTwo" aria-labelledby="headingTwo" aria-expanded="false" style="height: 0px;"> <div class="panel-body"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven heard of them accusamus labore sustainable VHS. </div> </div> </div> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="headingThree"> <h4 class="panel-title"> <a href="#collapseThree" class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="false" aria-controls="collapseThree"> Collapsible Group Item #3 </a> </h4> </div> <div class="panel-collapse collapse" role="tabpanel" id="collapseThree" aria-labelledby="headingThree" aria-expanded="false" style="height: 0px;"> <div class="panel-body"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven heard of them accusamus labore sustainable VHS. </div> </div> </div> </div>';
$slideshow_str = '<div class="carousel slide" id="carousel-example-generic" data-ride="carousel"> <ol class="carousel-indicators"> <li data-target="#carousel-example-generic" data-slide-to="0" class=""></li> <li data-target="#carousel-example-generic" data-slide-to="1" class=""></li> <li data-target="#carousel-example-generic" data-slide-to="2" class="active"></li> </ol> <div class="carousel-inner" role="listbox"> <div class="item"> <img alt="First slide [900x500]" data-src="holder.js/900x500/auto/#777:#555/text:First slide" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDkwMCA1MDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzkwMHg1MDAvYXV0by8jNzc3OiM1NTUvdGV4dDpGaXJzdCBzbGlkZQpDcmVhdGVkIHdpdGggSG9sZGVyLmpzIDIuNi4wLgpMZWFybiBtb3JlIGF0IGh0dHA6Ly9ob2xkZXJqcy5jb20KKGMpIDIwMTItMjAxNSBJdmFuIE1hbG9waW5za3kgLSBodHRwOi8vaW1za3kuY28KLS0+PGRlZnM+PHN0eWxlIHR5cGU9InRleHQvY3NzIj48IVtDREFUQVsjaG9sZGVyXzE2N2JiNGY3NGU1IHRleHQgeyBmaWxsOiM1NTU7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6NDVwdCB9IF1dPjwvc3R5bGU+PC9kZWZzPjxnIGlkPSJob2xkZXJfMTY3YmI0Zjc0ZTUiPjxyZWN0IHdpZHRoPSI5MDAiIGhlaWdodD0iNTAwIiBmaWxsPSIjNzc3Ii8+PGc+PHRleHQgeD0iMzA4LjI5Njg3NSIgeT0iMjcwLjEiPkZpcnN0IHNsaWRlPC90ZXh0PjwvZz48L2c+PC9zdmc+" data-holder-rendered="true"> </div> <div class="item"> <img alt="Second slide [900x500]" data-src="holder.js/900x500/auto/#666:#444/text:Second slide" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDkwMCA1MDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzkwMHg1MDAvYXV0by8jNjY2OiM0NDQvdGV4dDpTZWNvbmQgc2xpZGUKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNjdiYjRmNTA0NiB0ZXh0IHsgZmlsbDojNDQ0O2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjQ1cHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE2N2JiNGY1MDQ2Ij48cmVjdCB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgZmlsbD0iIzY2NiIvPjxnPjx0ZXh0IHg9IjI2NC45NTMxMjUiIHk9IjI3MC4xIj5TZWNvbmQgc2xpZGU8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true"> </div> <div class="item active"> <img alt="Third slide [900x500]" data-src="holder.js/900x500/auto/#555:#333/text:Third slide" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDkwMCA1MDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzkwMHg1MDAvYXV0by8jNTU1OiMzMzMvdGV4dDpUaGlyZCBzbGlkZQpDcmVhdGVkIHdpdGggSG9sZGVyLmpzIDIuNi4wLgpMZWFybiBtb3JlIGF0IGh0dHA6Ly9ob2xkZXJqcy5jb20KKGMpIDIwMTItMjAxNSBJdmFuIE1hbG9waW5za3kgLSBodHRwOi8vaW1za3kuY28KLS0+PGRlZnM+PHN0eWxlIHR5cGU9InRleHQvY3NzIj48IVtDREFUQVsjaG9sZGVyXzE2N2JiNGY3ZWU0IHRleHQgeyBmaWxsOiMzMzM7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6NDVwdCB9IF1dPjwvc3R5bGU+PC9kZWZzPjxnIGlkPSJob2xkZXJfMTY3YmI0ZjdlZTQiPjxyZWN0IHdpZHRoPSI5MDAiIGhlaWdodD0iNTAwIiBmaWxsPSIjNTU1Ii8+PGc+PHRleHQgeD0iMjk4LjMyMDMxMjUiIHk9IjI3MC4xIj5UaGlyZCBzbGlkZTwvdGV4dD48L2c+PC9nPjwvc3ZnPg==" data-holder-rendered="true"> </div> </div> <a href="#carousel-example-generic" class="left carousel-control" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a> <a href="#carousel-example-generic" class="right carousel-control" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> <span class="sr-only">Next</span> </a> </div>';
$panel_str = '<div class="panel panel-primary"> <div class="panel-heading"> <h3 class="panel-title">Panel title</h3> </div> <div class="panel-body"> Panel content </div> </div>';
$gridrow_str = '<div class="row"><div class="col-md-4">col_4</div><div class="col-md-4">col_4</div><div class="col-md-4">col_4</div></div>';
$navbar_str = '<nav class="navbar navbar-default" style=" z-index: 1 !important; "> <div class="container-fluid"> <div class="navbar-header"> <a href="#" class="navbar-brand"> <img alt="Brand" height="20" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAB+0lEQVR4AcyYg5LkUBhG+1X2PdZGaW3btm3btm3bHttWrPomd1r/2Jn/VJ02TpxcH4CQ/dsuazWgzbIdrm9dZVd4pBz4zx2igTaFHrhvjneVXNHCSqIlFEjiwMyyyOBilRgGSqLNF1jnwNQdIvAt48C3IlBmHCiLQHC2zoHDu6zG1iXn6+y62ScxY9AODO6w0pvAqf23oSE4joOfH6OxfMoRnoGUm+de8wykbFt6wZtA07QwtNOqKh3ZbS3Wzz2F+1c/QJY0UCJ/J3kXWJfv7VhxCRRV1jGw7XI+gcO7rEFFRvdYxydwcPsVsC0bQdKScngt4iUTD4Fy/8p7PoHzRu1DclwmgmiqgUXjD3oTKHbAt869qdJ7l98jNTEblPTkXMwetpvnftA0LLHb4X8kiY9Kx6Q+W7wJtG0HR7fdrtYz+x7iya0vkEtUULIzCjC21wY+W/GYXusRH5kGytWTLxgEEhePPwhKYb7EK3BQuxWwTBuUkd3X8goUn6fMHLyTT+DCsQdAEXNzSMeVPAJHdF2DmH8poCREp3uwm7HsGq9J9q69iuunX6EgrwQVObjpBt8z6rdPfvE8kiiyhsvHnomrQx6BxYUyYiNS8f75H1w4/ISepDZLoDhNJ9cdNUquhRsv+6EP9oNH7Iff2A9g8h8CLt1gH0Qf9NMQAFnO60BJFQe0AAAAAElFTkSuQmCC" width="20"> </a> <button type="button" class="collapsed navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> <a href="#" class="navbar-brand">Brand</a> </div> <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1"> <ul class="nav navbar-nav"> <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li> <li><a href="#">Link</a></li> <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a> <ul class="dropdown-menu"> <li><a href="#">Action</a></li> <li><a href="#">Another action</a></li> <li><a href="#">Something else here</a></li> <li role="separator" class="divider"></li> <li><a href="#">Separated link</a></li> <li role="separator" class="divider"></li> <li><a href="#">One more separated link</a></li> </ul> </li> </ul> <div class="navbar-form navbar-left"> <div class="form-group"> <input class="form-control" placeholder="Search"> </div> &nbsp; <button type="submit" class="btn btn-default">Submit</button> </div> <ul class="nav navbar-nav navbar-right"> <li><a href="#">Link</a></li> <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a> <ul class="dropdown-menu"> <li><a href="#">Action</a></li> <li><a href="#">Another action</a></li> <li><a href="#">Something else here</a></li> <li role="separator" class="divider"></li> <li><a href="#">Separated link</a></li> </ul> </li> </ul> </div> </div> </nav>';
$cardtab_str = '<div class="card card-primary"><div class="card-header"><ul class="nav nav-tabs card-header-tabs"><li class="active"><a aria-expanded="true" data-toggle="tab" href="#tab1" id="tab1-tab">{tab1}</a></li><li><a aria-expanded="true" data-toggle="tab" href="#tab2" id="tab2-tab">{tab2}</a></li></ul></div><div class="card-block tab-content"><div class="tab-pane fade in active" id="tab1">{content1}</div><div class="tab-pane fade" id="tab2">{content2}</div></div></div>';

$this->registerJs("
    
$('.btn-widget-tab').on('click',function(){
    let temp = '$tab_str';
    $('#ezmodule-ezm_html').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-cardtab').on('click',function(){
    let temp = '$cardtab_str';
    $('#ezmodule-ezm_html').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-accordion').on('click',function(){
    let temp = '$accordion_str';
    $('#ezmodule-ezm_html').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-slideshow').on('click',function(){
    let temp = '$slideshow_str';
    $('#ezmodule-ezm_html').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-panel').on('click',function(){
    let temp = '$panel_str';
    $('#ezmodule-ezm_html').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-gridrow').on('click',function(){
    let temp = '$gridrow_str';
    $('#ezmodule-ezm_html').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-navbar').on('click',function(){
    let temp = '$navbar_str';
    $('#ezmodule-ezm_html').froalaEditor('html.insert', temp, false);
});

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
	    " . SDNoty::show('result.message', 'result.status') . "
	    if(result.action == 'create') {
		//$(\$form).trigger('reset');
                $(document).find('#modal-ezmodule').modal('hide');
		$.pjax.reload({container:'#ezmodule-grid-pjax'});
	    } else if(result.action == 'update') {
		$(document).find('#modal-ezmodule').modal('hide');
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
		    $('#ezmodule-ezm_html').froalaEditor('html.set', result.html);
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
        $('#ezmodule-ezm_html').froalaEditor('html.insert', $(this).attr('data-widget'), false);
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