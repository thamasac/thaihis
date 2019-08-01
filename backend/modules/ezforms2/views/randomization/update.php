<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\RandomCode */

$this->title = 'Update Random Code: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Random Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="random-code-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
