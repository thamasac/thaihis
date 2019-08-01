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
  <div class="col-md-12" >
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
    <span data-content="{module}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{module}</span>
    <span data-content="{reloadDiv}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{reloadDiv}</span>
    <span data-content="{modal}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{modal}</span>
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
        <?= Html::label(Yii::t('ezform', 'Image Field'), 'options[image]', ['class' => 'control-label']) ?>
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
            <?= Html::textInput('options[width]', (isset($options['width'])?$options['width']:'450'), ['class'=>'form-control', 'type'=>'number'])?>
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
        <?= Html::textInput('options[page_size]', (isset($options['page_size'])?$options['page_size']:'10'), ['class'=>'form-control', 'type'=>'number'])?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'KeyID Name'), 'options[key_name]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[key_name]', (isset($options['key_name'])?$options['key_name']:'dataid'), ['class'=>'form-control'])?>
    </div>

  </div>  

<div class="form-group row">
    <div class="col-md-6">
      <?php
      $attrname_placeholder = 'options[placeholder]';
      $value_placeholder = isset($options['placeholder'])?$options['placeholder']:'Search ...';//4-ASC - 3-DESC
      ?>
        <?= Html::label(Yii::t('ezform', 'Short hint'), $attrname_placeholder, ['class' => 'control-label']) ?>
        <?php 
        echo Html::textInput($attrname_placeholder, $value_placeholder, ['class' => 'form-control ']);
        ?>
    </div>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Items Template').' <span class="btn btn-xs btn-info btn-his">HIS</span>', 'options[template_items]', ['class' => 'control-label']) ?>
   <?= Html::textarea('options[template_items]', isset($options['template_items'])?$options['template_items']:'', ['id'=>'template_items', 'class' => 'form-control', 'row'=>3])?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Selection Template'), 'options[template_selection]', ['class' => 'control-label']) ?>
   <?= Html::textarea('options[template_selection]', isset($options['template_selection'])?$options['template_selection']:'', ['id'=>'template_selection', 'class' => 'form-control', 'row'=>3])?>
</div>

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
    
    var his_patient = '<div class=\"row\">'+
                '<div class=\"col-sm-12\">'+
                  '<div style=\"margin-bottom: 5px;font-size: 17px;\"><strong>HN : {hn}</strong> Name : {fname} {lname}</div>'+
                  '<div style=\"margin-bottom: 5px;font-size: 15px;\"><strong>CID : </strong>{cid}</div>'+
                  '<div style=\"margin-bottom: 5px;font-size: 15px;\"><strong>Birthday : </strong><span>{bod}</span> <strong>Age : </strong>{fix_age}</div>'+
                '</div>'+
              '</div>';
              
    $('.btn-his').on('click',function(){
        $('#template_items').val(his_patient);
        $('#template_selection').val('<strong>HN : {hn}</strong> Name : {fname} {lname} <strong>CID : </strong>{cid}');
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
