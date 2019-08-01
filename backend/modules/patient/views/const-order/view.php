<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\patient\models\ConstOrder */

$this->title = 'Const Order#'.$model->order_code;
$this->params['breadcrumbs'][] = ['label' => 'Const Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="const-order-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'order_code',
		'order_name',
		'group_code',
		'group_type',
		'fin_item_code',
		'sks_code',
		'full_price',
		'order_status',
	    ],
	]) ?>
    </div>
</div>
