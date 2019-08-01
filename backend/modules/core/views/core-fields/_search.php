<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreFieldsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="core-fields-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'field_code') ?>

    <?= $form->field($model, 'field_internal') ?>

    <?= $form->field($model, 'field_class') ?>

    <?= $form->field($model, 'field_name') ?>

    <?= $form->field($model, 'field_meta') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
