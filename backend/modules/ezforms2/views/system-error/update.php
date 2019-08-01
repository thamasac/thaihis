<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\SystemError */

$this->title = Yii::t('ezform', 'Update {modelClass}: ', [
    'modelClass' => 'System Error',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'System Errors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('ezform', 'Update');
?>
<div class="system-error-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
