<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformInput */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Ezform Input',
]) . ' ' . $model->input_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ezform Inputs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->input_id, 'url' => ['view', 'id' => $model->input_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ezform-input-update">

    <?= $this->render('_form', [
        'model' => $model,
        'content' => $content,
    ]) ?>

</div>
