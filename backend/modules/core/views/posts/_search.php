<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CorePostsSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="core-posts-search">

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

    <?= $form->field($model, 'ID') ?>

    <?= $form->field($model, 'post_author') ?>

    <?= $form->field($model, 'post_date') ?>

    <?= $form->field($model, 'post_date_gmt') ?>

    <?= $form->field($model, 'post_content') ?>

    <?php // echo $form->field($model, 'post_title') ?>

    <?php // echo $form->field($model, 'post_excerpt') ?>

    <?php // echo $form->field($model, 'post_status') ?>

    <?php // echo $form->field($model, 'comment_status') ?>

    <?php // echo $form->field($model, 'ping_status') ?>

    <?php // echo $form->field($model, 'post_password') ?>

    <?php // echo $form->field($model, 'post_name') ?>

    <?php // echo $form->field($model, 'to_ping') ?>

    <?php // echo $form->field($model, 'pinged') ?>

    <?php // echo $form->field($model, 'post_modified') ?>

    <?php // echo $form->field($model, 'post_modified_gmt') ?>

    <?php // echo $form->field($model, 'post_content_filtered') ?>

    <?php // echo $form->field($model, 'post_parent') ?>

    <?php // echo $form->field($model, 'guid') ?>

    <?php // echo $form->field($model, 'menu_order') ?>

    <?php // echo $form->field($model, 'post_type') ?>

    <?php // echo $form->field($model, 'post_mime_type') ?>

    <?php // echo $form->field($model, 'comment_count') ?>

    <div class="form-group">
	<div class="col-sm-offset-2 col-sm-6">
	    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
