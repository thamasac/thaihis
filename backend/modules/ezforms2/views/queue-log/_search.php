<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\QueueLogSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="queue-log-search">

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

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'unit') ?>

    <?= $form->field($model, 'ezf_id') ?>

    <?= $form->field($model, 'dataid') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'enable') ?>

    <?php // echo $form->field($model, 'setting_id') ?>

    <?php // echo $form->field($model, 'module_id') ?>

    <?php // echo $form->field($model, 'current_unit') ?>

    <?php // echo $form->field($model, 'user_receive') ?>

    <?php // echo $form->field($model, 'time_receive') ?>

    <?php // echo $form->field($model, 'options') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
	<div class="col-sm-offset-2 col-sm-6">
	    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
