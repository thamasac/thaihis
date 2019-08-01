<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Ezform */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Ezform',
]) . ' ' . $model->ezf_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ezforms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ezf_id, 'url' => ['view', 'id' => $model->ezf_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ezform-update">

    <?= $this->render('_form', [
        'model' => $model, 'modelFields'=>$modelFields
    ]) ?>

</div>
