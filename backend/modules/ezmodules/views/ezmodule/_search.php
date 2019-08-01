<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-search">

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

    <?= $form->field($model, 'ezm_id') ?>

    <?= $form->field($model, 'ezm_name') ?>

    <?= $form->field($model, 'ezm_detail') ?>

    <?= $form->field($model, 'ezm_type') ?>

    <?= $form->field($model, 'ezm_system') ?>

    <?php // echo $form->field($model, 'ezm_devby') ?>

    <?php // echo $form->field($model, 'ezm_link') ?>

    <?php // echo $form->field($model, 'ezm_tag') ?>

    <?php // echo $form->field($model, 'ezm_icon') ?>

    <?php // echo $form->field($model, 'icon_base_url') ?>

    <?php // echo $form->field($model, 'template_id') ?>

    <?php // echo $form->field($model, 'ezm_js') ?>

    <?php // echo $form->field($model, 'ezf_id') ?>

    <?php // echo $form->field($model, 'sitecode') ?>

    <?php // echo $form->field($model, 'ezm_builder') ?>

    <?php // echo $form->field($model, 'public') ?>

    <?php // echo $form->field($model, 'approved') ?>

    <?php // echo $form->field($model, 'share') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'options') ?>

    <?php // echo $form->field($model, 'order_module') ?>

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
