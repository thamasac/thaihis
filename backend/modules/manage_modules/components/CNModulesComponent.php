<?php
 

namespace backend\modules\manage_modules\components;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
class CNModulesComponent extends \yii\base\Widget{
    //put your code here
    private $datas;
    private $imgPath = "";
    private $noImage = "";
    /**
     * @inheritdoc
     * @return CNModulesComponent the newly created [CNModulesComponentt]] instance.
     */
    public static function ui(){
        return Yii::createObject(CNModulesComponent::className());
    }
    
    /**
     * 
     * @param type $datas
     * @return $this
     */
    public function setDatas($datas){
        $this->datas = $datas;
        return $this;
    }
    public static function uiButtons($id, $edit){
        $ele = '';
        $ele .= Html::beginTag('div', ['class' => 'flex-items-button', 'style' => 'margin-bottom:5px;']);
        $ele .= Html::button("<i class='fa fa-bars'></i>", ['class' => 'btn btn-sm btn-default draggable']);
        $ele .= " ";
        if($edit == true){
           $ele .= Html::button("<i class='fa fa-pencil'></i>", ['class' => 'btn btn-sm btn-default btn-edit', 'data-action' => 'update', 'data-url' => '/ezforms2/ezform-data/ezform?ezf_id=1528936267089555700&dataid=' . $id . '&db2=0']);
           $ele .= " "; 
        }
        
        $ele .= Html::button("<i class='fa fa-trash'></i>", ['class' => 'btn btn-sm btn-default btn-delete', 'data-action' => 'delete', 'data-id' => "{$id}", 'data-url' => '/manage_modules/default/delete']);
        $ele .= Html::endTag('div');
        return $ele;
    }
    public function getIconModule($module_id){
        $module = \backend\modules\ezmodules\models\Ezmodule::findOne($module_id);
        if($module){
            return "{$module['icon_base_url']}/{$module['ezm_icon']}";
        }else{
            return "";
        }
    }

    public function uiGridBasic($edit='', $type=''){
        $default_image = "";
        $ele = '';      
        $ele .= Html::beginTag('div',['class'=>'flex-container-original','id'=>'ezf-box']);
        
        //\appxq\sdii\utils\VarDumper::dump($demo);    
            foreach($this->datas as $key=>$value){
                $access = \backend\modules\ezforms2\classes\EzfAuthFuncManage::auth()
                        ->accessAllow($value['data-id']);
                
                if($access == true){
                    $link = '#';
                    if($value['enabledLink'] == true){
                        $link = $value['link'];
                    }
                    $ele .= "<a href='".$link."' class='flex-items  cursor  dads-children bgcolor' title='".$value['name']."' data-id='".$value['id']."'>";
                        if($value['enabledButton'] == true){
                          $ele .= self::uiButtons($value['id'], $edit); 
                        }

                        $ele .= Html::beginTag('div',['class'=>'flex-items-image']);
                            $img = "{$value['imgPath']}/module/{$value['image_default']}";
    //                        \appxq\sdii\utils\VarDumper::dump($value['noImage']);
                            if($value['image_default'] == ''){   
                                $img = $this->getIconModule($value['module_id']);
                                if(!$img){
                                     $img = "{$value['noImage']}";
                                }

                            }
                            if($value['image'] != ''){          
                                $img = $this->getIconModule($value['module_id']);
                                if(!$img){
                                    $img = "{$value['imgPath']}/ezform/fileinput/{$value['image']}";
                                }

                            }
                            $ele .= Html::img($img, ['class'=>'images']);
                        $ele .= Html::endTag('div');
                        $ele .= Html::beginTag('div',['class'=>'flex-items-content']);
                            $ele .= \cpn\chanpan\classes\utils\CNUtils::lengthName($value['name']);
                        $ele .= Html::endTag('div');
                    $ele .= "</a>";  
                }             
                
            } 
            
        $ele .= Html::endTag('div');
            
        $this->cssOriginalRegister();
        $this->jsRegister();
       
        echo $ele;
    }
    
    /**
     * list view 
     */
    public function uiList(){
        $default_image = "";
        $ele  = '';
        $ele  .= '<div style="margin-top:10px;"></div>';
        $ele .= Html::beginTag('div',['class'=>'flex-container-original','id'=>'ezf-box']);
            
            foreach($this->datas as $key=>$value){
                $link = '#';
                if($value['enabledLink'] == true){
                    $link = $value['link'];
                }
                $ele .= "<a href='".$link."' class='flex-items  cursor  dads-children bgcolor' title='".$value['name']."' data-id='".$value['id']."'>";
                $color = ($value['color'] != '') ? $value['color'] : 'primary';
                $img = "{$value['imgPath']}/module/{$value['image_default']}";                
                if ($value['image_default'] == '') {
                    $img = "{$value['noImage']}";
                }
                if($value['image'] != ''){                            
                            $img = "{$value['imgPath']}/ezform/fileinput/{$value['image']}";
                }
                $image = '<img class="img-rounded" src="'.$img.'" alt="" style="width:40px;height:40px;">';
                if($value['icon'] != ''){
                    $image = "<i class='fa ".$value['icon']." text-".$color."' style='font-size: 30pt;'></i>";
                }
            $ele .= '
                    <div class="col-md-6 col-xs-6" style="margin-bottom: 30px;">
                    <div class="col-md-3 col-xs-3 text-right">
                    ';
                    if($value['enabledButton'] == true){
                      $ele .= self::uiButtons($value['id'], true); 
                    }
            $ele .= '                    
                    '.$image.'
                    </div>
                    <div class="col-md-9 col-xs-9 text-right">
                    <div class="col-md-12 text-left">
                    <strong class="btn btn-xs btn-block btn-'.$color.'">'.$value['name'].'</strong>
                    <div class="col-md-12">
                        '.\cpn\chanpan\classes\utils\CNUtils::lengthName($value['name']).'
                    </div>
                    </div>
                    </div>
                    </div>
               ';
            $ele .= "</a>";
            } 
            
        $ele .= Html::endTag('div');
            
        $this->cssOriginalRegisterList();
        $this->jsRegister();
        echo $ele;
    }
    public function cssOriginalRegisterList(){
        $view = $this->getView();
        $css="
             
        ";
        $view->registerCss($css);
    }
    public function cssOriginalRegister(){
        $view = $this->getView();
        $css="
            .flex-container-original{
                display:flex;
                flex-wrap: wrap;
            }
            .flex-items{
 
                flex-basis: 16.3333%;
                flex-basis: 130px;
                margin: 10px;
                padding: 10px;
                align-self: center;
                text-align: center;
                border-radius: 3px;
                height: 185px;
            }
            .cursor{cursor:pointer;}
            cursor:link{text-decoration:none;}
            .bgcolor{
               
            }
            .bgcolor:hover{
                background: #e5e5e536;
            }
            a:hover, a:focus {
                color: #23527c;
                text-decoration: none;
            }
            .flex-items-image{    text-align: center;}
            .images{
                width:100px;border-radius: 5px;
            }
        ";
        $view->registerCss($css);
    }
    public function jsRegister(){
        $view = $this->getView();
        $js= "
             
        ";
        
        $view->registerJs($js);
    }
}
