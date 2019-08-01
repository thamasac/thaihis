
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  <h3 class="modal-title" id="itemModalLabel"><?= Yii::t('patient', 'Rights details') ?></h3>
</div>
<div class="modal-body h5">
  <div class="row">
    <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Right') ?> : </strong></div>
    <div class="col-md-8 sdbox-col">
        <?php
        $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_code', ':ezf_id' => $ezf_id])->one();
        if (isset(Yii::$app->session['ezf_input'])) {
            $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
        }
        echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
        ?>
    </div>
  </div>
  <?php if (in_array($model['right_code'], ['LGO', 'OFC'])) { ?>
      <hr>
      <div class="row">
        <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Approve No.') ?> : </strong></div>
        <div class="col-md-8 sdbox-col">
            <?= (isset($model['right_prove_no']) ? $model['right_prove_no'] : '') ?>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Expire') ?> : </strong></div>
        <div class="col-md-8 sdbox-col">
            <?= (isset($model['right_prove_end']) ? appxq\sdii\utils\SDdate::mysql2phpThDate($model['right_prove_end']) : ''); ?>
        </div>
      </div>
  <?php } elseif (in_array($model['right_code'], ['UCS', 'SSS'])) { ?>
      <hr>
      <div class="row">
        <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Right Hospital') ?> : </strong></div>
        <div class="col-md-8 sdbox-col">
            <?php
            $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_hos_main', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            ?>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Refer In Hospital') ?> : </strong></div>
        <div class="col-md-8 sdbox-col">
            <?php
            $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_hos_refer', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            ?>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Refer No.') ?> : </strong></div>
        <div class="col-md-8 sdbox-col">
            <?= (isset($model['right_refer_no']) ? $model['right_refer_no'] : '') ?>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Refer date') ?> : </strong></div>
        <div class="col-md-8 sdbox-col">
            <?= (isset($model['right_refer_start']) ? appxq\sdii\utils\SDdate::mysql2phpThDate($model['right_refer_start']) : ''); ?>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Refer Ending Day') ?> : </strong></div>
        <div class="col-md-8 sdbox-col">
            <?= (isset($model['right_refer_start']) ? appxq\sdii\utils\SDdate::mysql2phpThDate($model['right_refer_end']) : ''); ?>
        </div>
      </div>
  <?php } elseif (in_array($model['right_code'], ['PRO', 'ORI'])) { ?>
      <hr>
      <div class="row">
        <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Agency') ?> : </strong></div>
        <div class="col-md-8 sdbox-col">
            <?php
            $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_project_id', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            ?>
        </div>
      </div>
  <?php } ?>
  <hr>
  <div class="row">
    <div class="col-md-4 text-right"><strong><?= Yii::t('patient', 'Right Status') ?> : </strong></div>
    <div class="col-md-8 sdbox-col">
        <?php
        $modelFields = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_status', ':ezf_id' => $ezf_id])->one();
        if (isset(Yii::$app->session['ezf_input'])) {
            $dataInput = backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
        }
        echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
        ?>
    </div>
  </div>
</div>
<div class="modal-footer">    
    <?php
    echo backend\modules\ezforms2\classes\BtnBuilder::btn()
            ->ezf_id($ezf_id)
            ->initdata(['right_status' => '2'])
            ->reloadDiv('modal-md-profile .modal-content')
            //->label('<i class="glyphicon glyphicon-pencil"></i>')->options(['class' => 'btn btn-sm pull-right btn-danger'])
            ->buildBtnEdit($dataid);
    ?>
  <button type="button" class="btn btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> <?= Yii::t('app', 'Close') ?></button>    
</div>