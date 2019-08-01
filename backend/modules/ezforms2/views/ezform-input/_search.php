<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformInputSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-input-search">

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

    <?= $form->field($model, 'input_id') ?>

    <?= $form->field($model, 'input_name') ?>

    <?= $form->field($model, 'input_class') ?>

    <?= $form->field($model, 'input_function') ?>

    <?= $form->field($model, 'input_class_validate') ?>

    <?php // echo $form->field($model, 'input_function_validate') ?>

    <?php // echo $form->field($model, 'input_specific') ?>

    <?php // echo $form->field($model, 'input_option') ?>

    <?php // echo $form->field($model, 'table_field_type') ?>

    <?php // echo $form->field($model, 'table_field_length') ?>

    <?php // echo $form->field($model, 'input_version') ?>

    <?php // echo $form->field($model, 'input_order') ?>

    <div class="form-group">
	<div class="col-sm-offset-2 col-sm-6">
	    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
