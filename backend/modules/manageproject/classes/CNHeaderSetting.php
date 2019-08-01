<?php

namespace backend\modules\manageproject\classes;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;


class CNHeaderSetting extends \yii\base\Widget{
   private $dataid, $dataicon;
   private $imgPath;
   private $noImage;
   private $projectName;
   private $roleName;
   private $sitecode;
   /**
     * @inheritdoc
     * @return CNHeaderSetting the newly created [[CNHeaderSetting]] instance.
     */
    public static function classNames() {
        return Yii::createObject(CNHeaderSetting::className());  
    }
    public function setImagePath($imgPath){
        $this->imgPath = ($imgPath != '') ? $imgPath : Yii::getAlias('@storageUrl') ;
        return $this;
    }
    public function setNoImage($noImage){
        $this->noImage = $noImage;
        return $this;
    }
    public function setDataId($dataid){
        $this->dataid=$dataid;
        return $this;
    }
    public function setDataIcon($dataicon){
        $this->dataicon=$dataicon;
        return $this;
    }
    public function setProjectName($projectName){
        $this->projectName=$projectName;
        return $this;
    }
    public function setRoleName($roleName){
        $this->roleName=$roleName;
        return $this;
    }
    public function setSitecode($sitecode){ 
        $this->sitecode=$sitecode;
        return $this;
    }
    
    public function shortModule(){
        
        $mocStr = isset(Yii::$app->params['shot_menu_head']) ? Yii::$app->params['shot_menu_head']  : '';
        $mocArr = explode(",", $mocStr);
        $moc_arr = [];
        foreach($mocArr as $m){
            if($m != ''){
                array_push($moc_arr, $m);
            }
        }
        
        $output=[];
        if(!isset(Yii::$app->session['short-module-title'])){
            foreach($moc_arr as $k=>$v){
                $module = \backend\modules\ezmodules\models\Ezmodule::find()
                ->select(['ezm_name','ezm_short_title','ezm_id','icon_base_url','ezm_icon','ezm_link'])
                ->where("ezm_id=:ezm_id AND active=1",[':ezm_id'=>$v])->asArray()->one();
                if($module){
                    //\appxq\sdii\utils\VarDumper::dump($module);
                    array_push($output, $module);
                }
            }
            Yii::$app->session['short-module-title'] = $output;
            return Yii::$app->session['short-module-title'];
        }else{
            //unset(Yii::$app->session['short-module-title']);
            $module = Yii::$app->session['short-module-title'];
        }
        
        return $module;

    }

 public static function setShortModule(){
        unset(Yii::$app->session['short-module-title']);
//        $output = [];
    }

    
    
   // 
   public  function headerBar(){
       $this->CssRegister();
       return Html::tag('div', $this->renderRow(), ['class'=>'alert','style'=>'margin-bottom: 0px;word-wrap: break-word;background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460;']);
   }

   public function getThemes(){
       return CNSettingProjectFunc::getThemes();
   }
  
   public function buildUi(){
       $html = '';
       
       $html .= Html::beginTag('div',['class'=>'col-md-2 col-sm-2 col-xs-2 cn-header-img' ]);
            $html .= $this->renderImage();
       $html .= Html::endTag('div');       
       $html .= Html::beginTag('div',['class'=>'col-md-10 col-sm-10 col-xs-10']);
            $html .= $this->renderContent();
       $html .= Html::endTag('div');
       $this->CssRegister();
       
       echo $html;
   }
 
   public function renderImage(){
            $image = Html::img($this->dataicon['data_create']['projecticon'], [
                'class'=>'img-rounded' ,'title'=>Yii::t('chanpan', 'Project Settings'),
                'width'=>'100%', 'height'=>'100%'
           ]);
       $project_setup_redirect_url = isset(\Yii::$app->params['project_setup_redirect_url'])?\Yii::$app->params['project_setup_redirect_url']:'';     
       $url = Url::to([$project_setup_redirect_url]);     
       $link = Html::a($image, $url, ['id'=>(\Yii::$app->user->can('admin'))?'':'','style'=>'text-decoration: none;']);
      return $link;
   }
   public function renderContent(){
       $html = '';
       $html .= $this->renderProjectName();
       $html .= $this->renderSiteCode();
       $html .= $this->renderRole();
       return $html;
       
   }
   public function renderProjectName(){
       $dropdown_change_page = $this->renderDropdownByAction();
       $html = "
            <dl class='dl-horizontal dl dl-header' style='margin-bottom: 8px'>
                <dt style='text-align: left' class='dt inline-cn-header'>Project:</dt> 
                <dd class='txt-color dd inline-cn-header'>{$this->projectName} {$dropdown_change_page}</dd>
                
            </dl>
        ";        
       return $html;
   }
   
   public function renderDropdownByAction(){
        
        $html ='';
        $items = [];
        $project_all = \cpn\chanpan\classes\utils\CNProject::get_project_all();
        $email = \cpn\chanpan\classes\CNUser::getEmail();
        if($email == 'chanpan.nuttaphon1993@gmail.com'){
            //\appxq\sdii\utils\VarDumper::dump($project_all);
        }
        
        $moduleId = (isset(Yii::$app->controller->module->id) && Yii::$app->controller->module->id != 'app-backend') ? Yii::$app->controller->module->id : '';
        $controllerId = isset(Yii::$app->controller->id) ? Yii::$app->controller->id : '';
        $actionId = isset(Yii::$app->controller->action->id) ? Yii::$app->controller->action->id : '';
        $status = 0;
        $viewId = \Yii::$app->request->get('id', '');
        
        if($project_all){
          $output_data = [];
          $output_projectacronym = [];
          $projectacronym = '';
          foreach($project_all as $k=>$v){
              $projectacronym = $v['projectacronym'];
              if(!in_array($projectacronym, $output_projectacronym)){
                array_push($output_projectacronym, $v['projectacronym']);
                $output_data[$k] = ['url'=>$v['url'],'projectacronym'=>$v['projectacronym']];
              }
          }
        foreach($output_data as $k=>$v){
            $url = "https://{$v['url']}";
           if($moduleId == 'ezforms2'){
               $url = "{$url}/site/auto-login?param_url=/ezforms2/ezform/index&token=";
               
               //if(!in_array($url, $items['url'])){
                   $items["{$url}"] = $v['projectacronym'].' '.$moduleId;
                    $status=1;
              // }
               
           }else if($moduleId == 'ezmodules' && $controllerId == 'default'){
               $url = "{$url}/site/auto-login?param_url=/ezmodules/default/index&token=";
               $items["{$url}"] = $v['projectacronym'].' '.$moduleId;
               $status=1;
           }
           else if($moduleId == 'ezforms2' && $controllerId == 'data-lists'){
               $url = "{$url}/site/auto-login?param_url=/ezforms2/data-lists/index&token=";
               $items["{$url}"] = $v['projectacronym'].' '.$moduleId;
               $status=1;
           }else{
               $status = 0;
           }
        }
        }
       if($status == 1){
          $html = \yii\bootstrap\Html::dropDownList('dropdown_redirect', '1', $items, ['class'=>'btn btn-xs btn-default btn-dropdown-redirect-project', 'prompt'=>'Please Select Items']);
       } 
       $view = $this->getView();
       $html .= $view->registerJs("
           $('.btn-dropdown-redirect-project').on('change', function(){
                let url_redirect = $(this).val();
                
                let url = '/site/get-token';
                $.get(url, function(data){
                    url_redirect += data; // data['data']['code'];
                    location.href = url_redirect;
                });
                return false; 
           });

       ");
       if($project_all){
           return $html;
       }
   }
   
   public function renderSiteCode(){
       $site_switch = isset(Yii::$app->user->identity->profile->site_switch) && Yii::$app->user->identity->profile->site_switch !=''?Yii::$app->user->identity->profile->site_switch:'00';
       $sitecode = isset(\Yii::$app->user->identity->profile->sitecode)?\Yii::$app->user->identity->profile->sitecode:'00';
       $original_site = isset(\Yii::$app->user->identity->profile->original_site)?\Yii::$app->user->identity->profile->original_site:$sitecode;
       $dropdown = '';
       $html = '';
        if($site_switch){
//            $site_switch_arr = \appxq\sdii\utils\SDUtility::string2Array($site_switch);
//            array_push($site_switch_arr, $original_site);
            $user_id = \cpn\chanpan\classes\CNUser::getUserId();
            $sites =[];
            try{
              $site_arr = (new \yii\db\Query())->select('*')->from('zdata_switch_site2')->where('user_id=:user_id AND rstat not in(0,3)',[':user_id'=>$user_id])->all();
                foreach($site_arr as $k=>$v){
                    array_push($sites, $v['switch_site']);
                }
                if(isset($site_arr[0]['original_site'])){
                    array_push($sites, $site_arr[0]['original_site']);
                }
                
                $data_site_arr = (new \yii\db\Query())
                    ->select(["site_name","CONCAT(site_detail,' (',site_name,')') as site_detail"])->from('zdata_sitecode')->where(['site_name'=>$sites])->all();
                $data_site_arr_map = \yii\helpers\ArrayHelper::map($data_site_arr, 'site_name', 'site_detail');
                $dropdown = \yii\bootstrap\Html::dropDownList('dropdown_redirect', $sitecode, $data_site_arr_map, ['class'=>'btn btn-xs btn-default btn-dropdown-switch-site', 'prompt'=> Yii::t('chanpan','Select Site to Switch')]);


            } catch (\yii\db\Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            }
            
            

        }
        $link_plus = Html::a("<i class='fa fa-plus'></i>", ['/site/switch-site'], ['class'=>'btn btn-xs btn-success']);
        
       $html .= '
            <dl class="dl-horizontal dl dl-header" style="margin-bottom: 8px">
                <dt style="text-align: left" class="dt inline-cn-header">Site:</dt>
                <dd class="txt-color dd inline-cn-header">'.$this->sitecode.'</dd>
                <div style="" class="inline-cn-header">
                '.$dropdown.' '.$link_plus.'
                </div>
            </dl>
            
        ';  
       //<dd class="txt-color dd">'.$this->sitecode.' '.$dropdown.' '.$link_plus.'</dd>
       $view = $this->getView();
       $html .= $view->registerJs("
           $('.btn-dropdown-switch-site').on('change', function(){
                let site_val = \$(this).val();
                if(site_val == ''){return false;}
                let url = '/user/settings/switch-site';
                let site =  \$(this).val();
                $.post(url, {site:site}, function(result){
                    console.log(result);
                    ".\appxq\sdii\helpers\SDNoty::show('result.message', 'result.status').";
                    setTimeout(function(){location.reload(); },1000);    
                });
                
                return false; 
           });

       ");
      
       return $html;
   }
   public function renderRole(){
       $role_name = isset($this->roleName) && $this->roleName != '' ?$this->roleName: Yii::t('chanpan','Not yet assigned');
       $html = '
            <dl class="dl-horizontal dl dl-header" style="margin-bottom: 8px">
                <dt style="text-align: left" class="dt inline-cn-header">Role:</dt>
                <dd class="txt-color dd inline-cn-header">'.$role_name.'</dd>
            </dl>
        ';        
       return $html;
   }
   public function buildUiIcon(){
       
       $view = $this->getView();
       \backend\modules\ezforms2\assets\EzfGenAsset::register($view);
       \backend\modules\ezforms2\assets\EzfColorInputAsset::register($view);
       \backend\modules\ezforms2\assets\DadAsset::register($view);
       \backend\modules\ezforms2\assets\EzfToolAsset::register($view);
       \backend\modules\ezforms2\assets\EzfTopAsset::register($view);
       \backend\modules\ezforms2\assets\ListdataAsset::register($view);
       
       $html = "";
       $getThemes = Yii::$app->params['themes'];
       $currentUrl = isset(Yii::$app->params['current_url']) ? Yii::$app->params['current_url'] : '';
       $portalUrl = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';        
       $tid = '';
        if (!empty($getThemes)) {
            $tid = $getThemes['id'];
        }
        
        
        $imgEdit = Html::img('@web/img/edit.png', ['style' => 'width:25px;height:25px;top: 0;', 'class' => 'img ', 'title' => Yii::t('chanpan', 'Edit short menu')]);
        $imgSetting = Html::img('@web/img/setting1.png', ['style' => 'width:25px;height:25px;top: 0;', 'class' => 'img ', 'title' => Yii::t('chanpan', 'Project Settings')]);
        $imgTheme = Html::img('@web/img/themes3.png', ['style' => 'width:25px;height:25px;top: 0;', 'class' => 'img ', 'title' => Yii::t('chanpan', 'Themes Settings')]);
        $imgClone = Html::img('@web/img/clone.png', ['style' => 'width:25px;height:25px;top: 0;filter: grayscale(100%);
-webkit-filter: grayscale(100%);
-moz-filter: grayscale(100%);
-o-filter: grayscale(100%);
-ms-filter: grayscale(100%);', 'class' => 'img ', 'title' => Yii::t('chanpan', 'Project Cloner')]);
        $urlThemes = yii\helpers\Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1528993539066325000&dataid=' . $tid . '&modal=modal-themes&reloadDiv=modal-divview-1528993539066325000&db2=0']);
        if($currentUrl != $portalUrl){
            $html .= "<div class='pull-right header-setting'>";
            $html .= Html::a('&nbsp;'.$imgEdit, '#', ['class' => 'btnEditShotModule', 'id'=>'btnEditShotModule']);
            $html .= Html::a('&nbsp;'.$imgSetting, yii\helpers\Url::to(['/site/project-setting']). ' ',['class'=>'btnSettingProjects', 'id'=>'btnSettingProjects']) ;
            $html .= Html::a('&nbsp;'.$imgClone, '#', ['class' => 'btnCloneProjects', 'id' => 'btnCloneProjects', 'id'=>'btnCloneProjects']);
            $html .= Html::a('&nbsp;'.$imgTheme, $urlThemes, ['class' => 'btnGetTheme', 'id' => 'btnEditThemes', 'id'=>'btnEditThemes']);
            $html .= "</div>";
        }
        $html .= "<div class='clearfix'></div>";
        $html .= yii\bootstrap\Modal::widget([
                'id'=>'modal-create-project',
                'size'=>'modal-xl',
                'options'=>['tabindex' => false]
            ]);
          $shot_menu =  \backend\modules\core\classes\CoreFunc::getParams('shot_menu_head','header menu');
        if($shot_menu){
            $html .= "<div class='header-setting pull-right' style=''>";   
                $html .= "<div id='short-module-header-bar' style='margin-top:10px;margin-left: 4px;display: flex;flex-wrap: wrap'>";
                $model = $this->shortModule();
                //\appxq\sdii\utils\VarDumper::dump($model);
                foreach($model as $k=>$m){
                    $ezmShortTitle = \cpn\chanpan\classes\utils\CNUtils::lengthName($m['ezm_short_title'], 3);
                    $noImg = Yii::getAlias('@storageUrl') . '/ezform/img/no_icon.png';
                    $img = ($m['ezm_icon'] != '') ? "{$m['icon_base_url']}/{$m['ezm_icon']}" : $noImg;
                    $html .= "
                        <a title='{$m['ezm_name']}' href='/ezmodules/ezmodule/view?id={$m['ezm_id']}'>  
                          <img title='{$m['ezm_name']}' src='{$img}' style='width: 45px;' class='img img-responsive img-rounded'>    
                            <div title='{$m['ezm_name']}' class='mt-5'>{$ezmShortTitle}</div>              
                        </a> 
                    ";
                    if($k < count($model)-1){
                        $html .= "
                            &nbsp;<li class='fa fa-arrow-right' style='margin-top:15px;'></li>&nbsp; 
                        ";
                    }
                }
                $html .= '</div>';
                
                //$html .= '<div class="pull-right" style="margin-left:5px">'.Html::button("<i class='fa fa-pencil'></i>", ['class'=>'btn btn-primary btnEditShotModule']).'</div>';
            $html .= "</div>";
            
        }
//        $shot_menu =  \backend\modules\core\classes\CoreFunc::getParams('shot_menu_head','xxx');
//        if($shot_menu){
//            $html .= "<div style='margin-top:2px;margin-left: 4px;display: flex;' class='icon-rights'>";            
//                $html .= $shot_menu;
//            $html .= "</div>";
//        } 
        

        $this->JsRegister();
        
            echo $html; 
        
    } 
   public function CssRegister(){
       $view = $this->getView();
       $view->registerCss("
            .cn-header-img{
                width: 100px; padding-right: 0;
            }
            .dl-header dt {
                width: 60px;
              }
              .dl-header dd {
                margin-left: 10px;
              }
              .inline-cn-header {
                display: inline-block
              }
            
            @media (max-width: 480px) {
              .cn-header-img{
                  position: absolute;width: 64px;padding-right: 0;right: 10px;
                }
            }
       
           #btnEditShotModule{
             -moz-filter: grayscale(100%);
            /* IE */
            filter: progid:DXImageTransform.Microsoft.BasicImage(grayscale=1);
            filter: gray;
            /* Chrome, Safari */
            -webkit-filter: grayscale(1);
            /* Firefox */
            filter: grayscale(1);
           }   
        ");
   }
   public function JsRegister(){
       $myproject = Yii::$app->params['my_project'];//\cpn\chanpan\classes\utils\CNProject::getMyProject(); 
       //\appxq\sdii\utils\VarDumper::dump($myproject['data_create']['id']);
       
       $ezfId='1552475744027480800';
       $url =  \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform?ezf_id='.$ezfId.'&modal=modal-create-project&dataid='.$myproject['data_create']['id']]);
       $mainStart = '';
       $mainUrl = \backend\modules\core\classes\CoreFunc::getParams('start_project', 'project');
        if (empty($mainUrl)) {
            $mainStart = "/ezmodules/ezmodule/view?id=1521647584047559700&addon=0&tab=1528945511006792400";
        } else {
            $mainStart = "{$mainUrl}";
        }
        $shortID = isset(\Yii::$app->params['model_dynamic']['data_id']) ? \Yii::$app->params['model_dynamic']['data_id'] : '';  
        
       $view = $this->getView();
       $view->registerCss("
              .header-setting a{
                text-decoration:none;
              }
              .dl-header dt {
                    width: 60px;
              }
              .dl-header dd {
                margin-left: 10px;
              }
              .inline-cn-header {
                display: inline-block
              }

        ");
       $js = "
        var showCreateProject = 'showCreateProject';
        var frm_str = 'ezform-1523071255006806900'; 
         

        function initShortModuleToHeaderBar(){
            let url = '/site/view-short-module';
            let module = '#short-module-header-bar';
            $(module).html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $.get(url, function (data) {
                $(module).html(data);
            });
        }
        //initShortModuleToHeaderBar();
        function updateStatus(){
            let status_form_str = 'ez1523071255006806900-status_form';
            $('#'+status_form_str).val('update');        
            return false;
        }
        function changeButtonText(){
            $('button[type=\"submit\"][value=\"1\"]').html('Update');
            return false;
        }
        function btnOnClick(){
            //setTimeout(function(){               
                $('button[type=\"submit\"][value=\"1\"]').on('click', function(e){
                    e.preventDefault();
                    let url = '". Url::to(['/manageproject/center-project/update'])."';
                    let frm = $('#'+frm_str).serialize();
                    let id = $('#ezform-1523071255006806900').attr('data-dataid');
                    setTimeout(function(){
                        $.post(url,{id:id}, function(data){
//                            console.log(data);return false;
                            location.href = data;
                        });
                    }, 2000);
                    return false;
                }); 
                

            //}, 500);
            
            setTimeout(function(){
                $('.btnBackupProjectHeaderShort').on('click', function(){
                    let id = '".$shortID."';
                    let url = '/manageproject/backup-restore/backup';
                    

                yii.confirm('Backup file Project', function(){
                    //onLoadings('body');  
                    $.post(url,{id:id}, function(data){
                       //hideLoadings('body');
                       if(data.status == 'success'){
                           ".\appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')."
                           let param = data['data']['url']+'/'+data['data']['path']+'/'+data['data']['file_name'];
                           console.log(param);
                           location.href = param;
                           let uri = '/manageproject/backup-restore/download';
                           $.get(uri,{params:data['data']}, function(data){
                               console.log(data);
                           });

                       } 
                    });
                  });


                    return false;
                });
            },1000);
            return false;
        }  
         
        loadUrl=function(){
            $('#modal-create-project .modal-content').empty();
            $('#modal-create-project .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');  
            
              $.get('".$url."', function(data){
                 $('#modal-create-project .modal-content').html(data);   
                 $('#modal-create-project').modal('show');
                 setTimeout(function(){
                    updateStatus();
                    changeButtonText();
                    btnOnClick();//on submit
                    

                    let btnBackupProjectHeaderShortMenu = '#ezform-1523071255006806900 .modal-header .btn-auth-view';
                    $(btnBackupProjectHeaderShortMenu).removeClass('btn-info');
                    $(btnBackupProjectHeaderShortMenu).addClass('btn-default btn-xs btnBackupProjectHeaderShort');
                    $(btnBackupProjectHeaderShortMenu).html('<i class=\"fa fa-refresh\"></i> Backup');
                    //model_dynamic
                    domHtml();
                    

                },1000);
              }).fail(function(err){
                console.error(err);
              });
            return false;  
        } 
        $('.btnSettingProject').on('click', function(){
              loadUrl();
              return false;
        }); 
        //loadUrl();
        
        $('.btnEditShotModule').on('click',function(){ 
            let modal = '#modal-create-project';
            $(modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $(modal).modal('show');
            let url = '/site/short-module';
            $.get(url, function(data){
                $(modal+' .modal-content').html(data); 
            });

            return false;
        });


        $('#btnShowProjectDetail').on('click', function(){
            let modal ='#modal-create-project';
            $(modal).modal('show');
            $(modal+' .modal-content').html('<i class=\"fa fa-spinner fa-spin fa-fw\"></i>');
            let url = '/site/show-project-detail';
            $.get(url,function(data){
                $(modal+' .modal-content').html(data); 
            });
            return false;
        });
        
       

       ";
       $view->registerJs($js);
       
   }
}
