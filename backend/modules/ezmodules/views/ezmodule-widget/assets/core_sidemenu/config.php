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


?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->
<div id="config-box">
<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
    </div>
  <div class="col-md-6 sdbox-col" >
        <?= Html::label(Yii::t('ezform', 'SQL'), 'options[sql]', ['class' => 'control-label']) ?>
        <?php
        $sql = isset($options['sql'])?$options['sql']:0;
        $data_sql = backend\modules\ezforms2\classes\EzfQuery::getSqlById($sql);
        $str = $sql;
        if($data_sql){
            $str = $data_sql['name'];
        }
        
        echo appxq\sdii\widgets\SDSqlBuilder::widget([
            'name'=>'options[sql]',
            'id'=>'sql_config',
            'value'=>isset($options['sql'])?$options['sql']:'',
            'initValueText'=>$str,
            'pluginEvents' => [
                "select2:select" => "function(e) { 
                  variable(e.params.data.id);
                  image(e.params.data.id); 
                  search(e.params.data.id);
                  key_id(e.params.data.id);
                }",
		"select2:unselect" => "function() { $('#var-text-box').html(''); image(0); search(0); key_id(0);}"
	    ]
        ]);
        ?>
    </div>
</div>
<div class="alert alert-info" role="alert"> 
    <strong>Variable : </strong>
    <span data-content="{title}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px;">{title}</span>
    <span data-content="{url}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{url}</span>
    <span data-content="{module}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{module}</span>
    <span data-content="{reloadDiv}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{reloadDiv}</span>
    <span data-content="{target}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{target}</span>
    <span data-content="{image}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{image}</span>
    <span id="var-text-box" > </span> 
</div>
  

  
<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Query String ($_GET)'), 'options[query_params]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[query_params]', (isset($options['query_params'])?$options['query_params']:'target={target}&dataid={key_id}'), ['class'=>'form-control'])?>
    </div>
</div>

<div class="form-group row">
    
  
    <div class="col-md-3 ">
        <?= Html::label(Yii::t('ezform', 'Image Width'), 'options[image_wigth]', ['class' => 'control-label']) ?>
      <div class="input-group">
        <?= Html::textInput('options[image_wigth]', (isset($options['image_wigth'])?$options['image_wigth']:'64'), ['class'=>'form-control', 'type'=>'number'])?>
        <span class="input-group-addon">px</span>
      </div>
    </div>
  
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Image Field (if any)'), 'options[image]', ['class' => 'control-label']) ?>
      <?php
      $attrname_image = 'options[image]';
      $value_image = isset($options['image'])?$options['image']:'';
      ?>
      <div id="image-variable-box"></div>
    </div>
  
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Search by sql params'), 'options[search]', ['class' => 'control-label']) ?>
      <?php
      $attrname_search = 'options[search]';
      $value_search = isset($options['search'])?$options['search']:'';
      ?>
      <div id="search-variable-box"></div>
    </div>
  <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Key ID'), 'options[key_id]', ['class' => 'control-label']) ?>
        <?php
        $attrname_key = 'options[key_id]';
        $value_key = isset($options['key_id'])?$options['key_id']:'';
        ?>
        <div id="key-id-box"></div>
      </div>
</div>

  <div class="form-group row">
    <div class="col-md-3 ">
        <?= Html::label(Yii::t('ezform', 'Menu Width'), 'options[width]', ['class' => 'control-label']) ?>
        <div class="input-group">
            <?= Html::textInput('options[width]', (isset($options['width'])?$options['width']:'300'), ['class'=>'form-control', 'type'=>'number'])?>
            <span class="input-group-addon">px</span>
          </div>
        
    </div>
<!--    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Margin Left'), 'options[margin_left]', ['class' => 'control-label']) ?>
        <div class="input-group">
            <?= Html::textInput('options[margin_left]', (isset($options['margin_left'])?$options['margin_left']:'45'), ['class'=>'form-control', 'type'=>'number'])?>
            <span class="input-group-addon">px</span>
          </div>
        
    </div>-->
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Page Size'), 'options[page_size]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[page_size]', (isset($options['page_size'])?$options['page_size']:'50'), ['class'=>'form-control', 'type'=>'number'])?>
    </div>
  </div>  

<?php
$media_image = '<div><a href="{url}" class="media {active}" ><div class="media-left">{image}</div><div class="media-body" ><h4 class="list-group-item-heading">HN {hn} CID {cid}</h4><div class="list-group-item-text"><div><strong>Name : </strong>{fname} {lname}</div><div>The default media displays a media object. </div></div></div></a></div>';
$media = '<div><a href="{url}" class="media {active}" ><div class="media-body" ><h4 class="list-group-item-heading">HN {hn} CID {cid}</h4><div class="list-group-item-text"><div><strong>Name : </strong>{fname} {lname}</div><div>The default media displays a media object. </div></div></div></a></div>';

?>
<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Content Template').' 
            <span data-content="'.Html::encode($media_image).'" class="btn btn-xs btn-info btn-content">Media Image</span>
            <span data-content="'.Html::encode($media).'" class="btn btn-xs btn-info btn-content">Media</span>
        ', 'options[template_content]', ['class' => 'control-label', 'id'=>'widget-temp-box']) ?>
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

</div>
<!--config end-->

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    variable('<?=$sql?>');
    image('<?=$sql?>');
    search('<?=$sql?>');  
    key_id('<?=$sql?>');  
    
    $('#config-box').on('click', '.btn-content',function(){
        $('#template_content').froalaEditor('html.insert', $(this).attr('data-content'), false);
    });
    
    function variable(sql_id){
        $.post('<?=Url::to(['/ezforms2/custom-config/get-variable'])?>',{ sql_id: sql_id}
          ).done(function(result){
             $('#var-text-box').html(result);
          }).fail(function(){
              <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"')?>
              console.log('server error');
          });
    }
    
    function key_id(sql_id){
        let value = '<?=$value_key?>';
        $.post('<?=Url::to(['/ezforms2/custom-config/get-fields'])?>',{ sql_id: sql_id, multiple:0, name: '<?=$attrname_key?>', value: value ,id:'config_key_id'}
          ).done(function(result){
             $('#key-id-box').html(result);
          }).fail(function(){
              <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"')?>
              console.log('server error');
          });
    }
    
    function image(sql_id){
        let value = '<?=$value_image?>';
        $.post('<?=Url::to(['/ezforms2/custom-config/get-fields'])?>',{ sql_id: sql_id, multiple:0, name: '<?=$attrname_image?>', value: value ,id:'config_image_variable'}
          ).done(function(result){
             $('#image-variable-box').html(result);
          }).fail(function(){
              <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"')?>
              console.log('server error');
          });
    }
    
    function search(sql_id){
        let value = '<?=$value_search?>';
        $.post('<?=Url::to(['/ezforms2/custom-config/get-params'])?>',{ sql_id: sql_id, multiple:0, name: '<?=$attrname_search?>', value: value ,id:'config_search_params'}
          ).done(function(result){
             $('#search-variable-box').html(result);
          }).fail(function(){
              <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"')?>
              console.log('server error');
          });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>