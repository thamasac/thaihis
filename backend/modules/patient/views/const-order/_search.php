<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\patient\models\ConstOrderSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="const-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
	'layout' => 'horizontal',
	'fieldConfig' => [
	    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
	    'horizontalCssClasses' => [
		'label' => 'col-sm-2',
		'offset' => 'col-sm-offset-3',
		'wrapper' => 'col-sm-6',
		'error' => '',
		'hint' => '',
	    ],
	],
    ]); ?>

    <?= $form->field($model, 'order_code') ?>

    <?= $form->field($model, 'order_name') ?>

    <?= $form->field($model, 'group_code') ?>

    <?= $form->field($model, 'group_type') ?>

    <?= $form->field($model, 'fin_item_code') ?>

    <?php // echo $form->field($model, 'sks_code') ?>

    <?php // echo $form->field($model, 'full_price') ?>

    <?php // echo $form->field($model, 'order_status') ?>

    <div class="form-group">
	<div class="col-sm-offset-2 col-sm-6">
	    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
