<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\tabs\TabsX;
/**
 * Description of WidgetBuilder
 *
 * @author appxq
 */
class TabBuilder extends Component {

    //put your code here
    public $items = [];
//    public $label;
//    public $path;
//    public $params = [];
//    public $content;
    
    public $id;
    public $bordered = true;
    public $position = 'above';
    public $align = 'left';
    public $pluginOptions = [];
    public $pluginEvents = [];
    public $options = [];
    
    /**
     * @param string $id  tab id
     * @return $this the query object itself
     */
    public function id($id) {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @param array items [['label'=>'<i class="glyphicon glyphicon-home"></i> Home', 'content'=>'', 'path'=>'', 'params'=>[]] ] 
     * @return $this the query object itself 
     * 
     */
    public function items($items) {
        $this->items = $items;
        return $this;
    }
    
    /**
     * @param boolean $bordered allow edit form
     * @return $this the query object itself
     */
    public function bordered($bordered) {
        $this->bordered = $bordered;
        return $this;
    }
    /**
     * @param string $position  data id
     * @return $this the query object itself
     */
    public function position($position = '') {
        $this->position = $position;
        return $this;
    }
    
    /**
     * @param string $align  data id
     * @return $this the query object itself
     */
    public function align($align = '') {
        $this->align = $align;
        return $this;
    }
    
    /**
     * @param array $pluginOptions search fields
     * @return $this the query object itself
     */
    public function pluginOptions($pluginOptions) {
        $this->pluginOptions = $pluginOptions;
        return $this;
    }
    
    /**
     * @param array $pluginEvents search fields
     * @return $this the query object itself
     */
    public function pluginEvents($pluginEvents) {
        $this->pluginEvents = $pluginEvents;
        return $this;
    }

    /**
     * @param array $options options Html
     * @return $this the query object itself
     */
    public function options($options) {
        $this->options = $options;
        return $this;
    }

    /**
     * @inheritdoc
     * @return TabBuilder the newly created [[TabBuilder]] instance.
     */
    public static function tabWidget() {
        return Yii::createObject(TabBuilder::className());//, [get_called_class()]
    }

    public function buildTab() {
       $view = Yii::$app->getView();
        
       $items = [];
       if(isset($this->items) && !empty($this->items)){
           foreach ($this->items as $key => $value) {
               $content = '';
               $label = isset($value['label'])?$value['label']:'';
               $active = isset($value['active'])?$value['active']:FALSE;

               if(isset($value['path'])){
                   $params = isset($value['params'])?$value['params']:[];
                   
                   $ajax = Url::to(['/ezmodules/ezmodule-widget/tabs-render', 'path'=>$value['path'], 'params'=> EzfFunc::arrayEncode2String($params)]);
                   $content = $view->render($value['path'], $params);
                   
                   $items[] = [
                        'label'=>$label,
                        'content'=>$content,
                        'active'=>$active,
                        'linkOptions'=>['data-url'=> $ajax]
                    ];
                   
               } else {
                   if(isset($value['content'])){
                       $content = $value['content'];
                   }
                   
                   $items[] = [
                        'label'=>$label,
                        'content'=>$content,
                        'active'=>$active,
                    ];
               }
               
           }
       } 
       
       if (!isset($this->id)) {
           $this->id = 'sdtab-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
       }
       
        return TabsX::widget([
            'id'=>$this->id,
            'items'=>$items,
            'position'=> $this->position,
            'align'=>$this->align,
            'pluginOptions'=>$this->pluginOptions,
            'pluginEvents'=>$this->pluginEvents,
            'encodeLabels'=>false,
            'enableStickyTabs'=>true,
            'bordered'=>$this->bordered,
            'options'=>$this->options,
        ]);
    }
    

}
