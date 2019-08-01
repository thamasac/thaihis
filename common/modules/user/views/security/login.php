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
use dektrium\user\widgets\Connect;

/**
 * @var yii\web\View                   $this
 * @var dektrium\user\models\LoginForm $model
 * @var dektrium\user\Module           $module
 */
$this->title = Yii::t('chanpan', 'Log into your nCRC account');
$this->params['breadcrumbs'][] = $this->title;

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback', 'autofocus' => 'autofocus', 'tabindex' => '1'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback', 'tabindex' => '2'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>


<div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
        <?= $this->render('../registration/logo')?>
        <div class="panel panel-default">
            
            <div class="panel-body">
                <?= $this->render('../registration/menu', ['active'=>'login'])?>
                <!-- /.social-auth-links -->
                <?php
                $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
                $main_url = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';
                ?> 
                <?php if ($domain == $main_url || cpn\chanpan\classes\CNServerConfig::isLocal() || cpn\chanpan\classes\CNServerConfig::isTest()): ?> 
                    <div class="social">
                        <?=
                        common\modules\user\classes\CNAuthChoice::widget([
                            'baseAuthUrl' => ['/social-media/auth'],
                            'popupMode' => false,
                            'options' => [
                            ]
                        ])
                        ?>
                        <div class="col-md-12">
                            <div class="col-md-12" style="padding-left: 5px;    padding-right: 5px;    margin-top: -9px;">
                                <?php Html::button('Line Login', ['class' => 'btn btn-success btn-block', 'style' => 'background: #1da01d;color: #FFFFFF;font-size: 14pt;font-family: serif;']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="or-box">
                        <span class="or">OR</span>
                        <div class="box-border"></div>
                    </div>

                <?php endif; ?>

                <div class="col-md-12">
                    <?php
                    $form = \yii\bootstrap\ActiveForm::begin([
                                'id' => 'login-form',
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => false,
                                'validateOnBlur' => false,
                                'validateOnType' => false,
                                'validateOnChange' => false,
                            ])
                    ?>

                    <?php if ($module->debug): ?>
                        <?=
                        $form->field($model, 'login', [
                            'inputOptions' => [
                                'autofocus' => 'autofocus',
                                'class' => 'form-control',
                                'tabindex' => '1']])->dropDownList(LoginForm::loginList());
                        ?>

                    <?php else: ?>

                        <?= $form->field($model, 'login', $fieldOptions1)->label(Yii::t('chanpan', 'Email or Username'));
                        ?>

                    <?php endif ?>

                    <?php if ($module->debug): ?>
                        <div class="alert alert-warning">
                            <?= Yii::t('user', 'Password is not necessary because the module is in DEBUG mode.'); ?>
                        </div>
                    <?php else: ?>
                        <?=
                                $form->field(
                                        $model, 'password', $fieldOptions2)
                                ->passwordInput()
                                ->label(
                                        Yii::t('user', 'Password')
                                        . ($module->enablePasswordRecovery ?
                                                ' (' . Html::a(
                                                        Yii::t('user', 'Forgot password?'), ['/user/recovery/request'], ['tabindex' => '5']
                                                )
                                                . ')' : '')
                                )
                        ?>
                    <?php endif ?>
                    <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '4']) ?>
                    <?= Html::submitButton(Yii::t('chanpan', 'Sign in'), ['class' => 'btn btn-primary btn-block btn-lg', 'tabindex' => '3']) ?>

                    <?php \yii\bootstrap\ActiveForm::end(); ?>
                </div>




            </div>
        </div>
        <?php
        $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
        $mainUrl = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        ?>         
        <?php if ($domain == $mainUrl || cpn\chanpan\classes\CNServerConfig::isLocal() || cpn\chanpan\classes\CNServerConfig::isTest()): ?>
            <?php if ($module->enableConfirmation): ?>
                <p class="text-center">
                    <?= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']) ?>
                </p>
            <?php endif ?>
            <?php if ($module->enableRegistration): ?>
                <p class="text-center">
                    <?= Html::a(Yii::t('chanpan', 'Don\'t have an account, create a new account here'), ['/user/registration/register']) ?>
                </p>
            <?php endif ?>
        <?php endif; ?> 

    </div>

</div>
