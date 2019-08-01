<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\Ezmodule */

$this->title = Yii::t('ezmodule', 'Update {modelClass}: ', [
    'modelClass' => 'Ezmodule',
]) . ' ' . $model->ezm_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodule'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ezm_id, 'url' => ['view', 'id' => $model->ezm_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-update">

    <?= $this->render('_form', [
        'model' => $model,
        'tab' => $tab,
    ]) ?>

</div>
