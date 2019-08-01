<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use common\modules\user\classes\InvitationInfo;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use kartik\select2\Select2;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 * @var dektrium\user\Module $module
 * @var invitationInfo $inviteInfo
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="row">

        <div class="col-md-6 col-md-offset-3">
            <?= $this->render('./logo')?>
            <div>
                <div class="text-center">
                    <h4><?= Yii::t('app','Please fill in the Sign up form below. Once you signed up, you will have an account in both nCRC and the invitation project.')?></h4>
                    <?php
                    if ($inviteInfo != null && $inviteInfo->isHasInvite) {
                        echo Html::a(Yii::t('chanpan', 'Already registered? Sign in for apply invitation!'), ['/user/security/login', 'email' => $inviteInfo->email, 'token' => $inviteInfo->token, 'project_id' => $inviteInfo->project_id]);
                    }
                    ?>
                    <br>
                </div>
            </div>
            <br>
            
            <div class="panel panel-default">
                 <?= $this->render('./menu', ['active'=>'register'])?>
                <div class="panel-body">
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <i class="fa fa-exclamation-circle"
                               aria-hidden="true"></i> <?= Yii::$app->session->getFlash('error') ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    $form = ActiveForm::begin([
                                'id' => 'registration-form',
                                'enableAjaxValidation' => true,
                                //'enableClientValidation' => false,
                                'options' => ['enctype' => 'multipart/form-data'],
                                'layout' => 'horizontal',
                                'fieldConfig' => [
                                    'horizontalCssClasses' => [
                                        'label' => 'col-md-3',
                                        'offset' => 'col-sm-offset-2',
                                        'wrapper' => 'col-md-8',
                                        'hint' => 'col-sm-8 col-sm-offset-3',
                                    ],
                                ],
                    ]);
                    ?>
                    <?php //$form->field($model, 'cid') ?>
                    <div class="col-md-12"><?= $form->field($model, 'username')->textInput(['autofocus'=>'autofocus', 'autocomplete'=>'off']) ?></div>

                    <div class="col-md-12"><?= $form->field($model, 'email')->textInput(['autocomplete'=>'off']) ?></div>

                    <div class="col-md-12">
                         <?php if ($module->enableGeneratingPassword == false): ?>
                                <?= $form->field($model, 'password')->passwordInput([])->hint(Yii::t('chanpan','Passwords must contain at least 6 characters.')) ?>
                                <?= $form->field($model, 'confirm_password')->passwordInput() ?>

                            <?php endif ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($model, 'firstname')->textInput(['autocomplete'=>'off']) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'lastname')->textInput(['autocomplete'=>'off']) ?>
                    </div>
                    <div class="clearfix"></div>  
<!--                    <div class="col-md-12">
                        <?php 
//                        $form->field($model, 'dob')->widget(\yii\widgets\MaskedInput::className(), [
//                            'mask' => '99/99/9999',
//                        ])->label(Yii::t('app','Birth date'))->hint(Yii::t('app','Example')." 20/12/1993")
                                ?>
                    </div>
                     
                    <div class="col-md-12">
                        <?php  
//                        $form->field($model, 'telephone')->widget(\yii\widgets\MaskedInput::className(), [
//                            'mask' => '9999999999',
//                        ])
                                ?>
                    </div>-->
                    <?php //echo $form->field($model, 'captcha')->widget(Captcha::className(), ['captchaAction' => ['/site/captcha']]); ?>
                    <?php
                    //echo common\modules\user\classes\CNDepartment::getDepartmentForm($form, $model, 'department');     
                    ?>
                    <?php
                    //echo common\modules\user\classes\CNSitecode::getSiteCodeForm($form, $model, 'sitecode');   

                    ?>

                </div>

                <div class="col-md-12">
                    <div class="col-md-8 col-md-offset-2">
                        <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-success btn-block btn-lg','style'=>'box-shadow: 1px 1px 1px #8e8e90;']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
                <div class="clearfix"></div>
                <p class="text-center" style="margin-top:15px;">
                    <?php
                    if ($inviteInfo != null && $inviteInfo->isHasInvite) {
                        echo Html::a(Yii::t('app', 'Already registered? Sign in for apply invitation!'), ['/user/security/login', 'email' => $inviteInfo->email, 'token' => $inviteInfo->token, 'project_id' => $inviteInfo->project_id]);
                    } else {
                        echo Html::a(Yii::t('app', 'Already registered? Sign in'), ['/user/security/login']);
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
<?php
$this->registerJs("
        $('form#registration-form').on('beforeSubmit', function(e){
            
        });

    ");
$this->registerCss("
        div.required label.control-label:after {
            content: \" *\";
            color: red;
        }
    ");
?>