<?php

use backend\modules\patient\classes\PatientHelper;
use yii\helpers\Html;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
        <i class="fa fa-user-md"></i> <?= Yii::t('patient', 'Diagnosis') ?> 
      </div>
      <div class="col-md-6 text-right">
          <?php
          if (!$btnDisabled || Yii::$app->user->identity->profile->attributes['position'] == '2') {
              echo '<label>' . (isset($model['create_date']) ? appxq\sdii\utils\SDdate::mysql2phpThDateTime($model['create_date']) : '') . '</label>';
              echo (isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm') : PatientHelper::btnAddTxt('', $ezf_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm'));
          }
          ?>
      </div>
    </div>
  </div>
  <div class="card-block">
    <div class="row">
      <div class="col-md-12">
        <strong>PDx :</strong>
        <?= (isset($model['id']) ? $model['di_txt'] : ''); ?>        
      </div>
      <div class="col-md-12">
        <strong>ICD-10 : </strong>
        <?= (isset($model['di_icd10']) ? \backend\modules\patient\classes\PatientQuery::getIcd10Fulltxt($model['di_icd10']) : ''); ?>
        <?= (isset($model['di_icd10_2']) ? '<div>' . \backend\modules\patient\classes\PatientQuery::getIcd10Fulltxt($model['di_icd10_2'] . '</div>') : ''); ?>
        <?= (isset($model['di_icd10_3']) ? '<div>' . \backend\modules\patient\classes\PatientQuery::getIcd10Fulltxt($model['di_icd10_3']) . '</div>' : ''); ?>
        <?= (isset($model['di_icd10_4']) ? '<div>' . \backend\modules\patient\classes\PatientQuery::getIcd10Fulltxt($model['di_icd10_4']) . '</div>' : ''); ?>
        <?= (isset($model['di_icd10_5']) ? '<div>' . \backend\modules\patient\classes\PatientQuery::getIcd10Fulltxt($model['di_icd10_5']) . '</div>' : ''); ?>
      </div>
      <?php
      if ($btnDisabled) {
          echo PatientHelper::uiEmrDoctor($target, 'view-doctor-treat-cpoe', '');
      }
      ?>
    </div>
  </div> 
</div>
<?php if (isset($model['id'])) { //if (isset($model['id'])) {?>
    <div class="card card-cpoe">
      <div class="card-block">  
        <div class="row">
          <div class="col-md-12">
            <table class="table" style="margin-bottom: 0px"> 
              <tbody> 
                <tr class="h4">
                  <td>
                    <div class="pull-right">
                        <?php
                        if (!$btnDisabled) {
                            echo (isset($modelStag['id']) ? PatientHelper::btnEditTxt('', $ezfStag_id, $modelStag['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm') : PatientHelper::btnAddTxt('', $ezfStag_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm'));
                        }
                        ?>
                    </div>

                    <?php
                    if (isset($modelStag['id'])) {
                        $modelFields = backend\modules\ezforms2\models\EzformFields::find()
                                        ->where('ezf_id = :ezf_id', [':ezf_id' => $ezfStag_id])
                                        ->orderBy(['ezf_field_order' => SORT_ASC])->all();

                        $txtStaging = str_replace('Stage ', '', \backend\modules\patient\classes\PatientFunc::getRefTableName($modelFields, 'staging_name_id', $modelStag['staging_name_id']));
                        ?>
                        <strong>Staging</strong> <span class="label label-warning" style="font-size: 100%;"><?= $txtStaging ?></span> 
                        <strong>T</strong> <span class="label label-primary" style="font-size: 100%;"><?= substr($modelStag['staging_tnm'], 0, 1) ?></span>
                        <strong>N</strong> <span class="label label-primary" style="font-size: 100%;"><?= substr($modelStag['staging_tnm'], 1, 1) ?></span>
                        <strong>M</strong> <span class="label label-primary" style="font-size: 100%;"><?= substr($modelStag['staging_tnm'], 2, 1) ?></span>
                    <?php } else { ?>
                        <strong>Staging</strong> <span class="label label-warning" style="font-size: 100%;"> </span> 
                        <strong>T</strong> <span class="label label-primary" style="font-size: 100%;"> </span>
                        <strong>N</strong> <span class="label label-primary" style="font-size: 100%;"> </span>
                        <strong>M</strong> <span class="label label-primary" style="font-size: 100%;"> </span>
                    <?php } ?>
                  </td>
                </tr>
                <tr>
                  <td> 
                    <div class="pull-right">
                        <?php
                        if (!$btnDisabled) {
                            echo PatientHelper::btnAddTxt('', $ezfComo_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm');
                            if ($dataDiagComo) {
                                echo ' ' . PatientHelper::btnViewTxt('', $ezfComo_id, $target, ['di_como_icd10'], 'modal-ezform-main', 'btn-sm');
                            }
                        }
                        ?>
                    </div>
                    <strong>Comorbidity : </strong>
                    <span class="text-info" style="vertical-align: text-top;">
                        <?php
                        foreach ($dataDiagComo as $value) {
                            echo '<code style="font-size:100%;">' . $value['di_icd10_code'] . '</code> : ' . $value['di_icd10_name'] . '<br>';
                        }
                        ?>
                    </span>
                  </td>
                </tr>  
                <tr>
                  <td> 
                    <div class="pull-right">
                        <?php
                        if (!$btnDisabled) {
                            echo PatientHelper::btnAddTxt('', $ezfComp_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm');
                            if ($dataDiagComp) {
                                echo ' ' . PatientHelper::btnViewTxt('', $ezfComp_id, $target, ['di_comp_icd10'], 'modal-ezform-main', 'btn-sm');
                            }
                        }
                        ?>
                    </div>
                    <strong>Complication : </strong>
                    <span class="text-info" style="vertical-align: text-top;">
                        <?php
                        foreach ($dataDiagComp as $value) {
                            echo '<code style="font-size:100%;">' . $value['di_icd10_code'] . '</code> : ' . $value['di_icd10_name'] . '<br>';
                        }
                        ?>
                    </span>
                  </td>
                </tr>  
                <tr>
                  <td>
                    <div class="pull-right">
                        <?php
                        if (!$btnDisabled) {
                            echo PatientHelper::btnAddTxt('', $ezfOperat_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm');
                            if ($dataOperat) {
                                echo ' ' . PatientHelper::btnViewTxt('', $ezfOperat_id, $target, ['di_operat_icd9'], 'modal-di-xxl', 'btn-sm');
                            }
                        }
                        ?>
                    </div>
                    <strong>Operation : </strong>
                    <span class="text-info" style="vertical-align: text-top;">
                      <?php
                      foreach ($dataOperat as $value) {
                          echo '<code style="font-size:100%;">' . $value['di_icd9_code'] . '</code> : ' . $value['di_icd9_name'] . '<br>';
                      }
                      ?>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>  
      </div>
    </div>
    <?php
    echo \appxq\sdii\widgets\ModalForm::widget([
        'id' => 'modal-di-xxl',
        'size' => 'modal-xxl',
        'tabindexEnable' => false,
    ]);

    $this->registerJS("
            $('#modal-di-xxl').on('hidden.bs.modal', function (e) {
            var url = $('#$reloadDiv').attr('data-url');
                if(url){
                    getUiAjax(url, '$reloadDiv');
                }            
            });
           ");
}
?>