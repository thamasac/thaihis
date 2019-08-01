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
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

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
                  key_id(e.params.data.id);
                  fields(e.params.data.id);
                }",
		"select2:unselect" => "function() { $('#var-text-box').html(''); fields(0); key_id(0);}"
	    ]
        ]);
        ?>
    </div>
</div>
<div class="alert alert-info" role="alert"> 
    <strong>Variable : </strong>
    <span data-content="{title}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px;">{title}</span>
    <span data-content="{module}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{module}</span>
    <span data-content="{reloadDiv}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{reloadDiv}</span>
    <span data-content="{modal}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{modal}</span>
    <span id="var-text-box" > </span> 
</div>

<div class="form-group row">
    <div class="col-md-12">
      <?php
      $attrname_fields = 'options[fields]';
      $value_fields_json = isset($options['fields']) && is_array($options['fields'])?\appxq\sdii\utils\SDUtility::array2String($options['fields']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Fields to display'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            
        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-3 ">
      <?php
      $attrname_pagesize = 'options[pagesize]';
      $value_pagesize = isset($options['pagesize'])?$options['pagesize']:50;
      ?>
        <?= Html::label(Yii::t('ezform', 'Page Size'), $attrname_pagesize, ['class' => 'control-label']) ?>
        <?php 
        echo Html::textInput($attrname_pagesize, $value_pagesize, ['class' => 'form-control ', 'type'=>'number']);
        ?>
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

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title"><?= Yii::t('ezmodule', 'Custom Column')?></h4>
</div>
<div class="form-group">
  <div class="row">
    <div class="col-md-3"><label><?= Yii::t('ezform', 'Varname')?></label></div>
    <div class="col-md-6 sdbox-col"><label><?= Yii::t('ezform', 'Label')?></label></div>
    <div class="col-md-2 sdbox-col"></div>
  </div>
  <div id="header-item-box">
  <?php
  if(isset($options['header']) && is_array($options['header']) && !empty($options['header'])){
      foreach ($options['header'] as $key_header => $value_header) {
          ?>
          <div id="<?=$key_header?>" class="row" style="margin-bottom: 10px;">
            <div class="col-md-3"><input type="text" class="form-control varname-input" name="options[header][<?=$key_header?>][varname]" value="<?= isset($value_header['varname'])?$value_header['varname']:''?>"></div>
            <div class="col-md-3 sdbox-col"><input type="text" class="form-control label-input" name="options[header][<?=$key_header?>][label]" value="<?= isset($value_header['label'])?$value_header['label']:''?>"></div>
            <div class="col-md-2 sdbox-col"> <?= Html::dropDownList("options[header][$key_header][align]", isset($value_header['align'])?$value_header['align']:'', ['left'=>'Left', 'right'=>'Right', 'center'=>'Center'], ['class'=>'form-control align-input'])?></div>
            <div class="col-md-2 sdbox-col"><input type="number" class="form-control width-input" name="options[header][<?=$key_header?>][width]" value="<?= isset($value_header['width'])?$value_header['width']:''?>" ></div>
            <div class="col-md-2 sdbox-col"><a href="#" class="header-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
          </div>
          <?php
      }
  }
  ?>
  </div>
  <div class="row">
      <div class="col-md-4">
        <a href="#" class="header-items-add btn btn-success"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezmodule', 'Custom Column')?></a>
    </div>
      
  </div>
  
  
</div>

<div class="modal-header" style="margin-bottom: 15px;">
  <h4 class="modal-title"><?= Yii::t('ezmodule', 'Actions Column {action}')?></h4> 
</div>
<div class="form-group">
  <div class="alert alert-info">
    <strong>Variable : </strong> {title} {reloadDiv} {modal} {sitecode} {department} {user} ... {[data_fields]} <br>
    <strong>Button : </strong> <?= Html::encode('<button class="btn btn-default btn-xs btn-action " data-url="/ezforms2/btn-action/update-data?ezf_id={ezf_id}&id={id}&field=&value=">Click</button>')?><br>
    <strong>Link : </strong> <?= Html::encode('<a href="#" class="btn btn-default btn-xs btn-action " data-url="/ezforms2/btn-action/update-data?ezf_id={ezf_id}&id={id}&field=&value=">Click</a>')?>
  </div>
  <div class="row">
    <div class="col-md-6"><label><?= Yii::t('ezform', 'Actions')?> </label></div>
    <div class="col-md-4 sdbox-col"><label><?= Yii::t('ezform', 'Show data conditions')?></label></div>
    <div class="col-md-2"></div>
  </div>
  <div id="actions-item-box">
  <?php
  if(isset($options['actions']) && is_array($options['actions']) && !empty($options['actions'])){
      foreach ($options['actions'] as $key_action => $value_action) {
          ?>
          <div id="<?=$key_action?>" class="row" style="margin-bottom: 10px;">
            <div class="col-md-6"><input type="text" class="form-control action-input" name="options[actions][<?=$key_action?>][action]" value="<?= isset($value_action['action'])?Html::encode($value_action['action']):''?>"></div>
            <div class="col-md-4 sdbox-col"><input type="text" class="form-control cond-input" name="options[actions][<?=$key_action?>][cond]" value="<?= isset($value_action['cond'])?$value_action['cond']:''?>"></div>
            <div class="col-md-2 sdbox-col"><a href="#" class="action-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
          </div>
          <?php
      }
  }
  ?>
  </div>
  <div class="row">
      <div class="col-md-4">
        <a href="#" class="action-items-add btn btn-success"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezmodule', 'Actions Column')?></a>
    </div>
      
  </div>
  
  
</div>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    key_id('<?=$sql?>');  
    variable('<?=$sql?>');
    fields('<?=$sql?>');  
    
    function variable(sql_id){
        $.post('<?=Url::to(['/ezforms2/custom-config/get-variable'])?>',{ sql_id: sql_id}
          ).done(function(result){
             $('#var-text-box').html(result);
          }).fail(function(){
              <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"')?>
              console.log('server error');
          });
    }
    
    function fields(sql_id){
        let value = <?=$value_fields_json?>;
        $.post('<?=Url::to(['/ezforms2/custom-config/get-fields'])?>',{ sql_id: sql_id, multiple:1, path_enable:0, name: '<?=$attrname_fields?>', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"')?>
              console.log('server error');
          });
    }
    
    function key_id(sql_id){
        let value = '<?=$value_key?>';
        $.post('<?=Url::to(['/ezforms2/custom-config/get-fields'])?>',{ sql_id: sql_id, multiple:0, path_enable:0, name: '<?=$attrname_key?>', value: value ,id:'config_key_id'}
          ).done(function(result){
             $('#key-id-box').html(result);
          }).fail(function(){
              <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"')?>
              console.log('server error');
          });
    }
    
    $('#actions-item-box').on('click', '.action-items-del', function(){
        $(this).parent().parent().remove();
    });
    
    $('.action-items-add').on('click', function(){
        $.ajax({
            method: 'POST',
            url: '<?=Url::to(['/ezmodules/ezmodule-tab/get-widget', 'view'=>'/ezmodule-widget/assets/core_sql_grid/_form_action'])?>',
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#actions-item-box').append(result);
            }
        });
    });
    
    $('#header-item-box').on('click', '.header-items-del', function(){
        $(this).parent().parent().remove();
    });
    
    $('.header-items-add').on('click', function(){
        getWidget();
    });

    function getWidget() {
        $.ajax({
            method: 'POST',
            url: '<?=Url::to(['/ezmodules/ezmodule-tab/get-widget', 'view'=>'/ezmodule-widget/assets/core_sql_grid/_form_header'])?>',
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#header-item-box').append(result);
            }
        });
    }
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>