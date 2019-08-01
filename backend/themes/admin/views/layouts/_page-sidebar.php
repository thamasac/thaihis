<div id="slide-collapse" class="page-sidebar navbar-collapse collapse " >
    <ul class="page-sidebar-menu-tool">
      <li class="sidebar-user" style="height: 51px; background-color: #4dadf7;">
          <?php 
            $url = '';
            $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
            $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
            if(Yii::$app->params['company_name'] == 'nCRC'){
                $url = 'https://www.ncrc.in.th';
            }else{
                $url = "https://{$main_url}/";
            }
          ?>
            <a href="<?= $url?>">
                <img class="img-rounded" src="<?= Yii::getAlias('@storageUrl') . '/images/ncrc.png' ?>"/> 
                <span class="title"> &nbsp; nCRC </span>
            </a>
            
        </li>
        
        <li class="sidebar-toggler-item">
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <div class="fa fa-bars sidebar-toggler hidden-phone"></div>
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        </li>
    </ul>
    <!-- BEGIN SIDEBAR MENU -->  
    <?php
    $moduleID = '';
    $controllerID = '';
    $actionID = '';

    if (isset(Yii::$app->controller->module->id)) {
        $moduleID = Yii::$app->controller->module->id;
    }
    if (isset(Yii::$app->controller->id)) {
        $controllerID = Yii::$app->controller->id;
    }
    if (isset(Yii::$app->controller->action->id)) {
        $actionID = Yii::$app->controller->action->id;
    }

    backend\components\AppComponent::sidebarMenu($moduleID, $controllerID, $actionID);
    ?>
    <?=
    \appxq\sdii\widgets\Sidebar::widget([
        'firstItemCssClass' => 'start',
        'lastItemCssClass' => 'last',
        'items' => Yii::$app->params['sidebar'],
    ]);
    ?>
    <!-- END SIDEBAR MENU -->
</div>