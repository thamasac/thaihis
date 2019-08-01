<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if($target){
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
    </div>
  <div class="col-md-6 sdbox-col" >
        <?= Html::label(Yii::t('ezform', 'Column'), 'options[column]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[column]', (isset($options['column'])?$options['column']:'2'), ['class'=>'form-control', 'type'=>'number'])?>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6 ">
      <?php
      $attrname_ezf_id = 'options[ezf_id]';
      $value_ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
      
      ?>
        <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php 
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value'=> $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id'=>'config_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
      <?php
      $attrname_fields = 'options[fields]';
      $value_fields = isset($options['fields']) && is_array($options['fields'])?\appxq\sdii\utils\SDUtility::array2String($options['fields']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <?php
        $attrname_image_field = 'options[image_field]';
        $value_image_field = isset($options['image_field'])?$options['image_field']:'';
        ?>
        <?= Html::label(Yii::t('ezform', 'Image Field'), $attrname_image_field, ['class' => 'control-label']) ?>
        <div id="pic_field_box">
            
        </div>
    </div>
  <div class="col-md-6 sdbox-col" >
        <?= Html::label(Yii::t('ezform', 'Action'), 'options[action]', ['class' => 'control-label']) ?>
        <?= kartik\select2\Select2::widget([
        'id'=>'config_action',
        'name' => 'options[action]',
        'value'=>isset($options['action'])?$options['action']:['create', 'update', 'delete', 'view'],
        'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('action'),
        'maintainOrder'=>true,
        'options' => ['placeholder' => Yii::t('ezform', 'Select action ...'), 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,
            'tokenSeparators' => [',', ' '],
        ]
    ]);?>
  </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Display'), 'options[display]', ['class' => 'control-label']) ?>
        <?= kartik\select2\Select2::widget([
        'id'=>'config_display',
        'name' => 'options[display]',
        'value'=>isset($options['display'])?$options['display']:'content_h',
        'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('display'),
        'options' => ['placeholder' => Yii::t('ezform', 'Select Display ...')],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);?>
    </div>
  <div class="col-md-6 sdbox-col" >
        <?= Html::label(Yii::t('ezform', 'Theme'), 'options[theme]', ['class' => 'control-label']) ?>
        <?= kartik\select2\Select2::widget([
        'id'=>'config_theme',
        'name' => 'options[theme]',
        'value'=>isset($options['theme'])?$options['theme']:'default',
        'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('theme'),
        'options' => ['placeholder' => Yii::t('ezform', 'Select Theme ...')],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);?>
    </div>
</div>

<div class="form-group row">
    
    <div class="col-md-6 " >
        <?= yii\bootstrap\Html::hiddenInput('options[initdata]', 0)?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[initdata]', (isset($options['initdata'])?$options['initdata']:0), ['label'=>'initdata'])?>
        <?= yii\bootstrap\Html::hiddenInput('options[disabled_box]', 0)?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[disabled_box]', (isset($options['disabled_box'])?$options['disabled_box']:0), ['label'=>'Disabled Box'])?>
    </div>
</div>

<div class="form-group">
  <?='<div class="alert alert-info" role="alert"> 
        <strong>Variable : </strong> {ezf_id}, {reloadDiv}, {target}, {targetField}, {modal}, {dataid}, {updated_at}, {created_at}
        <br/><strong>Variable Box : </strong> {title}, {content}, {theme}
        <br/><strong>Button : </strong> {save}, {create}, {update}, {delete}, {view}, {data_table}
      </div>'?>
    <?= Html::label(Yii::t('ezform', 'Template Content').' <span class="btn btn-xs btn-info btn-mini">Mini Content</span>
            <span class="btn btn-xs btn-info btn-thumbnail">Thumbnail</span>
            <span class="btn btn-xs btn-info btn-media">Media object</span>
            <span class="btn btn-xs btn-info btn-alert">Alert</span>
            <span class="btn btn-xs btn-info btn-tab">Tab</span>
            <span class="btn btn-xs btn-info btn-accordion">Accordion</span>
            <span class="btn btn-xs btn-info btn-slideshow">Slide Show</span>
            
        ', 'options[template_content]', ['class' => 'control-label']) ?>
  <?= appxq\sdii\widgets\FroalaEditorWidget::widget([
      'name'=>'options[template_content]',
      'value'=>isset($options['template_content'])?$options['template_content']:'',
      'toolbar_size'=>'md',
      'options'=>['id'=>'template_content'],
      'clientOptions'=>[
          'heightMin'=>150,
      ]
  ])?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Template Box').' <span class="btn btn-xs btn-info btn-card">Card</span> <span class="btn btn-xs btn-info btn-panel">Panel</span>', 'options[template_box]', ['class' => 'control-label']) ?>
   <?= appxq\sdii\widgets\FroalaEditorWidget::widget([
      'name'=>'options[template_box]',
      'value'=>isset($options['template_box'])?$options['template_box']:'',
      'toolbar_size'=>'md',
       'options'=>['id'=>'template_box'],
      'clientOptions'=>[
          'heightMin'=>150,
      ]
  ])?>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Parent Name'), 'options[target]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[target]', (isset($options['target'])?$options['target']:'target'), ['class'=>'form-control'])?>
    </div>
</div>
<?php
$thumb_str = '<div class="thumbnail"> <img alt="100%x200" data-src="holder.js/100%x200" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDI0MiAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTY3Yjc1YzhjNWYgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToxMnB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNjdiNzVjOGM1ZiI+PHJlY3Qgd2lkdGg9IjI0MiIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSI4OS44NTkzNzUiIHk9IjEwNS40Ij4yNDJ4MjAwPC90ZXh0PjwvZz48L2c+PC9zdmc+" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;"> <div class="caption"> <h3>Thumbnail label</h3> <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p> <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p> </div> </div>';
$media_str = '<div class="media"> <div class="media-left"> <a href="#"> <img alt="64x64" class="media-object" data-src="holder.js/64x64" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PCEtLQpTb3VyY2UgVVJMOiBob2xkZXIuanMvNjR4NjQKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNjdiNzVjYWRhNSB0ZXh0IHsgZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE2N2I3NWNhZGE1Ij48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIxMy40Njg3NSIgeT0iMzYuNSI+NjR4NjQ8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true" style="width: 64px; height: 64px;"> </a> </div> <div class="media-body"> <h4 class="media-heading">Media heading</h4> Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus. </div> </div>';
$tab_str = '<ul class="nav nav-tabs" id="myTabs" role="tablist"> <li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Home</a></li> <li role="presentation"><a href="#profile" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">Profile</a></li> <li role="presentation" class="dropdown"> <a href="#" class="dropdown-toggle" id="myTabDrop1" data-toggle="dropdown" aria-controls="myTabDrop1-contents">Dropdown <span class="caret"></span></a> <ul class="dropdown-menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents"> <li><a href="#dropdown1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">@fat</a></li> <li><a href="#dropdown2" role="tab" id="dropdown2-tab" data-toggle="tab" aria-controls="dropdown2">@mdo</a></li> </ul> </li> </ul> <div class="tab-content" id="myTabContent"> <div class="tab-pane fade in active" role="tabpanel" id="home" aria-labelledby="home-tab"> <p>Raw denim you probably haven heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica. Reprehenderit butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terry richardson ex squid. Aliquip placeat salvia cillum iphone. Seitan aliquip quis cardigan american apparel, butcher voluptate nisi qui.</p> </div> <div class="tab-pane fade" role="tabpanel" id="profile" aria-labelledby="profile-tab"> <p>Food truck fixie locavore, accusamus mcsweeney marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit. Keytar helvetica VHS salvia yr, vero magna velit sapiente labore stumptown. Vegan fanny pack odio cillum wes anderson 8-bit, sustainable jean shorts beard ut DIY ethical culpa terry richardson biodiesel. Art party scenester stumptown, tumblr butcher vero sint qui sapiente accusamus tattooed echo park.</p> </div> <div class="tab-pane fade" role="tabpanel" id="dropdown1" aria-labelledby="dropdown1-tab"> <p>Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg banh mi whatever gluten-free, carles pitchfork biodiesel fixie etsy retro mlkshk vice blog. Scenester cred you probably haven heard of them, vinyl craft beer blog stumptown. Pitchfork sustainable tofu synth chambray yr.</p> </div> <div class="tab-pane fade" role="tabpanel" id="dropdown2" aria-labelledby="dropdown2-tab"> <p>Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater. Fanny pack portland seitan DIY, art party locavore wolf cliche high life echo park Austin. Cred vinyl keffiyeh DIY salvia PBR, banh mi before they sold out farm-to-table VHS viral locavore cosby sweater. Lomo wolf viral, mustache readymade thundercats keffiyeh craft beer marfa ethical. Wolf salvia freegan, sartorial keffiyeh echo park vegan.</p> </div> </div>';
$accordion_str = '<div class="panel-group" role="tablist" id="accordion" aria-multiselectable="true"> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="headingOne"> <h4 class="panel-title"> <a href="#collapseOne" role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="true" aria-controls="collapseOne" class=""> Collapsible Group Item #1 </a> </h4> </div> <div class="panel-collapse collapse in" role="tabpanel" id="collapseOne" aria-labelledby="headingOne" aria-expanded="true" style=""> <div class="panel-body"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven heard of them accusamus labore sustainable VHS. </div> </div> </div> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="headingTwo"> <h4 class="panel-title"> <a href="#collapseTwo" class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="false" aria-controls="collapseTwo"> Collapsible Group Item #2 </a> </h4> </div> <div class="panel-collapse collapse" role="tabpanel" id="collapseTwo" aria-labelledby="headingTwo" aria-expanded="false" style="height: 0px;"> <div class="panel-body"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven heard of them accusamus labore sustainable VHS. </div> </div> </div> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="headingThree"> <h4 class="panel-title"> <a href="#collapseThree" class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="false" aria-controls="collapseThree"> Collapsible Group Item #3 </a> </h4> </div> <div class="panel-collapse collapse" role="tabpanel" id="collapseThree" aria-labelledby="headingThree" aria-expanded="false" style="height: 0px;"> <div class="panel-body"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven heard of them accusamus labore sustainable VHS. </div> </div> </div> </div>';
$slideshow_str = '<div class="carousel slide" id="carousel-example-generic" data-ride="carousel"> <ol class="carousel-indicators"> <li data-target="#carousel-example-generic" data-slide-to="0" class=""></li> <li data-target="#carousel-example-generic" data-slide-to="1" class=""></li> <li data-target="#carousel-example-generic" data-slide-to="2" class="active"></li> </ol> <div class="carousel-inner" role="listbox"> <div class="item"> <img alt="First slide [900x500]" data-src="holder.js/900x500/auto/#777:#555/text:First slide" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDkwMCA1MDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzkwMHg1MDAvYXV0by8jNzc3OiM1NTUvdGV4dDpGaXJzdCBzbGlkZQpDcmVhdGVkIHdpdGggSG9sZGVyLmpzIDIuNi4wLgpMZWFybiBtb3JlIGF0IGh0dHA6Ly9ob2xkZXJqcy5jb20KKGMpIDIwMTItMjAxNSBJdmFuIE1hbG9waW5za3kgLSBodHRwOi8vaW1za3kuY28KLS0+PGRlZnM+PHN0eWxlIHR5cGU9InRleHQvY3NzIj48IVtDREFUQVsjaG9sZGVyXzE2N2JiNGY3NGU1IHRleHQgeyBmaWxsOiM1NTU7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6NDVwdCB9IF1dPjwvc3R5bGU+PC9kZWZzPjxnIGlkPSJob2xkZXJfMTY3YmI0Zjc0ZTUiPjxyZWN0IHdpZHRoPSI5MDAiIGhlaWdodD0iNTAwIiBmaWxsPSIjNzc3Ii8+PGc+PHRleHQgeD0iMzA4LjI5Njg3NSIgeT0iMjcwLjEiPkZpcnN0IHNsaWRlPC90ZXh0PjwvZz48L2c+PC9zdmc+" data-holder-rendered="true"> </div> <div class="item"> <img alt="Second slide [900x500]" data-src="holder.js/900x500/auto/#666:#444/text:Second slide" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDkwMCA1MDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzkwMHg1MDAvYXV0by8jNjY2OiM0NDQvdGV4dDpTZWNvbmQgc2xpZGUKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNjdiYjRmNTA0NiB0ZXh0IHsgZmlsbDojNDQ0O2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjQ1cHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE2N2JiNGY1MDQ2Ij48cmVjdCB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgZmlsbD0iIzY2NiIvPjxnPjx0ZXh0IHg9IjI2NC45NTMxMjUiIHk9IjI3MC4xIj5TZWNvbmQgc2xpZGU8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true"> </div> <div class="item active"> <img alt="Third slide [900x500]" data-src="holder.js/900x500/auto/#555:#333/text:Third slide" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDkwMCA1MDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzkwMHg1MDAvYXV0by8jNTU1OiMzMzMvdGV4dDpUaGlyZCBzbGlkZQpDcmVhdGVkIHdpdGggSG9sZGVyLmpzIDIuNi4wLgpMZWFybiBtb3JlIGF0IGh0dHA6Ly9ob2xkZXJqcy5jb20KKGMpIDIwMTItMjAxNSBJdmFuIE1hbG9waW5za3kgLSBodHRwOi8vaW1za3kuY28KLS0+PGRlZnM+PHN0eWxlIHR5cGU9InRleHQvY3NzIj48IVtDREFUQVsjaG9sZGVyXzE2N2JiNGY3ZWU0IHRleHQgeyBmaWxsOiMzMzM7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6NDVwdCB9IF1dPjwvc3R5bGU+PC9kZWZzPjxnIGlkPSJob2xkZXJfMTY3YmI0ZjdlZTQiPjxyZWN0IHdpZHRoPSI5MDAiIGhlaWdodD0iNTAwIiBmaWxsPSIjNTU1Ii8+PGc+PHRleHQgeD0iMjk4LjMyMDMxMjUiIHk9IjI3MC4xIj5UaGlyZCBzbGlkZTwvdGV4dD48L2c+PC9nPjwvc3ZnPg==" data-holder-rendered="true"> </div> </div> <a href="#carousel-example-generic" class="left carousel-control" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a> <a href="#carousel-example-generic" class="right carousel-control" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> <span class="sr-only">Next</span> </a> </div>';
$panel_str = '<div class="panel panel-primary"> <div class="panel-heading"> <h3 class="panel-title">Panel title</h3> </div> <div class="panel-body"> Panel content </div> </div>';
$card_str = '<div class="alert alert-info"><div class="pull-right text-right">{create} {update} {delete} {view}</div><h3 class="page-header">{title}</h3>{content}</div>';



$this->registerJS("
    fields($('#config_ezf_id').val());
    pic_fields($('#config_ezf_id').val());
    
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fields(ezf_id);
      pic_fields(ezf_id);
    });
    
    $('.btn-mini').on('click',function(){
        let temp_mini = '<div class=\"alert alert-info\" style=\"padding-top: 5px;padding-bottom: 5px;\">'+
            '<div class=\"pull-right text-right\" style=\"margin-top: 10px;\">{save} {data_table}</div>'+
            '<div class=\"media\" style=\"margin-top: 0px;\">'+
                    '<div class=\"media-left\"><b style=\"font-size: 38px;\">BMI</b></div>'+
                    '<div class=\"media-body\" style=\"vertical-align: middle;\">'+
                      '<div style=\"font-size: 11px;\">{updated_at}</div><span id=\"custom_value\">{bmi}</span>&nbsp; <strong>Value:&nbsp;</strong>height/weight'+
                    '</div>'+
            '</div>'+
    '</div>';
    
        $('#template_content').froalaEditor('html.insert', temp_mini, false);
    });
    
    $('.btn-thumbnail').on('click',function(){
        let temp = '$thumb_str';
        
        $('#template_content').froalaEditor('html.insert', temp, false);
    });
    
    $('.btn-media').on('click',function(){
        let temp = '$media_str';
        
        $('#template_content').froalaEditor('html.insert', temp, false);
    });
    
    $('.btn-tab').on('click',function(){
        let temp = '$tab_str';
        
        $('#template_content').froalaEditor('html.insert', temp, false);
    });

    $('.btn-accordion').on('click',function(){
        let temp = '$accordion_str';
        
        $('#template_content').froalaEditor('html.insert', temp, false);
    });
    
    $('.btn-slideshow').on('click',function(){
        let temp = '$slideshow_str';
        
        $('#template_content').froalaEditor('html.insert', temp, false);
    });
    
    $('.btn-alert').on('click',function(){
        let temp = '<div class=\"alert alert-success\" role=\"alert\"> <strong>Well done!</strong> You successfully read this important alert message. </div>';
      
        $('#template_content').froalaEditor('html.insert', temp, false);
    });
    
    $('.btn-panel').on('click',function(){
        let temp_panel = '<div class=\"panel panel-default\">'+
                    '<div class=\"panel-heading\">'+
                      '<h3 class=\"panel-title\">Panel title</h3>'+
                    '</div>'+
                    '<div class=\"panel-body\">'+
                      'Panel content'+
                    '</div>'+
                  '</div>';
        $('#template_box').froalaEditor('html.insert', temp_panel, false);
    });
    
    $('.btn-card').on('click',function(){
        let temp = '$card_str';
        $('#template_box').froalaEditor('html.insert', temp, false);
    });

    function fields(ezf_id){
        var value = ".$value_fields.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function pic_fields(ezf_id){
        var value = '{$value_image_field}';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_image_field}', value: value ,id:'config_pic_fields'}
          ).done(function(result){
             $('#pic_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>