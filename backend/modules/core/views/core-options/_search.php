<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreOptionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="core-options-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'option_id') ?>

    <?= $form->field($model, 'option_name') ?>

    <?= $form->field($model, 'option_value') ?>

    <?= $form->field($model, 'autoload') ?>

    <?= $form->field($model, 'input_label') ?>

    <?php // echo $form->field($model, 'input_hint') ?>

    <?php // echo $form->field($model, 'input_field') ?>

    <?php // echo $form->field($model, 'input_data') ?>

    <?php // echo $form->field($model, 'input_required') ?>

    <?php // echo $form->field($model, 'input_validate') ?>

    <?php // echo $form->field($model, 'input_meta') ?>

    <?php // echo $form->field($model, 'input_order') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
