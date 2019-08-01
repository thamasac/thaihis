<?php

use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\PatientFunc;

$modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])->all();
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
        <i class="fa fa-calendar-check-o"></i> <?= Yii::t('patient', 'Certificate') ?> 
      </div>
    </div>
  </div>
  <div class="card-block"> 
    <div class="row">
      <div class="col-md-6" >
        <strong><?= Yii::t('patient', 'Doctor') ?> :</strong>
        <span class="text-info">
            <?php
            if (isset($model['id'])) {
                $dataField = PatientFunc::getRefTableName($modelFields, 'app_doctor');

                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($dataField['ezf_field_type'], Yii::$app->session['ezf_input']);
                }

                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $dataField, $model);
            }
            ?>
        </span>   
      </div>
      <div class="col-md-6 sdbox-col">
        <strong><?= Yii::t('patient', 'Inspect') ?> :</strong>
        <span class="text-info">
            <?php
            if (isset($model['id'])) {
                $dataField = PatientFunc::getRefTableName($modelFields, 'app_insp_id');

                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($dataField['ezf_field_type'], Yii::$app->session['ezf_input']);
                }

                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $dataField, $model);
            }
            ?>
        </span>
      </div>
      <hr>
      <div class="col-md-6">
        <strong><?= Yii::t('patient', 'Date') ?> :</strong>
        <span class="text-info">
            <?php
            if (isset($model['id'])) {
                echo SDdate::mysql2phpThDateSmall($model['app_date']);
            }
            ?>
        </span>
      </div>
      <div class="col-md-6 sdbox-col">
        <strong><?= Yii::t('patient', 'Department') ?> :</strong>
        <span class="text-info">
          <?php
          if (isset($model['id'])) {
              $dataField = PatientFunc::getRefTableName($modelFields, 'app_dept');

              if (isset(Yii::$app->session['ezf_input'])) {
                  $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($dataField['ezf_field_type'], Yii::$app->session['ezf_input']);
              }
              echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $dataField, $model);
          }
          ?>
        </span>
      </div>
    </div>              
  </div>
</div>