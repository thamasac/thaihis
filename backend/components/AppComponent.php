<?php

namespace backend\components;

use common\modules\user\models\User;
use common\modules\user\classes\CNSocialFunc;
use Yii;
use yii\base\Component;
use yii\web\UnauthorizedHttpException;

class AppComponent extends Component {
    
    public function init() {
        parent::init();
        
//        \appxq\sdii\utils\VarDumper::dump();
//      date_default_timezone_set('UTC');
        Yii::setAlias('storageUrl', Yii::$app->params['storageUrl']);
        Yii::setAlias('backendUrl', Yii::$app->params['backendUrl']);
        Yii::setAlias('frontendUrl', Yii::$app->params['frontendUrl']);
        
        $this->checkAccessToken();
        $dynamic = \cpn\chanpan\classes\CNServerConfig::getDynamicConnect(TRUE);
        //\appxq\sdii\utils\VarDumper::dump($dynamic);
        
        Yii::$app->params['sidebar'] = [];
        
        Yii::$app->params['profilefields']      = \backend\modules\core\classes\CoreQuery::getTableFields('profile');//
        $params = \backend\modules\core\classes\CoreQuery::getOptionsParams();
        Yii::$app->params = \yii\helpers\ArrayHelper::merge(Yii::$app->params, $params);
        
        //update project
        \cpn\chanpan\classes\utils\CNProject::CommandUpdate();
        
        //config params server project
        \cpn\chanpan\classes\CNServerConfig::configParamsServerProject();
        
    }
    
    public static function navbarMenu() { 
        $redirect2portal = isset(Yii::$app->params['redirect2portal']) ? Yii::$app->params['redirect2portal'] : '';//\cpn\chanpan\classes\CNServer::getUrl();
        $urlMain = isset(Yii::$app->params['model_dynamic']) ? Yii::$app->params['model_dynamic'] : '';//\cpn\chanpan\classes\CNServer::getServerName();
        $main_url = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';//\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        $menuList = isset(Yii::$app->params['project_menu']) ? Yii::$app->params['project_menu'] : '';//\backend\modules\core\classes\CoreFunc::getParams('project_menu', 'project'); 
        //\appxq\sdii\utils\VarDumper::dump($menuList); 
        $nav = [];
         

        if(!$menuList){ 
            Yii::$app->params['navbar'] = [
                ['label' => '<i class="glyphicon glyphicon-th"></i> '.Yii::t('appmenu', 'All My Projects'), 'encode' => FALSE, 'url' => "/site/index",'active'=>(Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index') ? true : false],
                ['label' => '<i class="fa fa-magic"></i> '.Yii::t('appmenu', 'EzForm'), 'encode' => FALSE, 'url' => ['/ezforms2/ezform/index'], 'visible'=>(Yii::$app->user->can('administrator') || $urlMain['url'] != $main_url)],
                ['label' => '<i class="fa fa-cube"></i> '.Yii::t('appmenu', 'EzModule'), 'encode' => FALSE, 'url' => ['/ezmodules/default/index'], 'visible'=>(Yii::$app->user->can('administrator') || $urlMain['url'] != $main_url)],
                ['label' => '<i class="glyphicon glyphicon-plus"></i> '.Yii::t('appmenu', 'EzEntry'), 'encode' => FALSE, 'url' => ['/ezforms2/data-lists/index'], 'visible'=>(Yii::$app->user->can('administrator') || $urlMain['url'] != $main_url)],
                ['label' => '<i class="glyphicon glyphicon-phone"></i> '.Yii::t('appmenu', 'EzMobile'), 'encode' => FALSE, 'url' => ['/ezforms2/mobile/index'], 'visible'=>(Yii::$app->user->can('user') || $urlMain['url'] != $main_url)],
                
            ];
        }else{
            $moduleId        = (isset(Yii::$app->controller->module->id) && Yii::$app->controller->module->id != 'app-backend') ? Yii::$app->controller->module->id : '';
            $controllerId    = isset(Yii::$app->controller->id) ? Yii::$app->controller->id : '';
            $actionId        = isset(Yii::$app->controller->action->id) ? Yii::$app->controller->action->id : '';
            $viewId          = \Yii::$app->request->get('id', ''); 
            
            foreach ($menuList["menus"] as $key => $value){
                if(isset($value['visible_local']) && $value['visible_local'] == FALSE && \cpn\chanpan\classes\CNServerConfig::isLocal()){
                    continue;
                }else{
                    $icon = isset($value["icon"]) ? " <i class='".$value['icon']."'></i> ": "";
                    $label = isset($value["label"]) ? Yii::t('appmenu', $value["label"]): "None";
                    $url = isset($value["url"]) ? $value["url"] : "#";
                    $url = str_replace('$mainUrl' , $redirect2portal,$url); 

                    $t = ['label' => $icon.$label, 'encode' => FALSE, 'url' => strpos($url, $redirect2portal) !== false ? $url :[$url]];

                    if(isset($value['value_active']) && isset($value['value_active']['controller']) && isset($value['value_active']['action']) && !isset($value['value_active']['id'])){
                        $t['active'] = ($controllerId == $value['value_active']['controller'] && $actionId == $value['value_active']['action']) ? TRUE :FALSE;
                    }//controller and action
                    else if(isset($value['value_active']) && isset($value['value_active']['controller']) && isset($value['value_active']['action']) && isset($value['value_active']['id'])){
                        $t['active'] = ($controllerId == $value['value_active']['controller'] && $actionId == $value['value_active']['action'] && $viewId == $value['value_active']['id']) ? TRUE :FALSE;
                    }
                    if( isset($value["visible_role"]) ){ 
                        $t['visible'] = (Yii::$app->user->can( $value["visible_role"] ) || $urlMain['url'] != $main_url);
                    } 
                    array_push($nav ,$t );
                }
                
            }
            Yii::$app->params['navbar']  = $nav;
            
        }


    }

    public static function navbarRightMenu() {
        $userProfile = isset(Yii::$app->user->identity->profile) ? Yii::$app->user->identity->profile : '';
        
        if (Yii::$app->user->isGuest) {
            Yii::$app->params['navbarR'][] = ['label' => '<i class="fa fa-user-plus"></i> '.Yii::t('chanpan', 'Apply for a new account'), 'encode' => FALSE, 'url' => ['/user/registration/register']];
            Yii::$app->params['navbarR'][] = ['label' => '<i class="fa fa-sign-in"></i> '.Yii::t('chanpan', 'Login'), 'encode' => FALSE, 'url' => ['/user/security/login']];
        } else {

            
            $avatar_url = Yii::getAlias('@storageUrl') . '/images/nouser.png';
            if(isset($userProfile->avatar_path) && !empty($userProfile->avatar_path)){
                $avatar_url = Yii::getAlias('@storageUrl/source').'/'.$userProfile->avatar_path;
            }
            $avatar_img = '<img class="img-circle" width="18" src="'.$avatar_url.'"/>';
            
            $urlMain = Yii::$app->params['model_dynamic'];//\cpn\chanpan\classes\CNServer::getServerName();
            $main_url = Yii::$app->params['main_url'];//\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
            
           // \appxq\sdii\utils\VarDumper::dump($main_url);
            
             
            $logout = \cpn\chanpan\classes\CNServerConfig::isPortal() ? Yii::t('appmenu', 'Logout') : Yii::t('appmenu', 'Logout  to Portal'); 
            Yii::$app->params['navbarR'][] = ['label' => $avatar_img.' '. (isset($userProfile['firstname'])?$userProfile['firstname'].' '.$userProfile['lastname']:'Unknown'), 'encode' => FALSE, 'items' => [
                    ['label' => '<i class="fa fa-cog"></i> '.Yii::t('chanpan', 'Project Settings'), 'encode' => FALSE, 'url' => ['/manageproject/monitor-project'], 'visible'=>($urlMain['url'] == $main_url)],
                    ['label' => '<i class="fa fa-user"></i> '.Yii::t('chanpan', 'User Profile'), 'encode' => FALSE, 'url' => ['/user/settings/profile']],
                    //['label' => '<i class="fa fa-facebook-official"></i> '.Yii::t('appmenu', 'Networks'), 'encode' => FALSE, 'url' => ['/user/settings/networks']],
                    //['label' => '<i class="fa fa-users"></i> '.Yii::t('appmenu', 'Manage users'), 'visible'=>(Yii::$app->user->can('adminsite')), 'encode' => FALSE, 'url' => ['/ezmodules/ezmodule/view?id=1524804539066947200']],
                    ['label' => '<i class="fa fa-sign-out"></i> '.$logout, 'encode' => FALSE, 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'get']],
            ],'dropDownOptions'=>['id'=>'navbarR-dropdown']];
            
        }
       
    }
    

    public static function sidebarMenu($moduleID, $controllerID, $actionID) {
        $group = 'item';
        if (isset($_GET['group']) && in_array($_GET['group'], ['person', 'place', 'item'])) {
            $group = $_GET['group'];
        } 
        $urlMain     = isset(Yii::$app->params['model_dynamic']) ? Yii::$app->params['model_dynamic'] : '';//\cpn\chanpan\classes\CNServer::getServerName();
        $main_url    = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';//\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        $errors      = \backend\modules\ezforms2\models\SystemError::find()->count();
        $urlFrontend = isset(Yii::$app->params['frontend_full_url']) ? Yii::$app->params['frontend_full_url'] : '';//'https://'.\backend\modules\core\classes\CoreFunc::getParams('frontend_url', 'url');
         
        
        $urlHome     = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';//\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        $urlHome     = isset(Yii::$app->params['redirect2portal']) ? Yii::$app->params['redirect2portal'] : '';//'https://'.$urlHome;
	Yii::$app->params['sidebar'] = [
            ['label' => Yii::t('appmenu', 'nCRC Central Site'), 'icon' => 'fa fa-home', 'url' => $urlFrontend],
	    ['label' => Yii::t('appmenu', 'All My Projects'), 'icon' => 'glyphicon glyphicon-th', 'url' => $urlHome,'active' => ($controllerID == 'site' && $actionID=='index') ? TRUE : FALSE],
   
	    ['label' => Yii::t('appmenu', 'EzForms'), 'icon' => 'fa fa-archive', 'url' => '#', 'visible'=>(Yii::$app->user->can('administrator') || $urlMain['url'] != $main_url), 'active' => (in_array($moduleID, [
		    'ezforms2',
                    'ezform_builder',
		])), 'items' => [
		    ['label' => Yii::t('appmenu', 'EzForm'), 'icon' => 'fa fa-magic', 'url' => ['//ezforms2/ezform/index'], 'active' => $controllerID == 'ezform'],
                    ['label' => Yii::t('appmenu', 'EzForm Versions'), 'visible'=>(Yii::$app->user->can('administrator')), 'icon' => 'fa fa-check-circle', 'url' => ['//ezforms2/ezform-version/index'], 'active' => $controllerID == 'ezform-version'],
                    ['label' => Yii::t('appmenu', 'Query Tools'), 'visible'=>(Yii::$app->user->can('administrator')), 'icon' => 'fa fa-wrench', 'url' => ['//ezforms2/ezform-community/index'], 'active' => $controllerID == 'ezform-community'],
                    ['label' => Yii::t('appmenu', 'Form Category'), 'visible'=>(Yii::$app->user->can('adminsite')), 'icon' => 'fa fa-sitemap', 'url' => ['//ezforms2/ezform-tree/index'], 'active' => $controllerID == 'ezform-tree'],
                    ['label' => Yii::t('appmenu', 'Auto Number'), 'icon' => 'fa fa-sort-numeric-asc', 'url' => ['//ezforms2/ezform-autonum/index'], 'active' => $controllerID == 'ezform-autonum'],
                    ['label' => Yii::t('appmenu', 'Randomization'), 'icon' => 'glyphicon glyphicon-random', 'url' => ['//ezforms2/randomization/index'], 'active' => $controllerID == 'randomization'],
                    ['label' => Yii::t('appmenu', 'EzInput'), 'visible'=>(Yii::$app->user->can('administrator')), 'icon' => 'fa fa-dropbox', 'url' => ['//ezforms2/ezform-input/index'], 'active' => $controllerID == 'ezform-input'],
                    ['label' => Yii::t('appmenu', 'System Errors'). ' <span class="badge">'.$errors.'</span>', 'visible'=>(Yii::$app->user->can('administrator')), 'encode'=>false, 'icon' => 'glyphicon glyphicon-warning-sign', 'url' => ['/ezforms2/system-error/index']],
		]
	    ],
            
            ['label' => Yii::t('appmenu', 'EzModules'), 'icon' => 'fa fa-cubes', 'url' => '#', 'visible'=>(Yii::$app->user->can('administrator') || $urlMain['url'] != $main_url), 'active' => (in_array($controllerID, [
		    'ezmodule',
                    'ezmodule-study',
                    'ezmodule-widget',
                    'ezmodule-template',
		])), 'items' => [
		    ['label' => Yii::t('appmenu', 'EzModule Management'), 'icon' => 'fa fa-cube', 'url' => ['//ezmodules/ezmodule/index'], 'active' => $controllerID == 'ezmodule'],
                    ['label' => Yii::t('appmenu', 'Study Templates'), 'icon' => 'fa fa-cube', 'url' => ['//study_manage/ezmodule-study/index'], 'active' => $controllerID == 'ezmodule-study'],
                    ['label' => Yii::t('appmenu', 'Templates'), 'visible'=>(Yii::$app->user->can('adminsite')), 'icon' => 'fa fa-file-code-o', 'url' => ['//ezmodules/ezmodule-template/index'], 'active' => $controllerID == 'ezmodule-template'],
                    ['label' => Yii::t('appmenu', 'Widgets'), 'visible'=>(Yii::$app->user->can('adminsite')), 'icon' => 'fa fa-puzzle-piece', 'url' => ['//ezmodules/ezmodule-widget/index'], 'active' => $controllerID == 'ezmodule-widget'],
		]
	    ],
//            ['label' => Yii::t('appmenu', 'RADT'), 'icon' => 'glyphicon glyphicon-user', 'url' => ['//patient/patient']],
//            ['label' => Yii::t('appmenu', 'CPOE'), 'icon' => 'fa fa-user-md', 'url' => ['//cpoe']],
//            ['label' => Yii::t('appmenu', 'Order'), 'icon' => 'glyphicon glyphicon-th', 'url' => '#', 'active' => (in_array($controllerID, [
//		    'Counter',
//                    'const-order',
//		])), 'items' => [
//		    ['label' => Yii::t('appmenu', 'Order Lists'), 'icon' => 'fa fa-list', 'url' => ['//patient/order/order-counter'], 'active' => $controllerID == 'order-counter'],
//                    ['label' => Yii::t('appmenu', 'Order Setting'), 'icon' => 'fa fa-cog', 'url' => ['//patient/order/order-setting'], 'active' => $controllerID == 'const-order'],
//                    ['label' => Yii::t('appmenu', 'Cashier Counter'), 'icon' => 'fa fa-money', 'url' => ['//patient/cashier/cashier-counter'], 'active' => $controllerID == 'cashier-counter'],
//		]
//	    ],
//            ['label' => Yii::t('appmenu', 'Ward'), 'icon' => 'fa fa-bed', 'url' => ['//patient/admit/ward']],
            ['label' => Yii::t('appmenu', 'System Config'), 'icon' => 'fa fa-cog', 'visible'=>(Yii::$app->user->can('adminsite')), 'url' => '#', 'active' => (in_array($controllerID, [
		    'core-fields',
		    'core-generate',
		    'core-options',
                    'media',
                    'file-storage',
		    'core-item-alias',
		    'tables-fields',
		    'tb-faculty',
		    'tb-department',
		]) || in_array($moduleID, [
		    'admin',
		])), 'items' => [
                    ['label' => Yii::t('appmenu', 'Member Management'), 'icon' => 'fa fa-user', 'visible'=>(Yii::$app->user->can('administrator')),'url' => ['/ezmodules/ezmodule/view?id=1520798323068323400&tab=1524480265054326200&addon=0']],
                    ///manageproject/system-log
                    ['label' => Yii::t('appmenu', 'SQL Command'), 'icon' => 'fa fa-location-arrow','visible'=>(Yii::$app->user->can('administrator')), 'url' => ['/update_project'], 'active'=>($moduleID=='update_project') ? true : false],
                    ['label' => Yii::t('appmenu', 'System Error'), 'icon' => 'fa fa-location-arrow','visible'=>(Yii::$app->user->can('administrator')), 'url' => ['/log'], 'active'=>($moduleID="log") ? true : false],
                    ['label' => Yii::t('appmenu', 'System Log'), 'icon' => 'fa fa-location-arrow','visible'=>(Yii::$app->user->can('administrator')), 'url' => ['/manageproject/system-log'], 'active'=>($controllerID=='system-log') ? true : false],
                    
                    ['label' => Yii::t('appmenu', 'Update Tools'), 'icon' => 'fa fa-wrench','visible'=>(Yii::$app->user->can('administrator')), 'url' => ['/study_manage/update-tool']],
                    ['label' => Yii::t('appmenu', 'Edit Templates'), 'icon' => 'fa fa-envelope','visible'=>(Yii::$app->user->can('administrator')), 'url' => ['/site/edit-templates']],
                    ['label' => Yii::t('appmenu', 'Line Bot'), 'icon' => 'fa fa-at','visible'=>(Yii::$app->user->can('administrator')), 'url' => ['/linebot/line-functions']],
                    
		    ['label' => Yii::t('appmenu', 'Enterprise information'), 'icon' => 'fa fa-location-arrow', 'url' => ['//core/core-options/config', 'term'=>'company'], 'active'=> (isset($_GET['term']) && $_GET['term']=='company')?true:false],
                    ['label' => Yii::t('appmenu', 'Token Config'), 'icon' => 'fa fa-location-arrow', 'url' => ['//core/core-options/config', 'term'=>'token']],
		    ['label' => Yii::t('appmenu', 'Generate'), 'icon' => 'fa fa-life-ring', 'active' => $controllerID == 'core-generate', 'url' => ['//core/core-generate']],
                    ['label' => Yii::t('appmenu', 'Media'), 'icon' => 'fa fa-cloud-upload', 'active' => $controllerID == 'media', 'url' => ['//core/media/index']],
                    ['label' => Yii::t('appmenu', 'File Storage Log'), 'icon' => 'fa fa-file-archive-o', 'active' => $controllerID == 'file-storage', 'url' => ['//core/file-storage/index']],
		    ['label' => Yii::t('appmenu', 'Options Config'), 'icon' => 'fa fa-sliders', 'active' => ($controllerID == 'core-options' && $actionID !== 'config'), 'url' => ['//core/core-options']],
		    ['label' => Yii::t('appmenu', 'Input Fields'), 'icon' => 'fa fa-plug', 'active' => $controllerID == 'core-fields', 'url' => ['//core/core-fields']],
		    ['label' => Yii::t('appmenu', 'Item Alias'), 'icon' => 'fa fa-share-alt', 'active' => $controllerID == 'core-item-alias', 'url' => ['//core/core-item-alias']],
		    
                    ['label' => Yii::t('appmenu', 'Authentication'), 'icon' => 'fa fa-cogs', 'active' => in_array($moduleID, ['admin']), 'url' => '#', 'items' =>[
                        ['label' => Yii::t('appmenu', 'Assignment'), 'icon' => 'fa fa-chevron-right', 'active' => ($controllerID == 'assignment'), 'url' => ['/admin/assignment']],
                        ['label' => Yii::t('appmenu', 'Role'), 'icon' => 'fa fa-chevron-right', 'active' => ($controllerID == 'role'), 'url' => ['/admin/role']],
                        ['label' => Yii::t('appmenu', 'Permission'), 'icon' => 'fa fa-chevron-right', 'active' => ($controllerID == 'permission'), 'url' => ['/admin/permission']],
                        ['label' => Yii::t('appmenu', 'Route'), 'icon' => 'fa fa-chevron-right', 'active' => ($controllerID == 'route'), 'url' => ['/admin/route']],
                    ]],
                    
		    ['label' => Yii::t('appmenu', 'Tables Fields'), 'icon' => 'fa fa-magic', 'active' => $controllerID == 'tables-fields', 'url' => '#', 'items' => [
			    ['label' => Yii::t('appmenu', 'Profile'), 'icon' => 'fa fa-chevron-right', 'active' => ($controllerID == 'tables-fields' && $_GET['table'] == 'profile'), 'url' => ['//core/tables-fields', 'table' => 'profile']],
			]],
		]
	    ],
	];
        
        
        
        $urlHome  = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';//\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        $mainUrl = isset(Yii::$app->params['redirect2portal']) ? Yii::$app->params['redirect2portal'] : '';//;\cpn\chanpan\classes\CNServer::getUrl(); 
        $frontendUrl = isset(Yii::$app->params['frontend_full_url']) ? Yii::$app->params['frontend_full_url'] : ''; //\cpn\chanpan\classes\CNServer::getFrontend();
        //\appxq\sdii\utils\VarDumper::dump($urlHome); 
        Yii::$app->params['sitemap'] = [
                'important_page'=>[
                    ['label' => '<i class=""></i> nCRC Central Site', 'url' => $frontendUrl], 
                    ['label' => '<i class=""></i> All nCRC Projects', 'url' => "{$frontendUrl}/?menu=1527503755093386700"], 
                    ['label' => '<i class=""></i> All My Projects', 'url' => "{$mainUrl}/site/index",'active' => ($controllerID == 'site' && $actionID=='index') ? TRUE : FALSE], 
                    ['label' => '<i class=""></i> Current Project Home', 'url' => ['/site/index']], 
                ],
                'dashboard'=>[
                   ['label' => '<i class="fa fa-university"></i> Dashboard', 'url' => $mainUrl], //'https://'.$urlHome
                ],
                'system-tools'=>[
                    ['label' => '<i class="fa fa-magic"></i> EzForm', 'url' => '/ezforms2/ezform/index','active' => ($controllerID=='ezform' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-check-circle"></i> EzForm version', 'url' => '/ezforms2/ezform-version/index','active' => ($controllerID=='ezform-version' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-cube"></i> EzModule', 'url' => '/ezmodules/ezmodule/index','active' => ($controllerID=='ezmodule' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-puzzle-piece"></i> EzWidget', 'url' => '/ezmodules/ezmodule-widget/index','active' => ($controllerID=='ezmodule-widget' && $actionID=="index") ? true: false],
                ],
                'data-management-tools'=>[
                    ['label' => '<i class="fa fa-angle-right"></i> Query Tools', 'url' => '/ezforms2/ezform-community/index','active' => ($controllerID=='ezform-community' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> Data Entry Tools', 'url' => '/ezmodules/ezmodule/view?id=1520782910020421400&tab=1520782967056634000&addon=0','active' => ($controllerID=='ezform-version' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> Data Validation Tools', 'url' => '/ezmodules/ezmodule/view?id=1527050077020533300','active' => ($controllerID=='ezmodule' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> Auto Number Configuration', 'url' => '/ezforms2/ezform-autonum/index','active' => ($controllerID=='ezmodule-widget' && $actionID=="index") ? true: false, 'options'=>['title'=>'Auto Number Configuration']],
                ],
                'research-tools'=>[
                    ['label' => '<i class=""></i> Random Codes Generator', 'url' => '/ezforms2/randomization/index','active' => ($controllerID=='randomization' && $actionID=="index") ? true: false],
                ],
                'project-modules' => [
                    ['label' => '<i class="fa fa-compass fa-1x"></i> Project Management System', 'url' => '/ezmodules/ezmodule/view?id=1520785947018301400','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1520785947018301400') ? true: false],
                    ['label' => '<i class="fa fa-street-view fa-1x"></i> Ethic Clearance System', 'url' => '/ezmodules/ezmodule/view?id=1521619318011247300','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1521619318011247300') ? true: false],
                    ['label' => '<i class="fa fa-cogs fa-1x"></i> Project System Settings', 'url' => '/manageproject/step/index?step=1','active' => ($controllerID=='step' && $actionID=="index") ? true: false],
                    
                    ['label' => '<i class="fa fa-files-o fa-1x"></i> Trial Master File Management System', 'url' => '/ezmodules/ezmodule/view?id=1520785809079460000&addon=0&tab=1525012537086811400','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1520785809079460000') ? true: false],
                    ['label' => '<i class="fa fa-money fa-1x"></i> Financial Management System', 'url' => '/ezmodules/ezmodule/view?id=1520807564095312900','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1520807564095312900') ? true: false],
                    ['label' => '<i class="fa fa-vcard fa-1x"></i> Subject Management System', 'url' => '/ezmodules/ezmodule/view?id=1521807350087906600&addon=0&tab=1521807381035975500','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1521807350087906600') ? true: false],
                    ['label' => '<i class="fa fa-users fa-1x"></i> Member Management System', 'url' => '/ezmodules/ezmodule/view?id=1520798323068323400&addon=0&tab=1520798433002763000','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1520798323068323400') ? true: false],
                    ['label' => '<i class="fa fa-thumbs-up fa-1x"></i> Quality Management System', 'url' => '/ezmodules/ezmodule/view?id=1521623387051619400&addon=0&tab=1521623624042594800','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1521623387051619400') ? true: false],
                    ['label' => '<i class="fa fa-random fa-1x"></i> Radomizations', 'url' => '/ezmodules/ezmodule/view?id=1525077575002520500&addon=0&tab=1525077617040511900','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1525077575002520500') ? true: false],
                    ['label' => '<i class="fa fa-hospital-o fa-1x"></i> Electronic Data Capture', 'url' => '/ezmodules/ezmodule/view?id=1524662782058574100','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1524662782058574100') ? true: false],
                    ['label' => '<i class="fa fa-ambulance fa-1x"></i> Adverse Events/Serious Adverse Events', 'url' => '/ezmodules/ezmodule/view?id=1522138126026776400&addon=0&tab=1524020098023355600','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1522138126026776400') ? true: false],
                    ['label' => '<i class="fa fa-list-alt fa-1x"></i> Thai Clinical Trials Registry', 'url' => '/ezmodules/ezmodule/view?id=1520772999024763100&tab=1522151744070407500','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1520772999024763100') ? true: false],
                    ['label' => '<i class="fa fa-calendar fa-1x"></i> EzCalendar', 'url' => '/ezmodules/ezmodule/view?id=1522637073052796200&addon=0&tab=1522640312086431800','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1522637073052796200') ? true: false],
                    ['label' => '<i class="fa fa-globe fa-1x"></i> EzMap', 'url' => '#','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1521619318011247300') ? true: false],
                    //['label' => '<i class="fa fa-line-chart fa-1x"></i> Future additional Module','url'=>'#','active' => ($controllerID=='ezmodule' && (isset($_GET['id'])?$_GET['id']:'') == '1521619318011247300') ? true: false],
                ],
                'system-config'=>[
                    ['label' => '<i class="fa fa-angle-right"></i> Enterprise information', 'url' => '/core/core-options/config','active' => ($controllerID=='core-options' && $actionID=="config") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> Generate', 'url' => '/core/core-generate/index','active' => ($controllerID=='core-generate' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> Media', 'url' => '/core/media/index','active' => ($controllerID=='media' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> File Storage Log', 'url' => '/core/file-storage/index','active' => ($controllerID=='file-storage' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> Options Config', 'url' => '/core/core-options/index','active' => ($controllerID=='core-options' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> Input Fields', 'url' => '/core/core-fields/index','active' => ($controllerID=='core-fields' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> Item Alias', 'url' => '/core/core-item-alias/index','active' => ($controllerID=='core-item-alias' && $actionID=="index") ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> '.Yii::t('appmenu', 'Config email'), 'icon' => 'fa fa-location-arrow', 'url' => ['/manage_user/setting/verify-email'],'active' => ($moduleID=='manage_user' && $controllerID=="setting") ? true: false],
                ],
                'table-fields'=>[
                    ['label' => '<i class="fa fa-angle-right"></i> Profile', 'url' => '/core/tables-fields?table=profile','active' => ($controllerID=='tables-fields' && (isset($_GET['table'])?$_GET['table']:'') == 'profile') ? true: false], 
                ],
                'update-project'=>[
                    ['label' => '<i class="fa fa-refresh"></i> Update Project', 'url' => '/update_project','active' => ($moduleID=='update_project' && $controllerID='default' && $actionID=='index') ? true: false], 
                ],
                'authentication'=>[
                    ['label' => '<i class="fa fa-angle-right"></i> '.Yii::t('chanpan','Assignments'), 'url' => '/admin/assignment','active' => ($controllerID=='assignment') ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> '.Yii::t('chanpan','Roles'), 'url' => '/admin/role','active' => ($controllerID=='role') ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> '.Yii::t('chanpan','Permissions'), 'url' => '/admin/permission','active' => ($controllerID=='permission') ? true: false],
                    ['label' => '<i class="fa fa-angle-right"></i> '.Yii::t('chanpan','Routes'), 'url' => '/admin/route','active' => ($controllerID=='route') ? true: false],
                ]
            
        ];
    }

    public function checkAccessToken(){
        $token = \Yii::$app->request->get('access_token', null);
        if($token != null){
//            $identity = User::findIdentityByAccessToken($token);
            $user = User::find()->where(['auth_key'=>$token])->one();
            if($user == null ){
                throw new UnauthorizedHttpException();
            }
            else{
                CNSocialFunc::autoLogin($user);
            }
        }
    }
}
