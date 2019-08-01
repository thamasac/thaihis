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
 * @var $this  yii\web\View
 * @var $form  yii\widgets\ActiveForm
 * @var $model dektrium\user\models\SettingsForm
 */

$this->title = Yii::t('user', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class='col-md-10 col-md-offset-1'>
        <?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
    </div>
</div>

<div class="container">
    <div class="col-md-3 sdbox-col">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class='fa fa-address-card'></i> <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                
                <?php $form = ActiveForm::begin([
                    'id'          => 'account-form',
                    'options'     => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label'],
                    ],
                    'enableAjaxValidation'   => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $form->field($model, 'email') ?>

                <div class="form-group field-settings-form-username required">
                    <label class="col-lg-3 control-label" for="settings-form-username">Username</label>
                    <div class="col-lg-9"><label style="margin-top:10px;"><?= $model->username; ?></label></div>
                    <div class="col-sm-offset-3 col-lg-9"><div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group field-settings-form-username required" style="margin-top: -25px;
    margin-bottom: -4px;">
                    <label class="col-lg-3 control-label" for="settings-form-username"></label>
                    <div class="col-lg-9">
                        <label style="margin-top:0px;">
                            *If the password has never been set, <a target="_BLANK" href="<?= yii\helpers\Url::to(['/user/recovery/request'])?>">CLICK HERE</a> to reset the password.
                        </label>
                    </div>
                    <div class="col-sm-offset-3 col-lg-9"><div class="help-block"></div>
                    </div>
                </div>
                <?= $form->field($model, 'current_password')->passwordInput(['autocomplete'=>'off']) ?>

                <?= $form->field($model, 'new_password')->passwordInput() ?>
                <?= $form->field($model, 'confirm_new_password')->passwordInput() ?>


               

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-primary btn-lg']) ?><br>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    
</div>
