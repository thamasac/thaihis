<?php

use backend\modules\patient\classes\PatientHelper;
use yii\helpers\Html;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
        <i class="fa fa-universal-access"></i> <?= Yii::t('patient', 'Physical examination') ?> 
      </div>
      <div class="col-md-6 text-right">
        <div>
            <?php
            echo '<label>' . (isset($model['create_date']) ? appxq\sdii\utils\SDdate::mysql2phpThDateTime($model['create_date']) : '') . '</label>';
            if (!$btnDisabled) {
                echo (isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm')
                        . ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezf_id)->label('<i class="fa fa-male"></i>')
                                ->options(['class' => 'btn btn-info btn-sm '])->buildBtnView($model['id']) : PatientHelper::btnAddTxt('', $ezf_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm'));
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
      <div class="col-md-12">
          <?php
          if (isset($model['id'])) {
              if ($model['pe_n_all'] == '1') {
                  $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'pe_n_all', ':ezf_id' => $ezf_id])->one();
                  if (isset(Yii::$app->session['ezf_input'])) {
                      $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                  }
                  echo ' <strong>' . $modelFields['ezf_field_label'] . '</strong> : ' . \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model) . ' ';
              } elseif ($model['pe_n_all'] == '2') {
                  $pe = ['pe_head', 'pe_neck', 'pe_breast', 'pe_heart', 'pe_lung', 'pe_abdomen'];
                  foreach ($pe as $value) {
                      if ($model[$value] == '2') {
                          $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $value, ':ezf_id' => $ezf_id])->one();
                          if (isset(Yii::$app->session['ezf_input'])) {
                              $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                          }
                          echo '<div> <strong>' . $modelFields['ezf_field_label'] . '</strong> : ' . \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model) . '</div>';
                      }
                  }
              }
              $pe = ['pe_ga_1', 'pe_ga_2', 'pe_ga_3', 'pe_ga_5', 'pe_ga_6', 'pe_ga_7', 'pe_ga_8'];
              $i = 0;
              $endDiv = '';
              foreach ($pe as $value) {
                  if ($model[$value]) {
                      if ($i == 0) {
                          echo'<div><strong>General Appearance : </strong>';
                          $endDiv = '</div>';
                      }
                      $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $value, ':ezf_id' => $ezf_id])->one();

                      echo $modelFields['ezf_field_label'] . ' ,';
                  }
                  $i++;
              }
              echo $endDiv;
          } else {
              echo ' <strong>PE</strong> : ';
          }
          ?>
      </div>
    </div>
    <hr>
    <div class="row ">
      <div class="col-md-12">
        <strong>Note : </strong>
        <?= (isset($model['id']) ? $model['pe_note'] : ''); ?>
      </div>
    </div>       
  </div>
</div>