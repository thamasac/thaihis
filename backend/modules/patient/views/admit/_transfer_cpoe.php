<?php

use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\PatientHelper;
?>
<div class="row">
  <table class="table" style="margin-bottom: 0px"> 
    <tbody>
      <tr>
        <td class="col-md-3 text-right">ส่งต่อไปเพื่อ : </td>
        <td class="col-md-4"><label class="text-info">
                <?php
                if ($model['id']) {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'refer_type', ':ezf_id' => $ezf_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                }
                ?>
          </label></td>

        <td class="col-md-2 text-right">เดินทางโดย : </td>
        <td class="col-md-3"><label class="text-info">
                <?php
                if ($model['id']) {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'refer_by', ':ezf_id' => $ezf_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                }
                ?>
          </label></td>
      </tr>
      <tr>
        <td class="col-md-3 text-right">ประวัติการเจ็บป่วย : </td>
        <td class="col-md-4"><label class="text-info">
                <?php
                if ($model['id']) {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'refer_ph', ':ezf_id' => $ezf_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                }
                ?>
          </label></td>

        <td class="col-md-2 text-right">ผลการตรวจ : </td>
        <td class="col-md-3"><label class="text-info">
                <?php
                if ($model['id']) {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'refer_result', ':ezf_id' => $ezf_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                }
                ?>
          </label></td>
      </tr>
      <tr>
        <td class="col-md-3 text-right">การรักษาที่ได้ให้ไว้แล้ว : </td>
        <td class="col-md-4"><label class="text-info">
                <?php
                if ($model['id']) {
                    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'refer_treatment', ':ezf_id' => $ezf_id])->one();
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                }
                ?>
          </label></td>

        <td class="col-md-2 text-right">สาเหตุที่ส่ง : </td>
        <td class="col-md-3"><label class="text-info">
            <?php
            if ($model['id']) {
                $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'refer_cause', ':ezf_id' => $ezf_id])->one();
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                }

                echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
            }
            ?>
          </label></td>
      </tr>
    </tbody>
  </table>
</div>
<?php
if (isset($model['id'])) {
    $url = \yii\helpers\Url::to(['/patient/restful/print-tranfer', 'tranfer_id' => $model['id']]);
    $btn = PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm')
            . ' ' . yii\helpers\Html::a('<span class="fa fa-print"></span>', $url, ['class' => 'btn btn-warning btn-sm print-report-tranfer'
                , 'target' => '_blank', 'title' => 'Print']);
} else {
    $btn = PatientHelper::btnAddTxt('', $ezf_id, $visit_id, [], $reloadDiv, 'modal-ezform-main', 'btn-sm');
}

$this->registerJS("
$('#btn-right').html('$btn');
");
?>