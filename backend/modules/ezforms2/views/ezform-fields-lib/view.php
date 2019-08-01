<?php

use yii\helpers\Html;
use backend\modules\ezbuilder\classes\EzBuilderFunc;
use backend\modules\ezforms2\classes\EzfFunc;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezfieldlib\models\EzformFieldsLib */

$this->title = 'View Question Library';
//$this->params['breadcrumbs'][] = ['label' => 'Ezform Fields Libs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-fields-lib-view">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
  </div>
  <div class="modal-body">
    <div class="row">
      <?php
      $modelEzform = \backend\modules\ezforms2\models\Ezform::findOne($model->ezf_id);
      $modelVersion = \backend\modules\ezforms2\classes\EzfQuery::getEzformConfig($modelEzform->ezf_id, $modelEzform->ezf_version);
      $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_id = :ezf_field_id', [':ezf_field_id' => $model->ezf_field_id, ':ezf_id' => $model->ezf_id])->all();

      if (isset($modelFields)):
          foreach ($modelFields as $key => $value):
              $dataInput;
              if (isset(Yii::$app->session['ezf_input'])) {
                  $dataInput = EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
              }
              if ($dataInput) {
                  if (isset($dataInput['input_category']) && $dataInput['input_category'] == 0 && Yii::$app->user->can('administrator')) {
                      continue;
                  } else {
                      $inputWidget = Yii::createObject($dataInput['system_class']);
                      try {
                          $htmlInput = $inputWidget->generateViewInput($value);
                      } catch (yii\base\Exception $e) {
                          $htmlInput = '<code>' . $e->getMessage() . '</code>';
                      }

                      echo EzBuilderFunc::createChildrenItem($value, $htmlInput, $modelEzform, $modelVersion);
                  }
              }
          endforeach;
      endif;
      ?>
    </div>
  </div>
</div>

<?php
$this->registerJs("
    $('.view-item .button-item').addClass('hide');
    ");
?>