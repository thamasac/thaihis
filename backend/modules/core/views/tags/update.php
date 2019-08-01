<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreTerms */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Core Terms',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Core Terms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->term_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="tags-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
