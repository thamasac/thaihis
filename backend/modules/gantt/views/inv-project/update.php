<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\gantt\models\InvProject */

$this->title = 'Update Inv Project: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inv Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inv-project-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
