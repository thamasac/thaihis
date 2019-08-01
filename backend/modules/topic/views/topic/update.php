<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Topic */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Topic',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Topics'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topic-update">

    <?= $this->render('_form', [
        'model' => $model,
        'options'=>$options
    ]) ?>

</div>
