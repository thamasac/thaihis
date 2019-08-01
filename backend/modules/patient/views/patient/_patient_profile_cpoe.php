<?php

use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientHelper;

//$img = ($dataProfile['pt_pic'] ? Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataProfile['pt_pic'] : Yii::getAlias('@storageUrl/images') . '/nouser.png');
?>
<div class="row">
  <table class="table" style="margin-bottom: 0px"> 
    <tbody>
      <tr>
        <td class="col-md-3 text-right"><strong><?= Yii::t('patient', 'Citizen ID') ?> : </strong></td>
        <td class="col-md-4 text-info"><?= $dataProfile['pt_cid'] ?></td>

        <td class="col-md-2 text-right"><strong>HN : </strong></td>
        <td class="col-md-3 text-info"><?= $dataProfile['pt_hn'] ?> 
            <?php
            if ($user['position'] <> '2') {
                $url = 'http://118.175.29.182:8082/chemo/module/admin/_changeHN.php?hn=' . $dataProfile['pt_hn'];
                echo ' ' . yii\bootstrap\Html::button('<i class="glyphicon glyphicon-pencil"></i>', ['class' => 'btn btn-sm btn-info ezform-main-open', 'data-modal' => 'modal-ezform-main', 'data-url' => $url,]);
            }
            ?>
        </td>
      </tr>  
      <tr>
        <td class="text-right"><strong><?= Yii::t('patient', 'Name') ?> : </strong></td>
        <td class="text-info"><?= $dataProfile['fullname']; ?></td>

        <td class="text-right"><strong><?= Yii::t('patient', 'Birthday') ?> : </strong></td>
        <td class="text-info"><?php
            if ($dataProfile['pt_bdate']) {
                try {
                    echo SDdate::mysql2phpThDateSmall(SDdate::dateTh2bod($dataProfile['pt_bdate']));
                } catch (Exception $ex) {
                    $bdate = PatientFunc::integeter2date($dataProfile['pt_bdate']);
                    echo SDdate::mysql2phpThDateSmall(SDdate::dateTh2bod($bdate));
                }
            }
            ?></td>
      </tr>
      <tr>
        <td class="text-right"><strong><?= Yii::t('patient', 'Right') ?> : </strong></td>
        <td class="text-info  <?= ($dataRight['right_status'] == '5' ? 'danger' : ''); ?>">
            <?php
            if (isset($dataRight['right_code'])) {
                $modelFieldsPtRight = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_code', ':ezf_id' => $ezfPtRight_id])->all();

                echo (isset($dataRight['right_code']) ? PatientFunc::getRefTableName($modelFieldsPtRight, 'right_code', $dataRight['right_code']) : '');
                if ($dataRight['right_prove_no'] == '' && $dataRight['right_code'] == 'LGO') {
                    echo '<br><strong class="bg-danger text-warning">ไม่พบเลขอนุมัติ กรุณาติดต่อตรวจสอบสิทธิ </strong>';
                }
            }
            $url = yii\helpers\Url::to(['/patient/order/right-detail', 'dataid' => $dataRight['right_id'],]);
            echo ' ' . yii\bootstrap\Html::button('<i class="fa fa-vcard"></i>', ['class' => 'btn btn-sm btn-info ezform-main-open', 'data-modal' => 'modal-md-profile', 'data-url' => $url,]);
            ?>
        </td>

        <td class="text-right"><strong><?= Yii::t('patient', 'Age') ?> : </strong></td>
        <td class="text-info"><?php
            if ($dataProfile['pt_bdate']) {
                try {
                    echo SDdate::getAge(SDdate::dateTh2bod($dataProfile['pt_bdate'])) . ' ปี';
                } catch (Exception $ex) {
                    $bdate = PatientFunc::integeter2date($dataProfile['pt_bdate']);
                    echo SDdate::getAge(SDdate::dateTh2bod($bdate)) . ' ปี';
                }
            }
            ?></td>
      </tr>   
      <tr>
        <td class="text-right"><strong><?= Yii::t('patient', 'Illness information') ?> : </strong></td>
        <td colspan="2">
            <?php
            if ($dataProfile['pt_ht_id']) {
                if ($dataProfile['pt_disease_status'] == '2') {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'pt_disease_detail', ':ezf_id' => $ezfPtHt_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }
                    $pt_disease = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $dataProfile);
                    echo isset($dataProfile['pt_disease_detail']) ? 'โรคประจำตัว ' . $pt_disease : '';
                }
                if ($dataProfile['pt_drug_status'] == '2') {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'pt_drug_list', ':ezf_id' => $ezfPtHt_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }
                    $pt_disease = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $dataProfile);
                    if (isset($dataProfile['pt_drug_list'])) {
                        echo ' <span class="bg-danger text-danger h4">แพ้ยา ' . $pt_disease . '</span>';
                    }
                }
                echo ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezfPtHt_id)->label('<i class="glyphicon glyphicon-pencil"></i>')
                        ->reloadDiv('view-patient-cpoe')
                        ->options(['class' => 'btn btn-primary btn-sm '])->buildBtnEdit($dataProfile['pt_ht_id']);

//                echo ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezfPtHt_id)->label('<i class="glyphicon glyphicon-eye-open"></i>')
//                        ->options(['class' => 'btn btn-info btn-sm '])->buildBtnView($dataProfile['pt_ht_id']);
            } else {
                echo backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezfPtHt_id)->label('<i class="glyphicon glyphicon-plus"></i>')
                        ->target($dataProfile['pt_id'])
                        ->reloadDiv('view-patient-cpoe')
                        ->options(['class' => 'btn btn-success btn-sm '])->buildBtnAdd();
            }
            ?>
        </td>
        <td><strong>เตือน : </strong>
            <?php
            $ezfWarning_id = \backend\modules\patient\Module::$formID['warning'];
            if ($dataWarning) :
                $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'wn_level', ':ezf_id' => $ezfWarning_id])->one();
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                }
                $dataWarningLevel = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $dataWarning);

                switch ($dataWarning['wn_level']) {
                    case '1':
                        $btnClass = 'primary';
                        break;
                    case '2':
                        $btnClass = 'warning';
                        break;
                    case '3':
                        $btnClass = 'error';
                        break;
                    default:
                        $btnClass = 'success';
                        break;
                }
                \richardfan\widget\JSRegister::begin([
                    //'key' => 'bootstrap-modal',
                    'position' => \yii\web\View::POS_READY
                ]);
                if ($dataWarning['wn_txt']) {
                    ?>
                  <script>
                      var noty_id = noty({"text": '<?= $dataWarning['wn_txt'] ?>', "type": '<?= $btnClass ?>',
                        "buttons": [{type: "btn btn-default", text: "Ok", click: function ($noty) {
                              $noty.close();
                            }
                          },
                        ], "timeout": false});
                  </script>
                  <?php
                  \richardfan\widget\JSRegister::end();
              }
              $btnClass = ($btnClass == "error" ? 'danger' : $btnClass);
              echo ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezfWarning_id)->label('<i class="fa fa-exclamation-triangle"></i>')
                      ->reloadDiv('view-patient-cpoe')
                      ->options(['class' => 'btn btn-' . $btnClass . ' btn-sm '])->buildBtnEdit($dataWarning['id']);
          else :
              echo backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezfWarning_id)->label('<i class="fa fa-exclamation-triangle"></i>')
                      ->target($dataProfile['pt_id'])->reloadDiv('view-patient-cpoe')
                      ->options(['class' => 'btn btn-success btn-sm '])->buildBtnAdd();
          endif;
          ?>
        </td>
      </tr>
      <?php
      if ($dataProfile['pt_vip']) :
          ?>
          <tr>
            <td class="text-right"><strong><?= Yii::t('patient', 'Special') ?> : </strong></td>
            <td colspan="3">
                <?php
                $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'pt_vip', ':ezf_id' => $ezf_id])->one();
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                }
                $datavip = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $dataProfile);
                echo ' <span class="bg-warning text-warning h4"> ' . $datavip . '</span>'
                . "<script>var noty_id = noty({'text': '{$datavip}', 'type': 'success'});</script>";
                ?>
            </td>
          </tr>
          <?php
      endif;
      ?>     
    </tbody> 
  </table>
</div>
<?php
echo appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-md-profile',
    'size' => 'modal-md',
]);

$btn = $btnDisabled == FALSE ? PatientHelper::btnEditTxt('', $ezf_id, $dataProfile['pt_id'], '', 'view-patient-cpoe', 'modal-ezform-main', 'btn-sm') : backend\modules\ezforms2\classes\BtnBuilder::btn()
                ->ezf_id($ezf_id)
                ->label('<i class="fa fa-eye"></i>')->options(['class' => 'btn btn-info'])
                ->buildBtnView($dataProfile['pt_id']);

try {
    $visitTime = '<label>' . (isset($dataVisit['visit_date']) ? SDdate::mysql2phpThDateTime($dataVisit['visit_date']) : '') . '</label>';
} catch (Exception $exc) {
    $visitTime = '';
}

$this->registerJS("
    $('#modal-md-profile .modal-content').attr('data-url','$url');
    $('#btn-right').html('$visitTime  $btn');
    var url = $('#$reloadDiv').attr('data-url');
    $('#modal-md-profile').on('hidden.bs.modal', function (e) {
        getUiAjax(url, '$reloadDiv');
    });
    ");
?>