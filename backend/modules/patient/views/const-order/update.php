<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\patient\models\ConstOrder */

$this->title = 'Update Const Order: ' . ' ' . $model->order_code;
$this->params['breadcrumbs'][] = ['label' => 'Const Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_code, 'url' => ['view', 'id' => $model->order_code]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="const-order-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
