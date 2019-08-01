<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                    $this
 * @var dektrium\user\models\ResendForm $model
 */

$this->title = Yii::t('user', 'If you have not recieved a verification email, you can resend the verification email.');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="">
            <div class="">
                <div class=""><?= Html::encode($this->title) ?></div>
            </div>
            <div class="">
                <?php $form = ActiveForm::begin([
                    'id'                     => 'resend-form',
                    'enableAjaxValidation'   => true,
                    'enableClientValidation' => false
                ]); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true])->label('Email') ?>

                <?= Html::submitButton(Yii::t('user', 'Continue'), ['class' => 'btn btn-primary btn-block']) ?><br>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
