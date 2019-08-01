<?php

use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezbuilder\classes\EzBuilderFunc;
use yii\helpers\Url;

$url = Url::to([
            '/ezforms2/ezform-fields-lib/add-lib-input',
            'ezf_id' => $ezf_id,
            'lib_id' => $model['field_lib_id'],
            'v' => $v,
            'show' => isset(Yii::$app->session['show_varname']) ? Yii::$app->session['show_varname'] : 0
        ]);
?>
<div class="list-group-item bgform-area"> 
  <button class="btn btn-sm btn-success pull-right btn-add-input-lib hidden" data-id="<?= $model['ezf_field_type'] ?>" data-url="<?= $url ?>"> <i class="glyphicon glyphicon-plus"></i></button>
  <h4 class="list-group-item-heading">
      <?php
      $txtName = $model['field_lib_name'];
      if ($model['lib_group_name']) {
          $txtName = $model['field_lib_name'] . " ({$model['lib_group_name']})";
      }
      echo $txtName;
      ?>
  </h4> 
  <div class="list-group-item-text" style="margin-bottom: 5px">
    <div>
      <?= Yii::t('ezform', 'Form') ?> : <strong class="text-info"><?= $model['ezf_name'] . ' '; ?></strong>
      <?= Yii::t('ezform', 'Question') ?> : <strong class="text-success"><?= $model['ezf_field_name']; ?></strong>
    </div>
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