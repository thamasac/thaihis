<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleTab */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-tab-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">Tab Menu Content</h4>
    </div>

    <div class="modal-body">
      <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 10px;">
        <li role="presentation" class="active"><a href="#form" aria-controls="form" role="tab" data-toggle="tab">Tab Widget</a></li>
        <li role="presentation"><a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">Advanced Settings</a></li>
    </ul>

  <!-- Tab panes -->
  <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="form">
        <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

      <?php
        $items_widget = backend\modules\ezmodules\classes\ModuleFunc::itemAlias('tab');
        $items_widget_db = \backend\modules\core\classes\CoreFunc::itemAlias('tab');
        $items_widget = yii\helpers\ArrayHelper::merge($items_widget, $items_widget_db);
            
        $items_widget['dropdown'] = 'Dropdown';
        
        $item_parent = \backend\modules\ezmodules\classes\ModuleQuery::getTabParent($module);
        
        ?>
        <div class="form-group">
      <?php
      $selection = 0;
      if($model->widget=='dropdown'){
          $selection = 1;
          $model->parent = NULL;
      }
      
      echo \backend\modules\ezforms2\classes\EzformWidget::radioList('ftype_widget', $selection, ['data'=>[Yii::t('ezmodule', 'Contain Widget'), Yii::t('ezmodule', 'Contain Dropdown list of other Menu')]], ['inline'=>true]);
              
        ?>
          </div>
        
        <div class="widget-box" style="display: <?=$selection?'none':'block'?>;">
            <?= $form->field($model, 'parent')->dropDownList(\yii\helpers\ArrayHelper::map($item_parent, 'tab_id', 'label'), ['prompt'=> Yii::t('ezmodule', 'Parent')]) ?>
        </div>
       
        <?= $form->field($model, 'order')->textInput(['type' => 'number']) ?>
      
        <?php 
        if(Yii::$app->user->can('administrator')){
            echo $form->field($model, 'tab_default')->checkbox();
        } else {
            echo Html::activeHiddenInput($model, 'tab_default');
        }
?>
        
        <div class="widget-box" style="display: <?=$selection?'none':'block'?>;">
        <?php //echo $form->field($model, 'widget')->dropDownList($items_widget) ?>
      <?php
      echo $form->field($model, 'widget')->widget(kartik\widgets\Select2::className(), [
                                        'options' => ['placeholder' => Yii::t('ezform', 'Select ...')],
                                        'data' => $items_widget,
                                        'pluginOptions' => [
                                            'allowClear' => false,
                                        ],
                                    ]);
      
      ?>
      <div id="tab-config">
          
      </div>
      </div>
        <?= Html::activeHiddenInput($model, 'tab_id') ?>
        <?= Html::activeHiddenInput($model, 'ezm_id') ?>
        <?= Html::activeHiddenInput($model, 'user_id') ?>
    </div>
      <div role="tabpanel" class="tab-pane" id="discuss">
        <?php
        $settings = [
            'minHeight' => 400,
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
      <div class="form-group pull-right">
            <a id="modal-addbtn-ezmodule-widget" style="cursor: pointer;" class="btn btn-danger btn-xs" data-url="<?= Url::to(['/ezmodules/ezmodule-widget/list-module', 'ezm_id'=>$model->ezm_id])?>"><i class="fa fa-puzzle-piece"></i> <?= Yii::t('ezmodule', 'Widget')?></a>
        
        </div>
      <?php
        echo $form->field($model, 'template')->widget(appxq\sdii\widgets\FroalaEditorWidget::className(), [
            'options'=>['class'=>'eztemplate'],
        ])->hint('Default widget is <a class="btn btn-warning btn-xs btn-widget" data-widget="{tab-widget}">{tab-widget}</a>')->label($model->getAttributeLabel('template').'
                    <span class="btn btn-xs btn-info btn-widget-tab">Tab</span>
                    <span class="btn btn-xs btn-info btn-widget-cardtab">Card Tab</span> 
                    <span class="btn btn-xs btn-info btn-widget-panel">Panel</span>
                    <span class="btn btn-xs btn-info btn-widget-accordion">Accordion</span>
                    <span class="btn btn-xs btn-info btn-widget-slideshow">Slide Show</span>
                    <span class="btn btn-xs btn-info btn-widget-gridrow">Grid Row</span>
                    <span class="btn btn-xs btn-info btn-widget-navbar">Toolbar</span>
                    ');

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

<?php  
$type_value = (isset($model->widget) && !empty($model->widget))?$model->widget:'form';
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
    $('#ezmoduletab-template').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-cardtab').on('click',function(){
    let temp = '$cardtab_str';
    $('#ezmoduletab-template').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-accordion').on('click',function(){
    let temp = '$accordion_str';
    $('#ezmoduletab-template').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-slideshow').on('click',function(){
    let temp = '$slideshow_str';
    $('#ezmoduletab-template').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-panel').on('click',function(){
    let temp = '$panel_str';
    $('#ezmoduletab-template').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-gridrow').on('click',function(){
    let temp = '$gridrow_str';
    $('#ezmoduletab-template').froalaEditor('html.insert', temp, false);
});

$('.btn-widget-navbar').on('click',function(){
    let temp = '$navbar_str';
    $('#ezmoduletab-template').froalaEditor('html.insert', temp, false);
});    

$('input[name=\"ftype_widget\"]').change(function(){
    if($(this).val()==1){
        $('#ezmoduletab-parent').val('');
        $('#ezmoduletab-widget').val('dropdown');
        $('.widget-box').hide();
    }else {
        $('#ezmoduletab-widget').val('form');
        $('.widget-box').show();
    }
    $('#ezmoduletab-widget').trigger('change');
});   

$('#ezmoduletab-widget').change(function(){
    if($(this).val()=='dropdown'){
        $('input[name=\"ftype_widget\"][value=\"1\"]').prop('checked', true);
        $('.widget-box').hide();
        $('#ezmoduletab-parent').val('');
    }
    
});    

genFormItem('$type_value', '{$model->tab_id}');

$('#ezmoduletab-widget').on('change', function() {
    genFormItem($(this).val(), '{$model->tab_id}');
});

function genFormItem(widget_type, id) {
    $('#tab-config').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $.ajax({
	    method: 'POST',
	    url:'".Url::to(['/ezmodules/ezmodule-tab/get-form'])."',
            data: {widget:widget_type, id:id},    
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    $('#tab-config').html(result.html);
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
    });
}

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    ". SDNoty::show('result.message', 'result.status') ."
            //getTabContent($('#ezmodule-tab-widget-menu').attr('data-url'));
            location.reload();
            $(document).find('#modal-ezmodule').modal('hide');
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
	} 
    }).fail(function() {
	". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
	console.log('server error');
    });
    return false;
});

function getTabContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#ezmodule-tab-widget-menu').html(result);
            }
        });
    }


$('.btn-widget').on('click', function() {
    $('#ezmoduletab-template').froalaEditor('html.insert', $(this).attr('data-widget'), false);
});

$('#modal-addbtn-ezmodule-widget').on('click', function() {
    modalEzmoduleWidget($(this).attr('data-url'));
    return false;
});

function modalEzmoduleWidget(url) {
    $('#modal-add-widget .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-add-widget').modal('show')
    .find('.modal-content')
    .load(url);
}


");?>