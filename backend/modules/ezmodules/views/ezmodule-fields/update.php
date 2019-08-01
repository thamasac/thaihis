<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleFields */

$this->title = Yii::t('ezmodule', 'Update {modelClass}: ', [
    'modelClass' => 'Ezmodule Fields',
]) . ' ' . $model->field_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodule Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->field_id, 'url' => ['view', 'id' => $model->field_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-fields-update">

    <?= $this->render('_form', [
        'model' => $model,
        'inform'=>$inform,
        'reloadDiv'=>$reloadDiv,
    ]) ?>

</div>
