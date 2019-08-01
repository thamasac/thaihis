<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of WidgetBuilder
 *
 * @author appxq
 */
class ContentBuilder extends Component {

    //put your code here
    public $ezf_id = '';
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $target = '';
    public $targetField = '';
    public $initdata = 0;
    public $initdate = 0;
    public $dataid = '';

    public $title='';
    public $readonly = 0;
    public $disabled = 0;
    public $disabled_box = 0;
    public $column = 2;
    public $action = []; //create, update, delete, view, search
    public $image_field = '';
    public $fields = [];
    public $template_content = '';
    public $template_box = '';
    public $display = 'content_h';//content_v, content_h, custom
    public $addon = 0;
    public $theme = 'default';//default, primary, success, info, warning, danger
    
    public $options = [];
    
    /**
     * @param string $theme color [default, primary, success, info, warning, danger]
     * @return $this the query object itself
     */
    public function theme($theme) {
        $this->theme = $theme;
        return $this;
    }
    
    /**
     * @param boolean $addon addon ezForm
     * @return $this the query object itself
     */
    public function addon($addon) {
        $this->addon = $addon;
        return $this;
    }
    
    /**
     * @param string $image_field show image
     * @return $this the query object itself
     */
    public function image_field($image_field = '') {
        $this->image_field = $image_field;
        return $this;
    }
    
    /**
     * @param string $title show title
     * @return $this the query object itself
     */
    public function title($title = '') {
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
     * @param string $display display content [content_v, content_h, custom] default content_h
     * @return $this the query object itself
     */
    public function display($display) {
        $this->display = $display;
        return $this;
    }
    
    /**
     * @param string $template_content template content
     * @return $this the query object itself
     */
    public function template_content($template_content) {
        $this->template_content = $template_content;
        return $this;
    }
    
    /**
     * @param string $template_box template box
     * @return $this the query object itself
     */
    public function template_box($template_box) {
        $this->template_box = $template_box;
        return $this;
    }
    
    /**
     * @param boolean $disabled show content
     * @return $this the query object itself
     */
    public function disabled($disabled) {
        $this->disabled = $disabled;
        return $this;
    }
    
    /**
     * @param boolean $readonly disable edit data
     * @return $this the query object itself
     */
    public function readonly($readonly) {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * @param boolean $initdata show last data
     * @return $this the query object itself
     */
    public function initdata($initdata) {
        $this->initdata = $initdata;
        return $this;
    }
    
    /**
     * @param boolean $initdate show last data
     * @return $this the query object itself
     */
    public function initdate($initdate) {
        $this->initdate = $initdate;
        return $this;
    }
    
    /**
     * @param boolean $disabled_box hide box
     * @return $this the query object itself
     */
    public function disabled_box($disabled_box) {
        $this->disabled_box = $disabled_box;
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
     * @param string $action use Action [create, update, delete, view, search] default none
     * @return $this the query object itself
     */
    public function action($action = null) {
        if (isset($action)) {
            $this->action = $action;
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
     * @param string $target target id
     * @return $this the query object itself
     */
    public function target($target = '') {
        $this->target = $target;
        return $this;
    }

    /**
     * @param string $targetField target field
     * @return $this the query object itself
     */
    public function targetField($targetField = '') {
        $this->targetField = $targetField;
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
     * @param string $dataid  data id
     * @return $this the query object itself
     */
    public function dataid($dataid = '') {
        $this->dataid = $dataid;
        return $this;
    }
    
    /**
     * @param int $column show column
     * @return $this the query object itself
     */
    public function column($column) {
        $this->column = $column;
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
     * @return ContentBuilder the newly created [[ContentBuilder]] instance.
     */
    public static function contentDisplay() {
        return Yii::createObject(ContentBuilder::className());//, [get_called_class()]
    }

    public function buildBox() {
       if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
       }
       
        $op_content = [
            'title' => $this->title,
            'readonly' => $this->readonly,
            'disabled' => $this->disabled,
            'disabled_box' => $this->disabled_box,
            'column' => $this->column,
            'action' => $this->action,
            'image_field' => $this->image_field,
            'template_content' => $this->template_content,
            'template_box' => $this->template_box,
            'display' => $this->display,
            'addon' => $this->addon,
            'theme' => $this->theme,
        ];
        
        $url = Url::to(['/ezforms2/ezform-data/content',
                    'ezf_id' => $this->ezf_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'fields' => EzfFunc::arrayEncode2String($this->fields),
                    'target' => $this->target,
                    'targetField' => $this->targetField,
                    'initdata' => $this->initdata,
                    'initdate' => $this->initdate,
                    'dataid' => $this->dataid,
                    'options' => EzfFunc::arrayEncode2String($op_content),
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        $options['data-id'] = $this->dataid;
        
        $view = Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }
    

}
