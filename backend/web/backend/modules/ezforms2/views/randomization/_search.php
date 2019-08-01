<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\RandomCodeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="random-code-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'code_random') ?>

    <?= $form->field($model, 'max_index') ?>

    <?= $form->field($model, 'code_index') ?>

    <?php // echo $form->field($model, 'seed') ?>

    <?php // echo $form->field($model, 'treatment') ?>

    <?php // echo $form->field($model, 'block_size') ?>

    <?php // echo $form->field($model, 'list_length') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
