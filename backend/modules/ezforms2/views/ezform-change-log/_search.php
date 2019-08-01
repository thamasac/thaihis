<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformChangeLogSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-change-log-search">

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

    <?= $form->field($model, 'log_id') ?>

    <?= $form->field($model, 'ezf_id') ?>

    <?= $form->field($model, 'ezf_field_id') ?>

    <?= $form->field($model, 'ezf_version') ?>

    <?= $form->field($model, 'log_type') ?>

    <?php // echo $form->field($model, 'log_event') ?>

    <?php // echo $form->field($model, 'log_count') ?>

    <?php // echo $form->field($model, 'log_ref_id') ?>

    <?php // echo $form->field($model, 'log_detail') ?>

    <?php // echo $form->field($model, 'log_ref_table') ?>

    <?php // echo $form->field($model, 'log_ref_version') ?>

    <?php // echo $form->field($model, 'log_ref_varname') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
	<div class="col-sm-offset-2 col-sm-6">
	    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
