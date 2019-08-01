<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\linebot\models\LineFunctions */

$this->title = Yii::t('linebot', 'Update {modelClass}: ', [
    'modelClass' => 'Line Functions',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('linebot', 'Line Functions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-functions-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
