<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

//$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
//if ($target) {
//    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
//} else {
//}



?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title"><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<div class="panel panel-default" id="PieConfig" data-cache="">
    <div class="panel-heading">

        <!--        <h3>Add Pieces</h3>-->

        <div>
            Add Pieces
            <buttoon class="btn" id="btnAdd">Add</buttoon>
        </div>
    </div>
    <div class="panel-body" id="rootDiv">
            <div class="form-group row">
                <div class="col-md-1 "></div>
                <div class="col-md-3 ">
                    <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                </div>
                <div class="col-md-3 sdbox-col">
                    <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
                </div>
                <div class="col-md-2 sdbox-col">
                    <b>X/Percent</b>
                </div>
            </div>
    </div>

</div>

<?php

$optionJson = json_encode($options);
$this->registerJS("
    function addRow(_ezf_id,_field,_handicap,_label){
        $.post('" . Url::to(['/ezwidget/ez-widget/get-pie-option']) . "',{ 'ezf_id': _ezf_id, 'label': _label, 'field': _field,'handicap':_handicap})
        .done(function(result){
            $('#rootDiv').append(result);
        }).fail(function(){
            " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
            console.log('server error');
        });
    }
    
    (function(){
      var optionJs = $optionJson;
      console.log(optionJs );
      if(optionJs['ezf_id'] != undefined){
          for(var i = 0; i < optionJs['ezf_id'].length;i++){
                addRow( optionJs['ezf_id'][i] , optionJs['fields'][i] , optionJs['handicap'][i] , optionJs['labels'][i] );
          }
      }
      
    })();
    
    $( '#btnAdd' ).click(function() {
        addRow('','',1);
    });
"
);
?>
