<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformAutonum */

$this->title = Yii::t('ezform', 'Update {modelClass}: ', [
    'modelClass' => 'Ezform Autonum',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Autonums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('ezform', 'Update');
?>
<div class="ezform-autonum-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
