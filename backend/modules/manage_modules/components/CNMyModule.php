<?php
namespace backend\modules\manage_modules\components;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
class CNMyModule extends \yii\base\Widget{
    private $dataModule, $imgPath , $noImage;
    private $cardWidth;
    private $link = false;
    private $target_link;
    /**
     * @inheritdoc
     * @return CNMyModule the newly created [[CNMyModule]] instance.
     */
    public static function classNames(){
        return Yii::createObject(CNMyModule::className());
    }
    
    /**
     * 
     * @param type $dataModule array ['module_id'=>'12345667','module_name'=>'test','image'=>'xxxxx', 'module_icon'=>'x', 'color'=>'primary']
     * @return $this
     */
    public function setDataModule($dataModule){
        $this->dataModule = $dataModule;
        return $this;
    }
    
    /**
     * 
     * @param type $imgPath string example https://storage.work.in.th
     * @return $this
     */
    public function setImgPath($imgPath){
        $this->imgPath = ($imgPath != '') ? $imgPath : Yii::getAlias('@storageUrl');
        return $this;
    }
    /**
     * 
     * @param type $noImage string example https://storage.work.in.th/ezform/img/no_icon.png
     * @return $this
     */
    public function setNoImage($noImage){
        $this->noImage = ($noImage != '') ? $noImage : $this->imgPath.'/ezform/img/no_icon.png';
        return $this;
    }
    /**
     * 
     * @param type $cardWidth integer  example 6  = 50%  12 = 100%
     * @return $this
     */
    public function setCardWidth($cardWidth){
        $this->cardWidth = ($cardWidth != '') ? $cardWidth : '12';
        return $this;
    }
    /**
     * 
     * @param type $link boolean  example true, false
     * @return $this
     */
    public function setLink($link){
        $this->link = ($link != '') ? $link : false;
        return $this;
    }
    
    /**
     * 
     * @param type $target_link string 
     * example 
     * _blank : Opens the linked document in a new window or tab <br>
     *  _self : Opens the linked document in the same frame as it was clicked (this is default)<br>
     * _parent : Opens the linked document in the parent frame<br>
     * _top : Opens the linked document in the full body of the window<br>
     * @return $this 
     */
    public function setTargetLink($target_link){
        $this->target_link = ($target_link != '') ? $target_link : '';
        return $this;
    }
    
    //get data
    public function getImageModule(){
        $html = "";
        $html .= Html::beginTag("DIV", ['class'=>'col-md-3 col-xs-3 text-right']);
        if($this->dataModule['image'] == '' &&  $this->dataModule['module_icon'] == ''){
            $html .= Html::img($this->noImage, ['class'=>'img-rounded', 'style'=>'width:40px;height:40px;']);
        }else{
           if($this->dataModule['module_icon'] != ''){
               $icon = (!empty($this->dataModule['module_icon'])) ? $this->dataModule['module_icon'] : 'fa-home';
                $color = (!empty($this->dataModule['color'])) ? $this->dataModule['color'] : 'success';
                $html .= Html::tag("I", "", [
                    'style'=>'font-size: 30pt;',
                    'class'=>"fa {$icon} text-{$color}"
                ]);
                
           }else{
               $html .= Html::img($this->imgPath.'/ezform/fileinput/'.$this->dataModule['image'], ['class'=>'img-rounded', 'style'=>'width:40px;height:40px;']); 
           }
        }
        $html .= Html::endTag("DIV");//col-md-3
        return $html;
    }
    public function getContentModule(){
        $html = "";
        $html .= Html::beginTag("DIV", ['class'=>'col-md-9 col-xs-9 text-right']);
            $html .= Html::beginTag("DIV", ['class'=>'col-md-12 text-left']);
                $color = (!empty($this->dataModule['color'])) ? $this->dataModule['color'] : 'primary';
                $html .= Html::tag('strong', $this->dataModule['module_name'], [
                    'class'=>'btn btn-xs btn-block btn-'.$color
                ]);
                $html .= Html::tag("DIV",($this->dataModule['detail'] != '') ? $this->dataModule['detail'] : $this->dataModule['module_name'],[
                    'class'=>'col-md-12'
                ]);
            $html .= Html::endTag("DIV");
        $html .= Html::endTag("DIV");
        $this->cssRegister();
        return $html;
    }
    public function buildCard(){
        $html = "";
        
        $html .= Html::beginTag("DIV", ['class'=>"col-md-{$this->cardWidth} col-xs-{$this->cardWidth}", 'style'=>'margin-bottom: 30px;']);
        $html .= $this->getImageModule();
        $html .= $this->getContentModule();
        $html .= Html::endTag("DIV");      
       
        if($this->link){
            
            $linkTo = ($this->dataModule['url'] != '') ? $this->dataModule['url'] : Url::to(["/ezmodules/ezmodule/view?id={$this->dataModule['module_id']}"]);        
            $links = Html::a($html, $linkTo, ['target'=> $this->target_link]);
            echo $links;
        }else{
            echo $html;
        }        
    }
    
    public function cssRegister(){
        $view = $this->getView();
        $view->registerCss("
            .btn-xs.btn-info {
                padding: 1px;
                font-size: 10pt;
            }
        ");
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
