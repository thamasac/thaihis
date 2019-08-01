<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\TablesFields */

$this->title = Yii::t('core', 'Update {modelClass}: ', [
    'modelClass' => 'Tables Fields',
]) . ' ' . $model->table_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Tables Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->table_id, 'url' => ['view', 'id' => $model->table_id]];
$this->params['breadcrumbs'][] = Yii::t('core', 'Update');
?>
<div class="tables-fields-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
