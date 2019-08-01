<?php

use backend\modules\patient\classes\PatientHelper;
use backend\modules\patient\classes\PatientFunc;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
          <?= Yii::t('patient', 'Refer In') ?> 
      </div>
      <div class="col-md-6 text-right">
        <div>
            <?=
            (isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm').' '.backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezf_id)
                ->label('<i class="fa fa-ambulance"> </i> ')->options(['class'=>'btn btn-sm btn-info'])
                ->buildBtnView($model['id'])
                  : PatientHelper::btnAddTxt('', $ezf_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm'));
            ?></div>
      </div>
    </div>
  </div>
  <div class="card-block"> 
    <div class="row">
      <div class="col-md-12" >
        <strong><?= Yii::t('patient', 'Refer In Hospital') ?> : </strong>
        <span class="text-info">
            <?php
            if (isset($model['id'])) {
                $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])->all();
                $value = PatientFunc::getRefTableName($modelFields, 'refer_rece_hos');
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                }
                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model);
            }
            ?>
        </span>   
      </div>
    </div>
    <hr>
    <div class="row ">
      <div class="col-md-12" >
        <strong><?= Yii::t('patient', 'Refer Note') ?> : </strong>
        <span class="text-info"><?= (isset($model['id']) ? $model['refer_rece_note'] : ''); ?></span>
      </div>
    </div>
    <hr>
    <div class="row ">
      <div class="col-md-12" >
        <strong><?= Yii::t('patient', 'Refer Diagnosis') ?> : </strong>
        <span class="text-info">
            <?php
            if (isset($model['id'])) {
                $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])->all();
                $value = PatientFunc::getRefTableName($modelFields, 'refer_rece_diag');
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                }
                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model);
            }
            ?>
        </span>
      </div>
    </div>
    <hr>
    <div class="row ">
      <div class="col-md-12">
        <strong><?= Yii::t('patient', 'Attachment') ?> : </strong>
        <?php
        if (isset($model['id'])) :
            echo ($model['refer_result_lab'] ? '<span class="text-info"><i class="fa fa-check-square-o"></i> LAB</span>,' : '');
            echo ($model['refer_result_pa'] ? ' <span class="text-info"><i class="fa fa-check-square-o"></i> Pathology</span>,' : '');
            echo ($model['refer_result_cyto'] ? ' <span class="text-info"><i class="fa fa-check-square-o"></i> Cytology</span>,' : '');
            echo ($model['refer_result_ct'] ? ' <span class="text-info"><i class="fa fa-check-square-o"></i> CT Scan</span>,' : '');
            echo ($model['refer_result_mri'] ? ' <span class="text-info"><i class="fa fa-check-square-o"></i> MRI</span>,' : '');
            echo ($model['refer_result_us'] ? ' <span class="text-info"><i class="fa fa-check-square-o"></i> U/S</span>,' : '');
            echo ($model['refer_result_ot'] ? ' <span class="text-info"><i class="fa fa-check-square-o"></i> อื่นๆ : ' . $model['refer_result_txt'] . '</span>' : '');
        endif;               
        ?>
      </div>
    </div> 
  </div>
</div>