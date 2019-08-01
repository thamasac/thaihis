<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformCommunity */

$this->title = Yii::t('ezform', 'Update {modelClass}: ', [
    'modelClass' => 'Ezform Community',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Communities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-community-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
