<?php

namespace frontend\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;
use yii\web\Request;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Url;

class AppComponent extends Component {

    public function init() {
        parent::init();
        Yii::setAlias('storageUrl', Yii::$app->params['storageUrl']);
        Yii::setAlias('backendUrl', Yii::$app->params['backendUrl']);
        Yii::setAlias('frontendUrl', Yii::$app->params['frontendUrl']);


        //Yii::$app->params['profilefields'] = \backend\modules\core\classes\CoreQuery::getTableFields('profile');
        $params = \backend\modules\core\classes\CoreQuery::getOptionsParams();
        Yii::$app->params = \yii\helpers\ArrayHelper::merge(Yii::$app->params, $params);
    }

    public static function navbarMenu($moduleID, $controllerID, $actionID) {
        //1511763910073227000
        $ezf_id = '1511763910073227000';
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        $sitecode = '00';

        $host_arr = explode(".", $_SERVER['HTTP_HOST']);

        if ($host_arr[0] !== "www") {
            //$host_arr[0] = 'joker123';
            $modelSitecodeConfig = Yii::$app->db->createCommand("SELECT * FROM zdata_sitecode WHERE site_frontend_url = :domain AND rstat < 3", [":domain" => $host_arr[0]])->queryOne();
            if($modelSitecodeConfig){
                $sitecode = $modelSitecodeConfig['site_name'];

//                Yii::$app->session->set('current_site',$sitecode);
            }
        }
        
        $model_menu = \backend\modules\subjects\classes\SubjectManagementQuery::getMenu($modelEzf->ezf_table, 0, $sitecode);

        Yii::$app->params['navbar'] = [
//            ['label' => '<i class="fa fa-calendar"></i> ' . Yii::t('appmenu', 'Calendar'), 'encode' => FALSE, 'url' => ['//calendar/index']],
//            ['label' => '<i class="fa fa-cube"></i> '.Yii::t('appmenu', 'Modules'), 'encode' => FALSE, 'url' => ['#']],
//            ['label' => '<i class="glyphicon glyphicon-plus"></i> '.Yii::t('appmenu', 'Data Lists'), 'encode' => FALSE, 'url' => ['//ezforms2/data-lists/index']],
//            ['label' => '<i class="glyphicon glyphicon-export"></i> '.Yii::t('appmenu', 'Export'), 'encode' => FALSE, 'url' => ['#']],
//	    ['label' => 'About', 'url' => ['/site/about']],
                //['label' => 'Contact', 'url' => ['/site/contact']],
        ];

        $menu_ezform = [];
        $menu = 0;
        foreach ($model_menu as $key => $value) {
            if ($key == 0 && $controllerID == 'site' && $actionID == 'index') {
                $menu = isset($_GET['menu']) ? $_GET['menu'] : $value['id'];
            }

            $modelSubmenu = \backend\modules\subjects\classes\SubjectManagementQuery::getMenu($modelEzf->ezf_table, $value['id'],$sitecode);

            if ($modelSubmenu) {
                $subItems = [];
                $subId = [];

                foreach ($modelSubmenu as $subKey => $subValue) {
                    $subItems[] = [
                        'label' => $subValue['menu_name'],
                        'url' => Url::to(['//site/index', 'menu' => $subValue['id']]),
                        'active' => $controllerID == 'site' && $actionID == 'index' && $menu == $subValue['id'],
                    ];
                    $subId[] = $subValue['id'];
                }
                $menu_ezform[] = [
                    'label' => Yii::t('app', $value['menu_name']),
                    'url' => '#',
                    'items' => $subItems,
                    'dropDownOptions' => ['id' => \appxq\sdii\utils\SDUtility::getMillisecTime()],
                    'active' => $controllerID == 'site' && $actionID == 'index' && in_array($menu, $subId),
                ];
            } else {
                $menu_ezform[] = ['label' => Yii::t('app', $value['menu_name']), 'encode' => FALSE, 'url' => ['//site/index', 'menu' => $value['id']], 'active' => ($menu == $value['id'])];
            }
        }

        Yii::$app->params['navbar'] = \yii\helpers\ArrayHelper::merge(Yii::$app->params['navbar'], $menu_ezform);
        //Yii::$app->params['navbar'][] = ['label' => '<i class="fa fa-calendar"></i> ' . Yii::t('appmenu', 'Calendar'), 'encode' => FALSE, 'url' => ['//calendar/index']];
    }

    public static function navbarRightMenu() {
        if (Yii::$app->user->isGuest) {
            //Yii::$app->params['navbarR'][] = ['label' => '<i class="fa fa-user-plus"></i> ' . Yii::t('appmenu', 'Sign up'), 'encode' => FALSE, 'url' => ['/user/registration/register']];
            //Yii::$app->params['navbarR'][] = ['label' => '<i class="fa fa-sign-in"></i> ' . Yii::t('appmenu', 'Sign in'), 'encode' => FALSE, 'url' => ['/user/security/login']];
        } else {
            Yii::$app->params['navbarR'][] = ['label' => '<i class="fa fa-sliders"></i> ' . Yii::t('appmenu', 'Account({name})', ['name' => Yii::$app->user->identity->username]), 'encode' => FALSE, 'items' => [
//                    ['label' => '<i class="fa fa-user"></i> ' . Yii::t('appmenu', 'Profile'), 'encode' => FALSE, 'url' => ['/user/settings/profile']],
//                    ['label' => '<i class="fa fa-key"></i> ' . Yii::t('appmenu', 'Account'), 'encode' => FALSE, 'url' => ['/user/settings/account']],
//                    ['label' => '<i class="fa fa-facebook-official"></i> ' . Yii::t('appmenu', 'Networks'), 'encode' => FALSE, 'url' => ['/user/settings/networks']],
//                    ['label' => '<i class="fa fa-users"></i> ' . Yii::t('appmenu', 'Manage users'), 'encode' => FALSE, 'url' => ['/user/admin/index']],
                    ['label' => '<i class="fa fa-sign-out"></i> ' . Yii::t('appmenu', 'Logout'), 'encode' => FALSE, 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
            ]];
        }
    }

}
