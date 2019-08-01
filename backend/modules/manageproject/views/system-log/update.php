<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\manageproject\models\SystemLog */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'System Log',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'System Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-log-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
