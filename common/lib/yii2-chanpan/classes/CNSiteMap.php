<?php
 
namespace cpn\chanpan\classes;
use yii\helpers\Url;
use Yii;
class CNSiteMap {
    public static function dashboard(){
        $domain = isset(Yii::$app->params['current_url']) ? Yii::$app->params['current_url'] : CNServerConfig::getDomainName();//\cpn\chanpan\classes\CNServer::getDemain();
        $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        return "
            <li style='list-style: none;'>
                <a href='https://".$main_url."' style='text-decoration: none;'>
                    <span><i class='fa fa-university'></i> Dashboard</span>
                </a>
            </li>
        ";
    }
    public static function navTeadTitle($icon='fa fa-archive', $message){
        $head="
            <h3 class='ncrc-menu-title'>
            <i class='$icon'></i> ".Yii::t('chanpan', $message)."</h3> 
        ";
        echo $head;
    }
    /**
     * 
     * @param type $options array example $options=['name'=>'Ezform' , 'url'=>'url', 'icon'=>'fa fa-home'];
     */
    public static function navbar($options){
        $nav = "<ul class='ncrc-ul'>";
            foreach($options as $o){
                $nav .= "<li>";
                    $nav .= "
                            <a href='".Url::to([$o['url']])."'>
                                <span><i class='".$o['icon']."'></i> ".Yii::t('chanpan',$o['name'])."</span>
                            </a>
                        ";
                $nav .= "</li>";
            }
        $nav .= "</ul>";
        return $nav;
    }
    public static function navSystemTools(){
         
         $nav = [
            ['name' => 'EzForm', 'url' => '/ezforms2/ezform/index', 'icon' => ''],
            ['name' => 'EzForm version', 'url' => '/ezforms2/ezform-version/index', 'icon' => ''],
            ['name' => 'EzModule', 'url' => '/ezmodules/ezmodule/index', 'icon' => ''],
            ['name' => 'EzWidget', 'url' => '/ezmodules/ezmodule-widget/index', 'icon' => ''],
        ];
        return CNSiteMap::navbar($nav);
    }
    public static function navDataManagementTools(){
         $nav = [
            ['name' => 'Query Tools', 'url' => '/ezforms2/ezform-community/index', 'icon' => ''],
            ['name' => 'Data Entry Tools', 'url' => '/ezforms2/ezform-version/index', 'icon' => ''],
            ['name' => 'Data Validation Tools', 'url' => '/ezmodules/ezmodule/index', 'icon' => ''],
            ['name' => 'Auto Number Configuration', 'url' => '/ezmodules/ezmodule-widget/index', 'icon' => ''],
        ];
        return CNSiteMap::navbar($nav);
    }
    
    public static function navResearchTools(){
         $nav = [
            ['name' => 'Random Codes Generator', 'url' => '/ezforms2/randomization/index', 'icon' => ''],
//            ['name' => 'Data Entry Tools', 'url' => '/ezforms2/ezform-version/index', 'icon' => ''],
//            ['name' => 'Data Validation Tools', 'url' => '/ezmodules/ezmodule/index', 'icon' => ''],
//            ['name' => 'Auto Number Configuration', 'url' => '/ezmodules/ezmodule-widget/index', 'icon' => ''],
        ];
        return CNSiteMap::navbar($nav);
    }
    
    public static function navProjectModules(){
         $nav = [
            ['name' => 'Project Management System', 'url' => '/ezmodules/ezmodule/view?id=1520785947018301400', 'icon' => ''],
            ['name' => 'Ethic Clearance System', 'url' => '/ezmodules/ezmodule/view?id=1521619318011247300', 'icon' => ''],
            ['name' => 'Project System Settings', 'url' => '/manageproject/step/index?step=1', 'icon' => ''],
            ['name' => 'Trial Master File Management System', 'url' => '/ezmodules/ezmodule/view?id=1520785809079460000&addon=0&tab=1525012537086811400', 'icon' => ''],
            ['name' => 'Financial Management System', 'url' => '/ezmodules/ezmodule/view?id=1520807564095312900', 'icon' => ''],
            ['name' => 'Subject Management System', 'url' => '/ezmodules/ezmodule/view?id=1521807350087906600&addon=0&tab=1521807381035975500', 'icon' => ''],
            ['name' => 'Member Management System', 'url' => '/ezmodules/ezmodule/view?id=1520798323068323400&addon=0&tab=1520798433002763000', 'icon' => ''],
            ['name' => 'Quality Management System', 'url' => '/ezmodules/ezmodule/view?id=1521623387051619400&addon=0&tab=1521623624042594800', 'icon' => ''],
            ['name' => 'Radomizations', 'url' => '/ezmodules/ezmodule/view?id=1525077575002520500&addon=0&tab=1525077617040511900', 'icon' => ''],
            ['name' => 'Electronic Data Capture', 'url' => '/ezmodules/ezmodule/view?id=1524662782058574100', 'icon' => ''],
            ['name' => 'Adverse Events/Serious Adverse Events', 'url' => '/ezmodules/ezmodule/view?id=1522138126026776400&addon=0&tab=1524020098023355600', 'icon' => ''],
            ['name' => 'Thai Clinical Trials Registry', 'url' => '/ezmodules/ezmodule/view?id=1520772999024763100&tab=1522151744070407500', 'icon' => ''],
            ['name' => 'EzCalendar', 'url' => '/ezmodules/ezmodule/view?id=1522637073052796200&addon=0&tab=1522640312086431800', 'icon' => ''],
            ['name' => 'EzMap', 'url' => '', 'icon' => ''],
            ['name' => 'Future additional Module', 'url' => '', 'icon' => ''],
        ];
        return CNSiteMap::navbar($nav);
    }
    
    public static function navSystemConfig(){
         $nav = [
            ['name' => 'Enterprise information', 'url' => '/core/core-options/config', 'icon' => ''],
            ['name' => 'Generate', 'url' => '/core/core-generate', 'icon' => ''],
            ['name' => 'Media', 'url' => '/core/media/index', 'icon' => ''],
            ['name' => 'File Storage Log', 'url' => '/core/file-storage/index', 'icon' => ''],
            ['name' => 'Options Config', 'url' => '/core/core-options', 'icon' => ''],
            ['name' => 'Input Fields', 'url' => '/core/core-fields', 'icon' => ''],
            ['name' => 'Item Alias', 'url' => '/core/core-item-alias', 'icon' => ''],
        ];
        return CNSiteMap::navbar($nav);
    }
    public static function navTableFields(){
         $nav = [
            ['name' => 'Profile', 'url' => '/core/tables-fields?table=profile', 'icon' => ''], 
        ];
        return CNSiteMap::navbar($nav);
    }
}
