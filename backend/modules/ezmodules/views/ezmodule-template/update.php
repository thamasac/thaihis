<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleTemplate */

$this->title = Yii::t('ezmodule', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('ezmodule', 'Template'),
]) . ' ' . $model->template_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Template'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->template_id, 'url' => ['view', 'id' => $model->template_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-template-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
