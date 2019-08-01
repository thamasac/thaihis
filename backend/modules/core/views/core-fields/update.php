<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreFields */

$this->title = Yii::t('core', 'Update {modelClass}: ', [
    'modelClass' => 'Core Fields',
]) . ' ' . $model->field_code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->field_code, 'url' => ['view', 'id' => $model->field_code]];
$this->params['breadcrumbs'][] = Yii::t('core', 'Update');
?>
<div class="core-fields-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
