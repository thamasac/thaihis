<?php

use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientHelper;

$img = ($dataProfile['pt_pic'] ? Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataProfile['pt_pic'] : Yii::getAlias('@storageUrl/images') . '/nouser.png');
?>
<div class="row">
  <div class="col-md-12">
    <div class="media">
      <div class="media-left">
        <img src="<?= $img ?>" alt="Demo Avatar John Doe" class="media-object" style="width:100px;">
      </div>
      <div class="media-body">
        <!--<h4 class="media-heading"> -->
          <div>
            <strong>HN : </strong> <span class="text-info"><?= $dataProfile['pt_hn'] ?> </span>
            <strong><?= Yii::t('patient', 'Name') ?> : </strong> <span class="text-info"><?= $dataProfile['fullname']; ?> </span>
          </div>
          <div style="margin-top:5px;">
            <strong><?= Yii::t('patient', 'Citizen ID') ?> : </strong> <span class="text-info"><?= $dataProfile['pt_cid'] ?> </span>
            <strong><?= Yii::t('patient', 'Birthday') ?> : </strong> <span class="text-info"><?= SDdate::mysql2phpThDateSmall(SDdate::dateTh2bod($dataProfile['pt_bdate'])); ?> </span>
            <strong><?= Yii::t('patient', 'Age') ?> : </strong> <span class="text-info"><?= SDdate::getAge(SDdate::dateTh2bod($dataProfile['pt_bdate'])) . ' ปี'; ?> </span>
          </div>
          <div style="margin-top:5px;">
            <strong><?= Yii::t('patient', 'Right') ?> : </strong> <span class="text-info"><?php
                if (isset($dataRight['right_code'])) {
                    $modelFieldsPtRight = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_code', ':ezf_id' => $ezfPtRight_id])->all();
                }
                echo (isset($dataRight['right_code']) ? PatientFunc::getRefTableName($modelFieldsPtRight, 'right_code', $dataRight['right_code']) : '');

                $url = yii\helpers\Url::to(['/patient/order/right-detail', 'dataid' => $dataRight['right_id'],]);
                echo ' ' . yii\bootstrap\Html::button('<i class="fa fa-vcard"></i>', ['class' => 'btn btn-sm btn-info ezform-main-open', 'data-modal' => 'modal-md-profile', 'data-url' => $url,]);
                ?></span>
          </div>
          <div style="margin-top:5px;">
            <strong><?= Yii::t('patient', 'Illness information') ?> : </strong> <span class="text-info"><?php
              if ($dataProfile['pt_ht_id']) {
                  if ($dataProfile['pt_disease_status'] == '2') {
                      echo isset($dataProfile['pt_disease_detail']) ? 'มีโรคประจำตัว ' . $dataProfile['pt_disease_detail'] : '';
                  }
                  echo ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezfPtHt_id)->label('<i class="glyphicon glyphicon-eye-open"></i>')
                          ->options(['class' => 'btn btn-info btn-sm '])->buildBtnView($dataProfile['pt_ht_id']);
              } else {
                  echo 'ไม่พบข้อมูลเจ็บป่วย';
              }
              ?></span>
          </div>
        <!--</h4>-->
      </div>
    </div>
  </div>
</div>
<?php
echo appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-md-profile',
    'size' => 'modal-md',
]);
?>