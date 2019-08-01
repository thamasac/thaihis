<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformVersion */

$this->title = Yii::t('ezform', 'Update {modelClass}: ', [
    'modelClass' => 'Ezform Version',
]) . ' ' . $model->ver_code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Versions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ver_code, 'url' => ['view', 'ver_code' => $model->ver_code, 'ezf_id' => $model->ezf_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-version-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
