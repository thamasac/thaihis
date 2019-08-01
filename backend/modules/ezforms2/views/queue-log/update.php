<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\QueueLog */

$this->title = Yii::t('ezform', 'Update {modelClass}: ', [
    'modelClass' => 'Queue Log',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Queue Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="queue-log-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
