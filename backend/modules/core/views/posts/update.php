<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CorePosts */

$this->title = Yii::t('core', 'Update {modelClass}: ', [
    'modelClass' => ucfirst($type),
]) . ' ' . $model->ID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ID, 'url' => ['view', 'id' => $model->ID]];
$this->params['breadcrumbs'][] = Yii::t('core', 'Update');
?>
<div class="posts-update">

    <?= $this->render('_form', [
        'model' => $model,
	'type'=>$type
    ]) ?>

</div>
