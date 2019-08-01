<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\classes;
use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;
/**
 * Description of ActivityBuilder
 *
 * @author jokeclancool
 */
class ItemListBuilder extends \yii\base\Component {
   //put your code here
    public $ezf_id = '';
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $target = '';
    public $targetField = '';
    public $dataid = '';
    public $title = '';
    public $image_field = '';
    public $fields = [];
    public $fields_search = [];
    public $template_items = '';
    public $template_selection = '';
    
    public $options = [];
    
    /**
     * @param array $title show title
     * @return $this the query object itself
     */
    public function title($title) {
        $this->title = $title;
        return $this;
    }
    
    
    /**
     * @param array $fields show fields
     * @return $this the query object itself
     */
    public function fields($fields) {
        $this->fields = $fields;
        return $this;
    }
    
    /**
     * @param string $modal use modal default modal-ezform-main
     * @return $this the query object itself
     */
    public function modal($modal = null) {
        if (isset($modal)) {
            $this->modal = $modal;
        }
        return $this;
    }

    /**
     * @param string $reloadDiv div id for reload html
     * @return $this the query object itself
     */
    public function reloadDiv($reloadDiv = '') {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }


    /**
     * @param string $ezf_id ezf id
     * @return $this the query object itself
     */
    public function ezf_id($ezf_id) {
        $this->ezf_id = $ezf_id;
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
     * @return ItemListBuilder the newly created [[ItemListBuilder]] instance.
     */
    public static function itemListWidget() {
        return Yii::createObject(ItemListBuilder::className());//, [get_called_class()]
    }

    public function buildItemList() {
       if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
       }
       
        $url = Url::to(['/report/item-lists/index',
                    'ezf_id' => $this->ezf_id,
                    'title'=>$this->title,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'fields' => EzfFunc::arrayEncode2String($this->fields),
                    'fields_search' => EzfFunc::arrayEncode2String($this->fields_search),
                    'current_url' => base64_encode(Url::current()),
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        
        $view = Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', $options);
    }
    

}
