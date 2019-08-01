<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleWidgetSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-widget-search">

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

    <?= $form->field($model, 'widget_id') ?>

    <?= $form->field($model, 'widget_name') ?>

    <?= $form->field($model, 'widget_varname') ?>

    <?= $form->field($model, 'widget_detail') ?>

    <?= $form->field($model, 'widget_example') ?>

    <?php // echo $form->field($model, 'options') ?>

    <?php // echo $form->field($model, 'enable') ?>

    <div class="form-group">
	<div class="col-sm-offset-2 col-sm-6">
	    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
