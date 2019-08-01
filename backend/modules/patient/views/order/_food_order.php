<?php

use backend\modules\patient\classes\PatientHelper;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
        <i class="fa fa-stethoscope"></i> <?= Yii::t('patient', 'Food Order') ?>
      </div>
      <div class="col-md-6 text-right">
        <div>
            <?php
            //(isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ward-xxl', 'btn-sm') : ''). ' ' .
            echo PatientHelper::btnAddTxt('', $ezf_id, $admit_id, [], $reloadDiv, 'modal-ward-xxl', 'btn-sm')
            . ' ' . PatientHelper::btnViewTxt('', $ezf_id, $admit_id, ['food_admit_master', 'food_admit_excep', 'food_admit_diag', 'food_admit_comment'], 'modal-ward-xxl', 'btn-sm');
            ?>
        </div>
      </div>
    </div>

  </div>
  <div class="card-block">
    <div class="row">
      <div class="col-md-4">
        <strong>ประเภทอาหาร : </strong>
        <span class="text-info">
            <?php
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'food_admit_master', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            ?>
        </span>
      </div>
      <div class="col-md-4">
        <strong>เฉพาะโรค : </strong>
        <span class="text-info">
            <?php
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'food_admit_diag', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            ?>
        </span>
      </div>
      <div class="col-md-4">
        <strong>เงื่อนไข : </strong>
        <span class="text-info">
            <?php
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'food_admit_excep', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            ?>
        </span>
      </div>
      <div class="col-md-12">
        <strong>หมายเหตุ : </strong>
        <span class="text-info">
            <?php
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'food_admit_comment', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            ?>
        </span>
      </div>
      <div class="col-md-12">
        <strong>หมายเหตุ : </strong>
        <span class="text-info">
          <?= $model['food_admit_comment_txt'] ?>
        </span>
      </div>
    </div>
  </div>
</div>



