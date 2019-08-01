<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformFields */

$this->title = Yii::t('ezform', 'Update {modelClass}: ', [
    'modelClass' => 'Ezform Fields',
]) . ' ' . $model->ezf_field_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ezf_field_id, 'url' => ['view', 'ezf_field_id' => $model->ezf_field_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ezform-fields-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modal' => $modal,
        'dataEzf' => $dataEzf,
        'reloadDiv' => $reloadDiv,
    ]) ?>

</div>
