<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleWidget */

$this->title = Yii::t('ezmodule', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('ezmodule', 'Widget'),
]) . ' ' . $model->widget_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Widget'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->widget_id, 'url' => ['view', 'id' => $model->widget_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-widget-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modal' => $modal,
        'ezm_id' => $ezm_id,
    ]) ?>

</div>
