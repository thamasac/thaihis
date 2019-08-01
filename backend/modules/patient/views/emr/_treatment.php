<?php

use backend\modules\patient\classes\PatientHelper;
use backend\modules\patient\classes\PatientFunc;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
        <i class="fa fa-med"></i> <?= Yii::t('patient', 'Treatment ') ?> 
      </div>
      <div class="col-md-6 text-right">
        <div>
            <?php
            echo '<label>' . (isset($model['create_date']) ? appxq\sdii\utils\SDdate::mysql2phpThDateTime($model['create_date']) : '') . '</label>';
            if (!$btnDisabled) {
                echo (isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm')
                        . ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezf_id)->label('<i class="fa fa-male"></i>')
                                ->options(['class' => 'btn btn-info btn-sm '])->buildBtnView($model['id']) : PatientHelper::btnAddTxt('', $ezf_id, $visit_id, [], $reloadDiv, 'modal-ezform-main', 'btn-sm'));
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
      <?php
      if (isset($model['id'])) {
          if ($model['treat_consult_check'] == '1' || $model['treat_send_check'] == '1') {
              ?>
            <div class="row">
              <div class="col-md-12">
                  <?php
                  if ($model['treat_consult_check'] == '1') {
                      echo ' <strong>' . PatientFunc::getLabelValue($ezf_id, 'treat_consult_check')['ezf_field_label'] . '</strong> : ' . PatientFunc::getInputValue($ezf_id, $model, 'treat_consult');
                  }
                  ?>
              </div>
              <div class="col-md-12">
                  <?php
                  if ($model['treat_send_check'] == '1') {
                      echo ' <strong>' . PatientFunc::getLabelValue($ezf_id, 'treat_send_check')['ezf_field_label'] . '</strong> : ' . PatientFunc::getInputValue($ezf_id, $model, 'treat_send_hosp');
                  }
                  ?>
              </div>
            </div>
            <hr>
            <?php
        }
        if ($model['treat_med_check'] == '1' || $model['treat_fu_check'] == '1') {
            ?>
            <div class="row">
              <div class="col-md-12">
                  <?php
                  if ($model['treat_med_check'] == '1') {
                      echo ' <strong>' . PatientFunc::getLabelValue($ezf_id, 'treat_med_check')['ezf_field_label'] . '</strong> ';
                  }
                  ?>
              </div>
              <div class="col-md-12">
                  <?php
                  if ($model['treat_fu_check'] == '1') {
                      switch ($model['treat_fu_time']) {
                          case '1':
                              $fu_time = $model['treat_fu_time_other_1'] . ' สัปดาห์';
                              break;
                          case '2':
                              $fu_time = $model['treat_fu_time_other_2'] . ' เดือน';
                              break;
                          case '3':
                              $fu_time = $model['treat_fu_time_other_3'] . ' ปี ';
                              break;
                          default:
                              $fu_time = '';
                              break;
                      }

                      echo ' <strong>' . PatientFunc::getLabelValue($ezf_id, 'treat_fu_check')['ezf_field_label'] . '</strong> : ' . $fu_time;
                  }
                  ?>
              </div>
            </div>
            <hr>
            <?php
        }
        if ($model['treat_advice_check'] == '1') {
            ?>
            <div class="row">
              <div class="col-md-12">
                  <?php
                  if ($model['treat_advice_check'] == '1') {
                      echo ' <strong>' . PatientFunc::getLabelValue($ezf_id, 'treat_advice_check')['ezf_field_label'] . '</strong> ';
                  }
                  ?>
              </div>
            </div>
            <hr>
        <?php } if ($model['treat_advicedoc_check'] == '1') {
            ?>
            <div class="row">
              <div class="col-md-12">
                  <?php
                  if ($model['treat_advicedoc_check'] == '1') {
                      echo ' <strong>' . PatientFunc::getLabelValue($ezf_id, 'treat_advicedoc_check')['ezf_field_label'] . '</strong> : ' . PatientFunc::getInputValue($ezf_id, $model, 'treat_advicedoc_txt');
                  }
                  ?>
              </div>
            </div>
            <hr>
            <?php
        } if ($model['treat_result_check'] == '1') {
            ?>
            <div class="row">
                <?php
                if ($model['treat_result_check'] == '1') {
                    $txt = ' <strong>' . PatientFunc::getLabelValue($ezf_id, 'treat_result_check')['ezf_field_label'] . '</strong> : ';
//                    $pe = ['treat_result_send_1', 'treat_result_send_2', 'treat_result_send_3', 'treat_result_send_4'];
//                    $i = 0;
                    if ($model['treat_result_send_1']) {
                        ?>
                      <div class="col-md-12">
                          <?= $txt . PatientFunc::getLabelValue($ezf_id, 'treat_result_send_1')['ezf_field_label']; ?>
                      </div>
                      <?php
                  }
                  if ($model['treat_result_send_2']) {
                      ?>
                      <div class="col-md-12">
                          <?= $txt . 'ผลอ่าน ' . PatientFunc::getInputValue($ezf_id, $model, 'treat_result_send_other_2'); ?>
                      </div>
                      <?php
                  }
                  if ($model['treat_result_send_3']) {
                      ?>
                      <div class="col-md-12">
                          <?= $txt . 'CD ' . PatientFunc::getInputValue($ezf_id, $model, 'treat_result_send_other_3'); ?>
                      </div>
                      <?php
                  }
                  if ($model['treat_result_send_4']) {
                      ?>
                      <div class="col-md-12">
                          <?= $txt . 'ผลตรวจ '  . PatientFunc::getInputValue($ezf_id, $model, 'treat_result_send_other_4'); ?>
                      </div>
                      <?php
                  }
              }
              ?>
            </div>
            <hr>
        <?php } ?>
        <div class="row ">
          <div class="col-md-12">
            <strong>Note : </strong>
            <?= (isset($model['id']) ? nl2br($model['treat_comment']) : ''); ?>
          </div>
        </div> 
    <?php } else {
        ?>
        <div class="row">
          <div class="col-md-12">
            <strong>Treatment</strong>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-12">
            <strong>Note : </strong>
          </div>
        </div>
    <?php }
    ?>
  </div>
</div>