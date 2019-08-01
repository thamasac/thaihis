<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleFilter */

$this->title = Yii::t('ezmodule', 'Update {modelClass}: ', [
    'modelClass' => 'Ezmodule Filter',
]) . ' ' . $model->filter_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodule Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->filter_id, 'url' => ['view', 'id' => $model->filter_id]];
$this->params['breadcrumbs'][] = Yii::t('ezmodule', 'Update');
?>
<div class="ezmodule-filter-update">

    <?= $this->render('_form', [
        'model' => $model,
        'user_module'=>$user_module,
                    'userId'=>$userId,
        'ezf_id'=>$ezf_id,
    ]) ?>

</div>
