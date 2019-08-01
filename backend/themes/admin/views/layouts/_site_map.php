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
<?php 
    $sitemap = isset(Yii::$app->params['sitemap']) ? Yii::$app->params['sitemap'] : '';
    
?> 


<div id="slide-collapse" class="page-sidebar navbar-collapse collapse " >
    <ul class="page-sidebar-menu-tool">
       
      <li class="sidebar-user yamm">
          <?php 
          //appxq\sdii\utils\VarDumper::dump(Yii::$app->params['logo_sitemap']);
            $url = ''; 
            $main_url = \Yii::$app->params['main_url'];
            if(Yii::$app->params['company_name'] == 'nCRC'){
                $url = isset(\Yii::$app->params['frontend_full_url'])?\Yii::$app->params['frontend_full_url']:'';//'https://www.ncrc.in.th';
            }else{
                $url = "https://{$main_url}/";
            }
          ?>
          <div class="dropdown">
              <img style="cursor: pointer;" 
                   data-toggle="dropdown"  
                   title="Main Menu" 
                   data-toggle="tooltip" 
                   data-placement="right" 
                   class="img-rounded" 
                   src="<?= isset(Yii::$app->params['logo_sitemap']) ? Yii::$app->params['logo_sitemap'] : \Yii::getAlias('@storageUrl') . '/images/ncrc.png'?>"/>
              <span class="title"> &nbsp; <?= isset(Yii::$app->params['text_sitemap']) ? Yii::$app->params['text_sitemap'] : 'nCRC';?> </span>
          
              <ul class="dropdown-menu CNDropdown items-sidebar2">
                  <div class="yamm-content">
                       <div class="row" id="items-side-scroll2">
                           <div class="col-md-3 padding-0">
                               <div class="col-md-12 pdl-20 bdr-color">
                                   <?php   
                                       $urlHome  = \Yii::$app->params['main_url'];; //\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
                                        $domain = Yii::$app->params['current_url'];//isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';  
                                        
                                            echo "<div class='CNHeader'>".Yii::t('chanpan','Important Pages')."</div>";
                                            echo cpn\chanpan\widgets\Sitemap::widget([
                                                'items' => $sitemap['important_page'],
                                                'options'=>['class'=>'list-unstyled'],
                                                'encodeLabels' => false,
                                            ]);
                                         
                                    ?>
                               </div>                                
                               <div class="col-md-12 pdl-20 bdr-color">
                                   <?php
                                        echo "<div class='CNHeader'>".Yii::t('chanpan','System tools')."</div>";
                                        echo cpn\chanpan\widgets\Sitemap::widget([
                                            'items' => $sitemap['system-tools'],
                                            'options'=>['class'=>'list-unstyled'],
                                            'encodeLabels' => false,
                                        ]);
                                    ?>
                               </div>
                               <div class="col-md-12 pdl-20 bdr-color">
                                    <?php
                                         echo "<div class='CNHeader'>".Yii::t('chanpan','Data Management Tools')."</div>";
                                       echo cpn\chanpan\widgets\Sitemap::widget([
                                            'items' => $sitemap['data-management-tools'],
                                            'options'=>['class'=>'list-unstyled'],
                                            'encodeLabels' => false,
                                        ]);
                                    ?>  
                                </div>
                           </div>
                           
                           <div class="col-md-5 padding-0 pdl-20 bdr-color">
                               <div class="col-md-12">
                                   <?php
                                       echo "<div class='CNHeader'>".Yii::t('chanpan','Project Modules')."</div>";
                                       echo cpn\chanpan\widgets\Sitemap::widget([
                                            'items' => $sitemap['project-modules'],
                                            'options'=>['class'=>'list-unstyled'],
                                            'encodeLabels' => false,
                                        ]);
                                   ?> 
                               </div> 
                           </div>
                            
                           <div class="col-md-4 padding-0">
                               <div class="col-md-12">
                                   <?php
                                        echo "<div class='CNHeader'>".Yii::t('chanpan','Research Tools')."</div>";
                                        echo cpn\chanpan\widgets\Sitemap::widget([
                                            'items' => $sitemap['research-tools'],
                                            'options'=>['class'=>'list-unstyled'],
                                            'encodeLabels' => false,
                                        ]);
                                    ?> 
                               </div>
                               <?php if(Yii::$app->user->can('administrator')):?>
                               <div class="col-md-12 pdl-20">
                                   <?php
                                        echo "<div class='CNHeader'>".Yii::t('chanpan','System Config')."</div>";
                                        echo cpn\chanpan\widgets\Sitemap::widget([
                                            'items' => $sitemap['system-config'],
                                            'options'=>['class'=>'list-unstyled'],
                                            'encodeLabels' => false,
                                        ]);
                                    ?> 
                               </div>
                               <div class="col-md-12 pdl-20 ">
                                   <?php
                                        echo "<div class='CNHeader'>".Yii::t('chanpan','Table Fields')."</div>";
                                        echo cpn\chanpan\widgets\Sitemap::widget([
                                            'items' => $sitemap['table-fields'],
                                            'options'=>['class'=>'list-unstyled'],
                                            'encodeLabels' => false,
                                        ]);
                                    ?> 
                               </div>
                               <div class="col-md-12 pdl-20 ">
                                   <?php
                                        echo "<div class='CNHeader'>".Yii::t('chanpan','Authentication')."</div>";
                                        echo cpn\chanpan\widgets\Sitemap::widget([
                                            'items' => $sitemap['authentication'],
                                            'options'=>['class'=>'list-unstyled'],
                                            'encodeLabels' => false,
                                        ]);
                                    ?> 
                               </div>
                               <?php endif;?>
                               <div class="col-md-12 pdl-20">
                                    
                                   <?= "<div class='CNHeader'>".Yii::t('chanpan','')."</div>";?> 
                                   
                                   <?php 
                                    $main_url = Yii::$app->params['main_url']; //\backend\modules\core\classes\CoreFunc::getParams('main_url','url');
                                    $current_url = Yii::$app->params['model_dynamic']; //\cpn\chanpan\classes\CNServer::getServerName();
                                    if($main_url != $current_url['url']){
                                      echo cpn\chanpan\widgets\Sitemap::widget([
                                            'items' => $sitemap['update-project'],
                                            'options'=>['class'=>'list-unstyled'],
                                            'encodeLabels' => false,
                                        ]);
                                    }
                                   ?>
                                   <div class="col-md-4 col-xs-4" style="padding: 0;">
                                       <!--<img class="img-responsive" src="<?= Yii::getAlias('@storageUrl') . '/images/ncrc.png' ?>"/>-->
                                       <img   style="width: 100px;" class="img-responsive" src="https://www.ncrc.in.th/img/ncrc.png"/>
                                       
                                   </div>
                                   <div class="col-md-8 col-xs-8" style="padding-left: 3px;">
                                       <div class="text-left">nCRC</div>
                                       <div class="text-left">Version: 1.0.0</div>
                                   </div>
                               </div>
                               
                               
                           </div>
                        </div>
                           
                  </div>
              </ul>
          </div>  
        </li>
       
    </ul>
    <!-- BEGIN SIDEBAR MENU -->  
    
    <?=
    \appxq\sdii\widgets\Sidebar::widget([
        'firstItemCssClass' => 'start',
        'lastItemCssClass' => 'last',
        'items' => Yii::$app->params['sidebar'],
    ]);
    ?>
    <!-- END SIDEBAR MENU -->
    <ul class="page-sidebar-menu-tool">
        <li class="sidebar-toggler-item">
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <div class="fa fa-angle-double-right sidebar-toggler hidden-phone"></div>
            
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        </li>
    </ul>
</div>
 
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
   
    itemsSidebarSiteMap();
    function  getHeightSiteMap() {
        var sidebarHeight = $('.CNDropdown').height(); //- $('.header').height()
//        if ($('.CNDropdown').hasClass("page-footer-fixed")) {
//            sidebarHeight = sidebarHeight - $('.footer').height();
//        }
        return sidebarHeight;
    }
    function  itemsSidebarSiteMap() {
        var itemside = $('#items-side-scroll2');

        if ($('.page-sidebar-fixed').length === 0) {
            return;
        }

        if ($(window).width() >= 992) {
            var sidebarHeight = getHeightSiteMap();

            itemside.slimScroll({
                size: '7px',
                color: '#a1b2bd',
                opacity: .8,
                position: 'right',
                height: sidebarHeight,
                allowPageScroll: false,
                disableFadeOut: false
            });
        } else {
            if (itemside.parent('.slimScrollDiv').length === 1) {
                itemside.slimScroll({
                    destroy: true
                });
                itemside.removeAttr('style');
                $('.items-sidebar2').removeAttr('style');
            }
        }

    }
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>