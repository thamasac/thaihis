<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

//, user-scalable=no
AppAsset::register($this);
\cpn\chanpan\assets\CNCroppieAssets::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{};
        Tawk_API.visitor = {
        name : '<?php echo (isset(\Yii::$app->user->identity->profile->firstname)?\Yii::$app->user->identity->profile->firstname:'Gust') . ' ' . (isset(\Yii::$app->user->identity->profile->lastname)?\Yii::$app->user->identity->profile->lastname:'');?>',
        email : '<?php echo isset(\Yii::$app->user->identity->email)?\Yii::$app->user->identity->email:'';?>'
        };
        Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5c9b4dd16bba460528ffd8f3/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
        <!--End of Tawk.to Script-->        
    </head>
    <body class="page-header-fixed  page-sidebar-fixed  <?= (isset($_COOKIE['sidebar_toggler']) && $_COOKIE['sidebar_toggler'] == 1) ? '' : 'page-sidebar-closed'; ?> ">
        <?php $this->beginBody() ?>
        <?php

        echo yii\bootstrap\Modal::widget([
            'id' => 'modal-themes',
            'size' => 'modal-xxl',
            'options' => ['tabindex' => false]
        ]);
        ?>
        <?php
        backend\components\AppComponent::navbarMenu();
        backend\components\AppComponent::navbarRightMenu();
        $mainUrl = Yii::$app->homeUrl;
        $domain = isset(Yii::$app->params['model_dynamic']) ? Yii::$app->params['model_dynamic'] : '';


        $main_url = isset(\Yii::$app->params['main_url']) ? \Yii::$app->params['main_url'] : '';
        $url_index = isset(\Yii::$app->params['url_index']) ? \Yii::$app->params['url_index'] : '';
        $urlFrontend = isset(Yii::$app->params['frontend_full_url']) ? Yii::$app->params['frontend_full_url'] : '';//'https://' . \backend\modules\core\classes\CoreFunc::getParams('frontend_url', 'url');
        if ($domain['url'] != $main_url) {
             $mainUrl = \yii\helpers\Url::to(['/']);//"/{$url_index}";

             //\appxq\sdii\utils\VarDumper::dump(\Yii::$app->user);

        }else {
//            $mainUrl = $urlFrontend;
            $mainUrl = "{$urlFrontend}";
        }

        if(\cpn\chanpan\classes\CNServerConfig::isPortal() || \cpn\chanpan\classes\CNServerConfig::isLocal() || \cpn\chanpan\classes\CNServerConfig::isTest()){
            $mainUrl = "{$urlFrontend}";
        }
        NavBar::begin([
            'id' => 'main-nav-app',
            'brandLabel' => isset(Yii::$app->params['company_name']) ? Yii::$app->params['company_name'] : 'My Company',
            'brandUrl' => $mainUrl, //Yii::$app->homeUrl,
            'innerContainerOptions' => ['class' => 'container-fluid'],
            'options' => [
                'class' => 'page-container navbar navbar-inverse navbar-fixed-top',
            ],
        ]);
        echo $this->render('_themes');

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav','id'=>'navbar-nav'],
            'items' => isset(Yii::$app->params['navbar']) ? Yii::$app->params['navbar'] : [],
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right','id'=>'navbar-right'],
            'items' => isset(Yii::$app->params['navbarR']) ? Yii::$app->params['navbarR'] : [],
        ]);
        //รันคำสั่ง sql เพิ่มเพิ่มการตั้งค่า notify
        //REPLACE INTO `core_options` VALUES ('99998','notify_endable', '1', 'yes', 'เปิดใช้งานการแจ้งเตือน', '', 'Checkbox', '', '', 0, '', '', 0);
        //REPLACE INTO `core_options` VALUES ('99999','notify_time', '5', 'yes', 'เวลาการเช็คข้อมูลการแจ้งเตือนใหม่', '', '', '', '', 0, '', '', 0);
//        if(!\cpn\chanpan\classes\CNServerConfig::isPortal()){
            if(!isset(Yii::$app->params['notify_endable'])){
                $notify_enable = \backend\modules\core\models\CoreOptions::findOne(['option_name' => 'notify_endable']);
            }else{
                $notify_enable = [];
                $notify_enable['option_value'] = Yii::$app->params['notify_endable'];
            }
            if (!Yii::$app->user->isGuest && (isset($notify_enable['option_value']) && $notify_enable['option_value'] == 1 || !$notify_enable)) {
                if(!isset(Yii::$app->params['notify_endable'])){
                    $notify_time = \backend\modules\core\models\CoreOptions::findOne(['option_name' => 'notify_time']);
                    $notify_time = isset($notify_time['option_value']) && $notify_time['option_value'] != '' ? $notify_time['option_value'] : '5';
                }else{
                    $notify_time = Yii::$app->params['notify_time'];
                }

                echo dms\aomruk\widgets\AlertNotify::ui()->time($notify_time)->position('left')->buildWidget();
            }
//        }



        echo '<div class="navbar-text pull-right">';
        echo \lajax\languagepicker\widgets\LanguagePicker::widget([
            'skin' => \lajax\languagepicker\widgets\LanguagePicker::SKIN_DROPDOWN,
            'size' => \lajax\languagepicker\widgets\LanguagePicker::SIZE_SMALL
        ]);
        echo '</div>';

        NavBar::end();
        ?>
        <?php
        //echo $this->render('//layouts/_page-sidebar');
        echo $this->render('//layouts/_site_map');
        ?>

        <section class="page-container page-content" role="main">

            <div class="sdbox">
                <div class="page-column <?= (isset($_COOKIE['feedback_toggler']) && $_COOKIE['feedback_toggler'] == 1 && isset(Yii::$app->params['feedback']) && Yii::$app->params['feedback'] == 1 && !Yii::$app->user->isGuest) ? 'column2' : ''; ?>">


                    <?php
                            //end dynamic url config

                    $url = Yii::$app->params['model_dynamic'];//backend\modules\manageproject\classes\CNConfig::getServerName();

                    if (!Yii::$app->user->isGuest) {
                        $projectName    = isset(Yii::$app->params['project_name'])  ? Yii::$app->params['project_name'] : ''; //\backend\modules\manageproject\classes\CNProject::getProjectName();
                        $aconym         = isset(Yii::$app->params['aconym'])        ? Yii::$app->params['aconym'] : '';//\backend\modules\manageproject\classes\CNProject::getProjectAcronym();
                        $sitecode       = isset(Yii::$app->params['site_name'])     ? Yii::$app->params['site_name'] : ''; //\common\modules\user\classes\CNSitecode::getSiteValue();
                        $dataId         = isset(Yii::$app->params['model_dynamic']) ? Yii::$app->params['model_dynamic'] : ''; //backend\modules\manageproject\classes\CNProject::getProject();
                        $dataId         = isset($dataId)                            ? $dataId['data_id'] : '';

                        ?>
                        <?php

                            $role_name  = isset(\Yii::$app->params['role_name']) ? \Yii::$app->params['role_name'] : 'Not yet assigned';
                            $urlHome    = isset(\Yii::$app->params['main_url']) ? \Yii::$app->params['main_url'] : '';//\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
                            $urlCurrent = isset(\Yii::$app->params['model_dynamic']) ? \Yii::$app->params['model_dynamic'] : '';//\cpn\chanpan\classes\CNServer::getServerName();
                            $dataIcon   = isset(\Yii::$app->params['my_project']) ? \Yii::$app->params['my_project'] : '';
                            $imgPath    = isset(\Yii::$app->params['storageUrl']) ? \Yii::$app->params['storageUrl'] : '';
                            $imgBackend = isset(\Yii::$app->params['backendUrl']) ? \Yii::$app->params['backendUrl'] : '';
                            $imageSec   = "https://{$urlHome}//img/health-icon.png";
                            $site_text = \backend\modules\core\classes\CoreFunc::getParams('site_text', 'url');
                        ?>


                        <?php if (!empty($site_text) || $site_text != ''): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $site_text ?>
                                </div>
                            </div>
                        <?php else: ?>
                    
                            <?php if(!cpn\chanpan\classes\CNServerConfig::isLocal()):?>
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-8">
                                        <?= \backend\modules\manageproject\classes\CNHeaderSetting::classNames()
                                            ->setImagePath($imgPath)
                                            ->setNoImage($imageSec)
                                            ->setDataId($dataId)
                                            ->setProjectName($projectName)
                                            ->setRoleName($role_name)
                                            ->setSitecode($sitecode)
                                            ->setDataIcon($dataIcon)
                                            ->buildUi() ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?php if(Yii::$app->user->can('administrator')):?>
                                             <?php echo \backend\modules\manageproject\classes\CNHeaderSetting::classNames()->buildUiIcon()?>
                                        <?php else: ?>
                                        <?php
                                            if(!cpn\chanpan\classes\CNServerConfig::isLocal()){
                                                $html = "";
                                                $html .= yii\bootstrap\Modal::widget([
                                                    'id'=>'modal-create-project',
                                                    'size'=>'modal-xxl',
                                                    'options'=>['tabindex' => false]
                                                ]);
                                                $imgClone = Html::img('@web/img/clone.png', ['style' => 'width:25px;height:25px;top: 0;filter: grayscale(100%);
                                                -webkit-filter: grayscale(100%);
                                                -moz-filter: grayscale(100%);
                                                -o-filter: grayscale(100%);
                                                -ms-filter: grayscale(100%);', 'class' => 'img ', 'title' => Yii::t('chanpan', 'Clone Project')]);
                                                 $html .= "<div class='pull-right'>";
                                                  $html .= Html::a('&nbsp;'.$imgClone, '#', ['class' => 'btnCloneProjects', 'id' => 'btnCloneProjects']);
                                                 $html .= "</div>";
                                                 echo $html;
                                            }

                                        ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php \richardfan\widget\JSRegister::begin();?>
                                        <script>
                                            $('.btnCloneProjects').on('click', function(){
                                                let uri = '/manageproject/template/get-clone-form-create';
                                                let id = '<?= $dataId?>';
                                                $('#modal-create-project').modal('show');
                                                $('#modal-create-project .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
                                                $.get(uri, {id:id}, function(data){
                                                  $('#modal-create-project .modal-content').html(data);
                                                   return false;
                                                });
                                               return false;
                                            });
                                        </script>
                                    <?php \richardfan\widget\JSRegister::end();?>
                                </div>
                            </div><!-- -->
                            <?php endif; ?>
                            <?php endif; ?>

                        <?php
                    }
                    ?>
                            <?php
                    //\appxq\sdii\utils\VarDumper::dump($this->params['breadcrumbs']);
                            ?>
                            <div class="alert alert-warning fade in alert-dismissible" id="browser_support"  style="display:none;">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        <strong><i class="fa fa-exclamation-circle" aria-hidden="true"></i></strong> Google Chrome was strongly recommended for the best compatibility display.
                    </div>
                    <?php
                        $mainUrl = (\cpn\chanpan\classes\CNServerConfig::isPortal())?$mainUrl:'/ezmodules/ezmodule/view?id=1521647584047559700&tab=1528945511006792400&addon=0';
                        echo Breadcrumbs::widget([
                        'homeLink' => ['label' => 'Home',
                        'url' => $mainUrl],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        'encodeLabels'=>false
                    ])
                    ?>

                    <?php foreach (Yii::$app->session->getAllFlashes() as $message): ?>
                        <?php
                        if (isset($message['body'])) {
                            echo \yii\bootstrap\Alert::widget([
                                'body' => $message['body'],
                                'options' => $message['options'],
                            ]);
                        }
                        ?>
                    <?php endforeach; ?>

                    <?php
//                        $js = "
//                            checkUpdate=function(){
//                            console.log('check update...');
//                            let url = '".yii\helpers\Url::to(['/site/update-command'])."';
//                            $.get(url, function(data){
//                               console.log(data);
//                            }).fail(function() {
//                            });
//                        }
//                        checkUpdate();
//                        ";
//                       $this->registerJs($js);
                    ?>

                    <?= $content ?>

                    <?= (isset(Yii::$app->params['feedback']) && Yii::$app->params['feedback'] == 1 && !Yii::$app->user->isGuest) ? $this->render('//layouts/_rightside') : '' ?>

                    <?php echo $this->render('//layouts/_footer'); ?>
                </div>
            </div>
        </section>

        <?= \bluezed\scrollTop\ScrollTop::widget() ?>
        <?php appxq\sdii\widgets\CSSRegister::begin();?>
        <style>
            
            @media screen and (max-width: 1078px) {
                .navbar-inverse .navbar-nav > li > a {
                   color: #EEE;
                   font-size: 12px;
               }
               #user-profile-responsive{
                   background: transparent;
                    width: 50px;
                    overflow: hidden;
                    height: 39px;
               }

   
            }
            
        </style>
        <?php appxq\sdii\widgets\CSSRegister::end();?>
        <?php richardfan\widget\JSRegister::begin(); ?>
        <script> 
            if(!/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())){
               $('#browser_support').show();
            }
        </script>
        <?php richardfan\widget\JSRegister::end(); ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>

