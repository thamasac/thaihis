<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreOptions */

$this->title = Yii::t('core', 'Update {modelClass}: ', [
    'modelClass' => 'Core Options',
]) . ' ' . $model->option_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Options'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->option_id, 'url' => ['view', 'id' => $model->option_id]];
$this->params['breadcrumbs'][] = Yii::t('core', 'Update');
?>
<div class="core-options-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
