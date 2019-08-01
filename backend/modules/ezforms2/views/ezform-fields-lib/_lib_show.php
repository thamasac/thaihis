<?php

use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\controllers\EzformFieldsLibController;
use yii\web\JsExpression;
?>
<div class="form-group">
  <label> <?= Yii::t('ezform', 'Question Library') ?> </label>
  <ul class="h4">
      <?php if ($data) : ?>
        <li>
          <?= Yii::t('ezform', 'Group Question Library') ?> : <strong class="text-info"><?= $data['lib_group_name'] ?> </strong>
          <?php
          echo ' ' . Html::button('<i class="fa fa-pencil"></i> ', [
              'data-toggle' => 'tooltip',
              'title' => Yii::t('yii', 'Update'),
              'class' => 'btn btn-primary btn-xs btn-open-lib-edit',
              'data-url' => Url::to(['/ezforms2/ezform-fields-lib/update', 'id' => $data['field_lib_id'], 'mode' => 'ezform'])])
          . ' ' . Html::button('<i class="glyphicon glyphicon-trash"></i> ', [
              'data-toggle' => 'tooltip',
              'title' => Yii::t('yii', 'Delete'),
              'class' => 'btn btn-danger btn-xs btn-open-lib-delete',
              'data-url' => Url::to(['/ezforms2/ezform-fields-lib/delete', 'id' => $data['field_lib_id'], 'mode' => 'ezform'])])
          ?>
        </li>
        <li style="margin-top: 10px;"><?= Yii::t('ezform', 'Name Question Library') ?> : <strong class="text-success"><?= $data['field_lib_name'] ?></strong></li>
        <li style="margin-top: 10px;"><?= Yii::t('ezform', 'Shared Question Library') ?> : <strong class="text-success"><?= EzformFieldsLibController ::itemAlias('public', ( $data['field_lib_share']) ? $data['field_lib_share'] : '') ?></strong></li>
        <li style="margin-top: 10px;"><?= Yii::t('ezform', 'Status') ?> : <strong class="text-success"><?= EzformFieldsLibController ::itemAlias('status', $data['field_lib_status']) ?></strong></li>
    <?php else : ?>
        <li>
            <?php
            echo Yii::t('ezform', 'Archive To Question Library') . ' ' . Html::button('<i class="glyphicon glyphicon-plus"></i> ', [
                'data-toggle' => 'tooltip',
                'title' => Yii::t('ezform', 'Add Question Library'),
                'class' => 'btn btn-success btn-xs btn-open-lib-add',
                'data-url' => Url::to(['/ezforms2/ezform-fields-lib/quick-create',
                    'action' => 'add_lib',
                    'ezf_id' => $ezf_id,
                    'ezf_field_id' => $ezf_field_id])])
            ?>
        </li>
    <?php endif; ?>
  </ul>
</div>
<hr>
<div class="form-group" style="margin-top: 10px">
  <label><?= Yii::t('ezform', 'Question Standard') ?></label>
  <div class="alert alert-success" style="margin-bottom: 0px; margin-top: 5px;">
    <div id="box-data">
      <div class="form-group row">
        <div class="col-md-4">
          <label for="standard-group"><?= Yii::t('ezform', 'Standard Group') ?></label>
          <?php
          echo \kartik\widgets\Select2::widget([
              'id' => 'standard-group-select',
              'name' => 'standard-group',
              'data' => [0 => 'กระทรวงสาธารณสุข (43 แฟ้ม)', 1 => 'โรงพยาบาลขอนแก่น', 2 => 'องค์กรแพทย์', 3 => 'องค์กรพยาบาล'],
              'options' => ['placeholder' => 'รายงาน', 'multiple' => FALSE, 'class' => 'form-control'],
              'pluginOptions' => [
                  'allowClear' => true,
              ],
          ]);
          ?>
        </div>
        <div id="standard-ezform" class="col-md-4 hidden">
          <label for="standard-group"><?= Yii::t('ezform', 'Standard Form') ?></label>
          <?php
//        $ezf_initValue = empty($model->ezf_id) ? '' : \backend\modules\ezforms2\models\Ezform::findOne($model->ezf_id)->ezf_name;
          echo \kartik\widgets\Select2::widget([
//            'initValueText' => $ezf_initValue,
              'id' => 'standard-ezform-select',
              'name' => 'standard-ezform',
              'options' => ['placeholder' => Yii::t('ezform', 'Form')],
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
          ]);
          ?>
        </div>
        <div id="standard-ezfield" class="col-md-4 hidden">
          <label for="standard-group"><?= Yii::t('ezform', 'Standard Question') ?></label>
          <?php
//      $ezf_initValue = empty($model->ezf_field_id) ? '' : \backend\modules\ezforms2\models\EzformFields::findOne($model->ezf_field_id)->ezf_field_name;
          echo \kartik\widgets\Select2::widget([
//          'initValueText' => $ezf_initValue,
              'id' => 'standard-ezfield-select',
              'name' => 'standard-ezfield',
              'options' => ['placeholder' => Yii::t('ezform', 'Search Question')],
              'pluginOptions' => [
                  'minimumInputLength' => 0,
                  'allowClear' => true,
                  'ajax' => [
                      'url' => Url::to(['/ezforms2/ezform-fields-lib/get-fields']),
                      'dataType' => 'json',
                      'data' => new JsExpression("function(params) { return {q:params.term,ezf_id:$('#standard-ezform-select').val()}; }")
                  ],
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

  <?php
  $this->registerJS("
    hideStandard($('#standard-group-select').val(),'#standard-ezform');
    hideStandard($('#standard-ezform-select').val(),'#standard-ezfield');

    $('#standard-group-select').on('change', function (e) {
        if($(this).val()){
            hideStandard($(this).val(),'#standard-ezform');
        }else{
            hideStandard($(this).val(),'#standard-ezform,#standard-ezfield');
        }      
    });
    
    $('#standard-ezform-select').on('change', function (e) {
      hideStandard($(this).val(),'#standard-ezfield');
    });

    function hideStandard(val,prentName) {
      if (val) {
        $(prentName).removeClass('hidden');        
      } else {
        $(prentName).addClass('hidden');
      }
    }

    $('.btn-open-lib-add,.btn-open-lib-delete').on('click',function(){
        let url = $(this).attr('data-url');
        if(url){
            $.ajax({
            method: 'POST',
            url: url,
            dataType: 'JSON',
            success: function (result) {
                    if(result.status == 'success') {
                    " . SDNoty::show('result.message', 'result.status') . "
                    reloadDiv();
                    } else {
                    ". SDNoty::show('result.message', 'result.status') ."
                    }               
                }
            }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
            });
        }        
    });

    $('.btn-open-lib-edit').on('click',function(){
        let url = $(this).attr('data-url');
        modalEzformFieldsLib(url);
    });

    function modalEzformFieldsLib(url, modal = 'modal-ezform-version') {
      $('#' + modal + ' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
      $('#' + modal).modal('show')
              .find('.modal-content')
              .load(url);
    }
    
    $('#modal-ezform-version').off('hidden.bs.modal');
    $('#modal-ezform-version').on('hidden.bs.modal', function (e) {
        reloadDiv();
    });
    
        $('#modal-ezform-version').on('hidden.bs.modal', function(e){                
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
            }
        });
    
    function reloadDiv(){
        let url = $('#lib-grid-lists').attr('data-url');
        if(url){
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#lib-grid-lists').html(result);
                }
            });
        }       
    }
   
    ");
  ?>