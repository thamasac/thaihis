<?php

use backend\modules\patient\classes\PatientHelper;
use yii\helpers\Html;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
        <i class="fa fa-ambulance"></i> <?= Yii::t('patient', 'History Taking') ?> 
      </div>
      <div class="col-md-6 text-right">
        <div>
            <?php
            echo '<label>' . (isset($model['create_date']) ? appxq\sdii\utils\SDdate::mysql2phpThDateTime($model['create_date']) : '') . '</label>';
            if (!$btnDisabled) {
                echo (isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm') : PatientHelper::btnAddTxt('', $ezf_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm'));
            } else {
                if (isset($model['id'])) {
                    echo backend\modules\ezforms2\classes\BtnBuilder::btn()
                            ->ezf_id($ezf_id)
                            ->label('<i class="fa fa-eye"></i>')->options(['class' => 'btn btn-info'])
                            ->buildBtnView($model['id']);
                }
            }
            ?></div>
      </div>
    </div>
  </div>
  <div class="card-block">
    <div class="row">
      <div class="col-md-6">
        <strong>CC : </strong>
        <?= (isset($model['id']) ? $model['tk_cc'] : ''); ?>
      </div>
      <div class="col-md-6 sdbox-col">
        <strong>PH : </strong>
        <?= (isset($model['id']) ? $model['tk_ph'] : ''); ?>
      </div>
    </div>
    <hr>
    <div class="row ">
      <div class="col-md-6">
        <strong>PI : </strong>
        <?= (isset($model['id']) ? $model['tk_pi'] : ''); ?>
      </div>
      <div class="col-md-6 sdbox-col">
        <strong>FH : </strong>
        <?= (isset($model['id']) ? $model['tk_fh'] : ''); ?>
      </div>
    </div>
    <hr>
    <div class="row ">
      <div class="col-md-6">
        <strong>การตรวจ : </strong>
        <?php
        if ($model['tk_inspect']) {
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'tk_inspect', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
        }
        ?>
      </div>
      <div class="col-md-6">
        <strong>NPO : </strong>
        <?php
        if ($model['tk_npo']) {
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'tk_npo', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            if ($model['tk_npo'] == '1') {
                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            } elseif ($model['tk_npo'] == '2') {
                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            }
        }
        ?>
      </div>
    </div>
    <?php if ($model['tk_result_out'] && $model['tk_result_file']) { ?>
        <div class="row ">
          <div class="col-md-12">
            <strong>ผู้ป่วยมีไฟล์แนบ : </strong> <?=
                    backend\modules\ezforms2\classes\BtnBuilder::btn()
                    ->ezf_id($ezf_id)
                    ->label('<i class="fa fa-eye"></i>')->options(['class' => 'btn btn-sm btn-info'])
                    ->buildBtnView($model['id'])
            ?>
          </div>
        </div>
    <?php } ?>
  </div>
</div>

