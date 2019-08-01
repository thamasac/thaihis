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
class TargetBuilder extends Component {

    //put your code here
    public $ezf_id = '';
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $target = '';
    public $targetField = '';
    public $dataid = '';
    
    public $image_field = '';
    public $age_field = '';
    public $fields = [];
    public $fields_search = [];
    public $template_items = '';
    public $template_selection = '';
    public $current_url = '';
    public $placeholder = 'Search ...';
    
    public $options = [];
    
    /**
     * @param string $placeholder  placeholder
     * @return $this the query object itself
     */
    public function placeholder($placeholder = '') {
        $this->placeholder = $placeholder;
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
     * @param string $image_field show image
     * @return $this the query object itself
     */
    public function image_field($image_field = '') {
        $this->image_field = $image_field;
        return $this;
    }
    
    public function age_field($age_field = '') {
        $this->age_field = $age_field;
        return $this;
    }
    
    /**
     * @param array $fields_search search fields
     * @return $this the query object itself
     */
    public function fields_search($fields_search) {
        $this->fields_search = $fields_search;
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
     * @param string $template_items template items (JS String)
     * @return $this the query object itself
     */
    public function template_items($template_items) {
        $this->template_items = $template_items;
        return $this;
    }
    
    /**
     * @param string $template_selection template Selection (JS String)
     * @return $this the query object itself
     */
    public function template_selection($template_selection) {
        $this->template_selection = $template_selection;
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
     * @param string $ezf_id ezf id
     * @return $this the query object itself
     */
    public function current_url($current_url) {
        $this->current_url = $current_url;
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
     * @return TargetBuilder the newly created [[TargetBuilder]] instance.
     */
    public static function targetWidget() {
        return Yii::createObject(TargetBuilder::className());//, [get_called_class()]
    }

    public function buildTarget() {
       if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
       }
       
        $op_content = [
            'image_field' => $this->image_field,
            'age_field' => $this->age_field,
            'template_items' => $this->template_items,
            'template_selection' => $this->template_selection,
        ];
        
        $current_url = $this->current_url!=''?$this->current_url:Url::current(['target'=>$this->target]);
        
        $url = Url::to(['/ezforms2/ezform-data/target',
                    'ezf_id' => $this->ezf_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'fields' => EzfFunc::arrayEncode2String($this->fields),
                    'fields_search' => EzfFunc::arrayEncode2String($this->fields_search),
                    'target' => $this->target,
                    'targetField' => $this->targetField,
                    'dataid' => $this->dataid,
                    'placeholder' => $this->placeholder,
                    'current_url' => base64_encode($current_url),
                    'options' => EzfFunc::arrayEncode2String($op_content),
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        $options['data-id'] = $this->target;
        
        $view = Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', $options);
    }
    

}
