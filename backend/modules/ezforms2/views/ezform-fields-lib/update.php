<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezfieldlib\models\EzformFieldsLib */

$this->title = 'Update Group Question Library';
//$this->params['breadcrumbs'][] = ['label' => 'Ezform Fields Libs', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->field_lib_id, 'url' => ['view', 'id' => $model->field_lib_id]];
$this->params['breadcrumbs'][] = $this->title;

$view = '_form';
if (isset($action)) {
    $view = '_form_group';
}
?>
<div class="ezform-fields-lib-update">

  <?=
  $this->render($view, [
      'model' => $model,
      'mode' => isset($mode) ? $mode : ''
  ])
  ?>

</div>
