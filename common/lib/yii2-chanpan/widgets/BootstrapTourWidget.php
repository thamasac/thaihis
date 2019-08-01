<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace cpn\chanpan\widgets;
 

/**
 * Description of CNIconWidget
 *
 * @author AR9
 */
class BootstrapTourWidget extends \yii\base\Widget {
    /**
    *   
     * \cpn\chanpan\widgets\BootstrapTourWidget::widget([
         'data'=>[
             [
                'element'=>'#btnCreateProject',
                'title'=>'ปุ่มสำหรับสร้างโครงการ',
                'content'=>'ปุ่มสำหรับสร้างโครงการ',
                'placement'=> 'auto',
                'smartPlacement'=> true, 
            ],
            [
                'element'=>'#btnManageProject',
                'title'=>'จัดการโครงการของคุณ',
                'content'=>'รายละเอียด จัดการโครงการของคุณ',
                'placement'=> 'auto',
                'smartPlacement'=> true, 
            ],
            [
                'element'=>'.btnEditShotModule',
                'title'=>'Title of my step',
                'content'=>'Content of my step Content of my step Content of my step',
                'placement'=> 'auto',
                'smartPlacement'=> true, 
            ] 
         ] 
    ]);
     * 
     * 
    */
    
    
    //put your code here
    public $data = [];
    public function init() {
        $view = $this->getView();
        return \cpn\chanpan\assets\tour\BootstrapTourAssets::register($view);
    }

    public function run() {
        $view = $this->getView();        
        $demo_json = \yii\helpers\Json::encode($this->data); 
        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $template = "
          `
            <div class=\'popover tour\' id='".$id."'>
                    <div class=\'arrow\'></div>
                    <h3 class=\'popover-title\'></h3>
                    <div class=\'popover-content\'></div>
                    <div class=\'popover-navigation\'>
                        <button class=\'btn btn-default\' data-role=\'prev\'>« ".\Yii::t('appmenu','Prev')."</button>                        
                        <button class=\'btn btn-default\' data-role=\'next\'>".\Yii::t('appmenu','Next')." »</button>
                        <button class=\'btn btn-default\' data-role=\'end\'>".\Yii::t('appmenu','End')."</button>
                    </div>
                    
           </div>
          `      
        ";
        $js="           
           tours();                 
           function tours(){
                //localStorage.removeItem('tour_end');
                let tour = new Tour({
                   backdrop: true,  
                   steps:".$demo_json.",
                   template: ".(string)$template.", 
                   
                });
                
                
                tour.init();
                tour.start();
            }
            
        ";
        $css="
            .tour{
               min-width: 500px;               
            }
            .tour .popover-content {
                padding: 9px 14px;
                width: 500px;
                overflow: hidden;
            }
            .tour-backdrop{
                z-index:100;
            }
        ";
        $html = "";
        $html .= $view->registerJs($js);
        $html .= $view->registerCss($css);
          
    } 
}
