<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\ProfileTcc */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Profile Tcc',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Tccs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="profile-tcc-update">

    <?= $this->render('_form', [
        'model' => $model,
        'status'=>'2'
    ]) ?>

</div>
