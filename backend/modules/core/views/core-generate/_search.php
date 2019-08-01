<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreGenerateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="core-generate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'gen_id') ?>

    <?= $form->field($model, 'gen_group') ?>

    <?= $form->field($model, 'gen_name') ?>

    <?= $form->field($model, 'gen_tag') ?>

    <?= $form->field($model, 'gen_link') ?>

    <?php // echo $form->field($model, 'gen_process') ?>

    <?php // echo $form->field($model, 'gen_ui') ?>

    <?php // echo $form->field($model, 'template_php') ?>

    <?php // echo $form->field($model, 'template_html') ?>

    <?php // echo $form->field($model, 'template_js') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
