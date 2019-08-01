<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    
     
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php

    NavBar::begin([
        'brandLabel' => Yii::$app->params['proj_aconym'],
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menuList = \backend\modules\core\classes\CoreFunc::getParams('project_home_menu', 'url');
    $menuList = stripslashes($menuList);
    $menuList = json_decode($menuList,true);
    
    $mainUrl = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';
    

    $nav = [];
    
    if(!$menuList){
        // default if no config found
        $menuItems =[
            'label' => 'Select Project',
            'items' => [
                 ['label' => 'Enter to project', 'url' => ['/site/index?proj_h=0']],
                 '<li class="divider"></li>', 
                 ['label' => 'Go to Portal', 'url' => "https://{$mainUrl}"],
            ],
        ];
            //['label' => 'Enter to project', 'url' => ['/site/index?proj_h=0']],
        //];
    }else{
        
        $menuItems[] = ['label' => 'Enter to project', 'url' => ['/site/index?proj_h=0']];
        
        //print_r($menuList);exit();
        //print_r(Yii::$app->params['main_url']);exit();
        $menuDropDown = [
                'label'=>'Enter to menu',
                'items'=>[] 
        ];
        if(\Yii::$app->user->id == '1'){
           foreach($menuList['menus'] as $key1=>$v){
                $url = $v['url'];
                $mainUrl = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';
                $currentUrl = isset(Yii::$app->params['current_url']) ?Yii::$app->params['current_url'] : '';
                $modelForm = ['current_url'=>$currentUrl,'main_url'=>"https://{$mainUrl}"];
                $path = [];
                foreach ($modelForm as $key => $value) {
                        $path["{" . $key . "}"] = $value;
                }
                $urls = strtr($url, $path); 

                $t = ['label' => $v['label'], 'encode' => FALSE, 'url' => $urls];
                $menuDropDown['items'][$key1] = $t;


           }//end foreach
        $nav[] = $menuDropDown;
        $menuItems  = $nav;
        //\appxq\sdii\utils\VarDumper::dump($menuItems);
        }
        
//        
//        //print_r($menuList);exit();
//        //print_r(Yii::$app->params['main_url']);exit();
//        $menuDropDown = [
//                'label'=>'Enter to menu',
//                'items'=>[] 
//        ];
//        foreach ($menuList["menus"] as $key => $value){
//            $icon = isset($value["icon"]) ? " <i class='".$value['icon']."'></i> ": "";
//            $label = isset($value["label"]) ? Yii::t('appmenu', $value["label"]): "None";
//            $url = isset($value["url"]) ? $value["url"] : "#";
//            
//             if(cpn\chanpan\classes\CNServerConfig::isPortal()){      
//                $url = str_replace('$mainUrl' , '',$mainUrl); 
//                $t = ['label' => $icon.$label, 'encode' => FALSE, 'url' => "https://{$url}"]; 
//                $menuDropDown['items'][$key] = $t; 
//             }else{ 
//                $url = str_replace('$mainUrl' , '',$url);
//                $t = ['label' => $icon.$label, 'encode' => FALSE, 'url' => strpos($url, $mainUrl) !== false ? $url :[$url]];
//                $menuDropDown['items'][$key] = $t;
//             }
//                // $url = str_replace('$mainUrl' , '',$url);
////            var_dump($url); exit();
////            $t = ['label' => $label];
////            $t = ['label' => $icon.$label, 'encode' => FALSE, 'url' => strpos($url, $mainUrl) !== false ? $url :[$url]];
//            if( isset($value["visible_role"]) ){
//                $t['visible'] = (Yii::$app->user->can( $value["visible_role"] ) || $urlMain['url'] != $main_url);
//            }
//            if($key == 0){
//                $t['active'] = (Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index') ? true : false;
//            }
//                //array_push($nav ,$menuDropDown );
//        }
//                $nav[] = $menuDropDown;
//        //\appxq\sdii\utils\VarDumper::dump($nav);
//        $menuItems  = $nav;

    }


    $ezf_content = \appxq\sdii\utils\SDUtility::string2Array(Yii::$app->params['web_content_form']);
    $ezf_id = $ezf_content[0];
    $getProject = cpn\chanpan\classes\utils\CNProject::getProject();
    if (!empty($getProject)) {
        $myproject = \backend\modules\manageproject\classes\CNSettingProjectFunc::MyProjectByidNoUser($getProject['data_id']);

    }
    $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
    $model_menu = \backend\modules\subjects\classes\SubjectManagementQuery::getMenu($modelEzf->ezf_table, 0,$myproject[0]['sitecode']);

    Yii::$app->params['navbar'] = [];

    $menu_ezform = [];
    $tab = 0;
    foreach ($model_menu as $key => $value) {
        if ($key == 0) {
            $tab = isset($_GET['tab']) ? $_GET['tab'] : $value['id'];

        }
        $modelSubmenu = \backend\modules\subjects\classes\SubjectManagementQuery::getMenu($modelEzf->ezf_table, $value['id'],$myproject[0]['sitecode']);

        if ($modelSubmenu) {
            $subItems = [];
            $subId = [];

            foreach ($modelSubmenu as $subKey => $subValue) {

                $subItems[] = [
                    'label' => $subValue['menu_name'],
                    'url' => Url::to(['/site/index', 'tab'=> $subValue['id']]),
                    'active' => $tab == $subValue['id'],

                ];
                $subId[] = $subValue['id'];
            }
            $menu_ezform[] = [
                'label' => Yii::t('app', $value['menu_name']),
                'url' => '#',
                'items' => $subItems,
                'dropDownOptions' => ['id' => \appxq\sdii\utils\SDUtility::getMillisecTime()],
                'active' => in_array($tab, $subId),

            ];
        } else {
            $menu_ezform[] = ['label' => Yii::t('app', $value['menu_name']), 'encode' => FALSE, 'url' => ['/site/index', 'tab'=> $value['id']], 'active' => ($tab == $value['id'])];
        }
    }

    Yii::$app->params['navbar'] = \yii\helpers\ArrayHelper::merge(Yii::$app->params['navbar'], $menu_ezform);

    //\appxq\sdii\utils\VarDumper::dump($subItems);
    echo \yii\bootstrap\Nav::widget([
        'items' => Yii::$app->params['navbar'],
        'options' => ['class' => 'navbar-nav'],
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);

    echo '<div class="navbar-text pull-right">';
    echo \lajax\languagepicker\widgets\LanguagePicker::widget([
        'skin' => \lajax\languagepicker\widgets\LanguagePicker::SKIN_DROPDOWN,
        'size' => \lajax\languagepicker\widgets\LanguagePicker::SIZE_SMALL
    ]);
    echo '</div>';
    NavBar::end();
    $getProject = cpn\chanpan\classes\utils\CNProject::getProject();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; 2018 <?= $getProject['aconym'] ?></p>
        <p class="pull-right">
            <?php
            if (Yii::$app->user->isGuest) {
                echo Html::a('Login',Url::to(['/user/login']), [
                    'class' => 'btn btn-info btn-sm btn-login',
                ]);
            } else {
                echo Html::a('Logout (' . Yii::$app->user->identity->username . ')',Url::to(['/site/logout']), [
                    'class' => 'btn btn-warning btn-sm btn-logout',
                ]);

            }

            echo '<div class="col-md-3 pull-right" align="right">';
            echo 'Powered by nCRC at ';
            echo '<a href="https://www.ncrc.in.th/">www.ncrc.in.th</a>';
            echo '</div>';


            ?>

        </p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
