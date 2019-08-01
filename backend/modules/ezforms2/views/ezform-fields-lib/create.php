<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezfieldlib\models\EzformFieldsLib */

$this->title = 'Question Library';
//$this->params['breadcrumbs'][] = ['label' => 'Ezform Fields Library', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$view = '_form';
if (isset($action)) {
    $view = '_form_group';
}
?>
<div class="ezform-fields-lib-create">

  <?=
  $this->render($view, [
      'model' => $model,
      'mode' => ''
  ])
  ?>

</div>
