<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreGenerate */

$this->title = Yii::t('core', 'Update {modelClass}: ', [
    'modelClass' => 'Core Generate',
]) . ' ' . $model->gen_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Generates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->gen_id, 'url' => ['view', 'id' => $model->gen_id]];
$this->params['breadcrumbs'][] = Yii::t('core', 'Update');
?>
<div class="core-generate-update">

    <?= $this->render('_form', [
        'model' => $model,
	'modelUi' => $modelUi,
    ]) ?>

</div>
