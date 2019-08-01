<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformVersionSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-version-search">

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

    <?= $form->field($model, 'ver_code') ?>

    <?= $form->field($model, 'ver_for') ?>

    <?= $form->field($model, 'ver_approved') ?>

    <?= $form->field($model, 'ver_active') ?>

    <?= $form->field($model, 'approved_by') ?>

    <?php // echo $form->field($model, 'approved_date') ?>

    <?php // echo $form->field($model, 'ver_options') ?>

    <?php // echo $form->field($model, 'ezf_id') ?>

    <?php // echo $form->field($model, 'field_detail') ?>

    <?php // echo $form->field($model, 'ezf_sql') ?>

    <?php // echo $form->field($model, 'ezf_js') ?>

    <?php // echo $form->field($model, 'ezf_error') ?>

    <?php // echo $form->field($model, 'ezf_options') ?>

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
