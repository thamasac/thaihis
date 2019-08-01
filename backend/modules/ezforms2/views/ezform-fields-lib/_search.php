<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezfieldlib\models\EzformFieldsLibSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-fields-lib-search">

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

    <?= $form->field($model, 'field_lib_id') ?>

    <?= $form->field($model, 'ezf_field_id') ?>

    <?= $form->field($model, 'ezf_id') ?>

    <?= $form->field($model, 'ezf_version') ?>

    <?= $form->field($model, 'field_lib_group') ?>

    <?php // echo $form->field($model, 'field_lib_name') ?>

    <?php // echo $form->field($model, 'field_lib_share') ?>

    <?php // echo $form->field($model, 'field_lib_status') ?>

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
