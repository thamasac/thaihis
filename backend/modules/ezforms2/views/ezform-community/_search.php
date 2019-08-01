<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformCommunitySearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-community-search">

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

    <?= $form->field($model, 'parent_id') ?>

    <?= $form->field($model, 'send_to') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'object_id') ?>

    <?php // echo $form->field($model, 'dataid') ?>

    <?php // echo $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'query_tool') ?>

    <?php // echo $form->field($model, 'field') ?>

    <?php // echo $form->field($model, 'value_old') ?>

    <?php // echo $form->field($model, 'value_new') ?>

    <?php // echo $form->field($model, 'approv_by') ?>

    <?php // echo $form->field($model, 'approv_date') ?>

    <?php // echo $form->field($model, 'approv_status') ?>

    <?php // echo $form->field($model, 'status') ?>

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
