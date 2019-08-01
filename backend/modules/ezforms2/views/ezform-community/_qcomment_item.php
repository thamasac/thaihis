<?php

use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//appxq\sdii\utils\VarDumper::dump($value,1,0);

//if($value->send_to!=''){
//    $to_name = $value->send_to;
//    $send_to_name = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn($to_name);
//    if($send_to_name){
//        $send_to_name = yii\helpers\ArrayHelper::getColumn($send_to_name, 'fullname');
//        $value->send_to_name = implode(', ', $send_to_name);
//    }
//}

$modelEzf = EzfQuery::getEzformOne($value->object_id);
$version = $modelEzf->ezf_version;
$modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $value->dataid);

if ($modelZdata) {
    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
        $version = (in_array($modelZdata->rstat, [0,1]))?$version:$modelZdata->ezf_version;
    }
    if(!empty($modelZdata->ezf_version)){
        $modelEzf->ezf_version = (in_array($modelZdata->rstat, [0,1]))?$version:$modelZdata->ezf_version;
    }
}

$modelFields = EzfQuery::getFieldByNameVersion($modelEzf->ezf_id, $value->field, $version);


$items = ['Waiting', 'Resolve with out any change.', 'Resolve with some change.', 'Unresolvable'];
$dataInput;
if (isset(Yii::$app->session['ezf_input'])) {
    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
}
$modelFields['ezf_field_name'] = 'value_old';
$value_old = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $value);

$modelFields['ezf_field_name'] = 'value_new';
$value_new = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $value);
?>
<div class="media"> 
    <?php
    if (Yii::$app->user->can('administrator') && 0) {
        ?>
        <button type="button" class="close commt-del-btn" data-id="<?= $value['id'] ?>" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?php } ?>
    <div class="media-left"> 
        <a > 
            <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="<?= (isset($value->avatar_path) && $value->avatar_path != '') ? Yii::getAlias('@storageUrl/source') . '/' . $value['avatar_path'] : ModuleFunc::getNoUserImage() ?>" data-holder-rendered="true"> 
        </a> 
    </div> 
    <div class="media-body"> 
      <h4 class="media-heading">
        <i class="glyphicon glyphicon-user"></i> <?= $value['user_name'] ?> 
        <small><i class="glyphicon glyphicon-calendar"></i> <?= \appxq\sdii\utils\SDdate::mysql2phpDateTime($value['created_at']) ?></small> 
        <?php  //echo (isset($value->send_to_name)?'<code>@'.$value['send_to_name'].'</code>':'')?> 
        <?php if($value['type']=='ezform' && $value['dataid']>0 && $value['dataid']!=$dataid):?>
        <?= backend\modules\ezforms2\classes\EzfHelper::btn($value['object_id'])->modal($modal)->options(['class'=>'btn btn-info btn-xs'])->buildBtnView($value['dataid'])?>
        <?php endif;?>
      </h4> 
        <p><?= $value['content'] ?></p> 
          <div class="alert alert-info" role="alert">
            <div>
              <strong>Status : </strong> <?= isset($value['approv_status'])?$items[$value['approv_status']]:''?>
            </div>
            <?php if($value['approv_status']==2):?>
            <strong>Old : <code><?=$value_old?></code></strong> -> <strong>New : <code><?=$value_new?></code></strong>
            <?php endif;?>
        </div>
    </div> 
</div> 


