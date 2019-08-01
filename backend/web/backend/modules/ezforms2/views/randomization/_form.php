<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\RandomCode */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="random-code-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code_random')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'max_index')->textInput() ?>

    <?= $form->field($model, 'code_index')->textInput() ?>

    <?= $form->field($model, 'seed')->textInput() ?>

    <?= $form->field($model, 'treatment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'block_size')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'list_length')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
