<?php
/**
 * Created by PhpStorm.
 * User: tyroroto
 * Date: 4/1/2019 AD
 * Time: 23:46
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dektrium\user\widgets\Connect;

/**
 * @var yii\web\View                   $this
 * @var dektrium\user\models\LoginForm $model
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
        <div class="panel panel-default">

            <div class="panel-body">
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
                        <div class="box-border"></div>
                    </div>

                <?php endif; ?>

                <div class="col-md-12">

                </div>




            </div>
        </div>
        <?php
        $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
        $mainUrl = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        ?>

    </div>

</div>
