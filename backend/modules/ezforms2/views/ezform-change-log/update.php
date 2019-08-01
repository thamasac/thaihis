<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformChangeLog */

$this->title = Yii::t('ezform', 'Update {modelClass}: ', [
    'modelClass' => 'Ezform Change Log',
]) . ' ' . $model->log_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Change Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->log_id, 'url' => ['view', 'id' => $model->log_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-change-log-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
