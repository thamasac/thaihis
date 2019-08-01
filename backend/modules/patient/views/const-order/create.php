<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\patient\models\ConstOrder */

$this->title = 'Create Const Order';
$this->params['breadcrumbs'][] = ['label' => 'Const Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="const-order-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
