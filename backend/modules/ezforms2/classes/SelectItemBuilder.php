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
class SelectItemBuilder extends Component {

    //put your code here
    public $sql_id='';
    public $reloadDiv = '';
    public $target = '';

    public $title='';
    public $image = '';
    public $search = '';
    public $query_params = '';
    public $width = 300;
    public $image_wigth = 64;
    public $template_content = '';
    public $template_items = '';
    public $template_selection = '';
    public $params = [];
    public $key_id = '';
    public $key_name = 'dataid';
    public $page_size = 50;
    public $current_url = '';
    public $placeholder = 'Search ...';
    public $reload_widget='';
    public $ezf_id='';
    public $save_ezf_id='';
    public $after_save_url='';
    public $data_column=[];
    //public $parent_name='target';
    
    public $options = [];
    
    /**
     * @param array $data_column show column
     * @return $this the query object itself
     */
    public function data_column($data_column) {
        $this->data_column = $data_column;
        return $this;
    }
    /**
     * @param string $parent_name  parent_name
     * @return $this the query object itself
     */
//    public function parent_name($parent_name = 'target') {
//        $this->parent_name = $parent_name;
//        return $this;
//    }
    /**
     * @param string $after_save_url  after_save_url
     * @return $this the query object itself
     */
    public function after_save_url($after_save_url = '') {
        $this->after_save_url = $after_save_url;
        return $this;
    }
    /**
     * @param string $reload_widget  reload_widget
     * @return $this the query object itself
     */
    public function reload_widget($reload_widget = '') {
        $this->reload_widget = $reload_widget;
        return $this;
    }
    /**
     * @param string $ezf_id  ezf_id
     * @return $this the query object itself
     */
    public function ezf_id($ezf_id = '') {
        $this->ezf_id = $ezf_id;
        return $this;
    }
    /**
     * @param string $save_ezf_id  save_ezf_id
     * @return $this the query object itself
     */
    public function save_ezf_id($save_ezf_id = '') {
        $this->save_ezf_id = $save_ezf_id;
        return $this;
    }
    
    /**
     * @param string $placeholder  placeholder
     * @return $this the query object itself
     */
    public function placeholder($placeholder = '') {
        $this->placeholder = $placeholder;
        return $this;
    }
    /**
     * @param string $current_url ezf id
     * @return $this the query object itself
     */
    public function current_url($current_url) {
        $this->current_url = $current_url;
        return $this;
    }
    /**
     * @param string $key_name show key_name
     * @return $this the query object itself
     */
    public function key_name($key_name = '') {
        $this->key_name = $key_name;
        return $this;
    }
    /**
     * @param string $key_id show key_id
     * @return $this the query object itself
     */
    public function key_id($key_id = '') {
        $this->key_id = $key_id;
        return $this;
    }
    /**
     * @param string $page_size show page_size
     * @return $this the query object itself
     */
    public function page_size($page_size = 50) {
        $this->page_size = $page_size;
        return $this;
    }
    
    /**
     * @param string $params show params
     * @return $this the query object itself
     */
    public function params($params = []) {
        $this->params = $params;
        return $this;
    }
    /**
     * @param string $search show search
     * @return $this the query object itself
     */
    public function search($search = '') {
        $this->search = $search;
        return $this;
    }
    /**
     * @param string $query_params show query_params
     * @return $this the query object itself
     */
    public function query_params($query_params = '') {
        $this->query_params = $query_params;
        return $this;
    }
    /**
     * @param string $width show width
     * @return $this the query object itself
     */
    public function width($width = 300) {
        $this->width = $width;
        return $this;
    }
    /**
     * @param string $image_wigth show image
     * @return $this the query object itself
     */
    public function image_wigth($image_wigth = 64) {
        $this->image_wigth = $image_wigth;
        return $this;
    }
    
    /**
     * @param string $image show image
     * @return $this the query object itself
     */
    public function image($image = '') {
        $this->image = $image;
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
     * @param string $template_content template content
     * @return $this the query object itself
     */
    public function template_content($template_content) {
        $this->template_content = $template_content;
        return $this;
    }
    
    /**
     * @param string $template_items template content
     * @return $this the query object itself
     */
    public function template_items($template_items) {
        $this->template_items = $template_items;
        return $this;
    }
    
    /**
     * @param string $template_selection template content
     * @return $this the query object itself
     */
    public function template_selection($template_selection) {
        $this->template_selection = $template_selection;
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
     * @param string $sql_id sql id
     * @return $this the query object itself
     */
    public function sql_id($sql_id) {
        $this->sql_id = $sql_id;
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
     * @return SideMenuBuilder the newly created [[SelectItemBuilder]] instance.
     */
    public static function selectItem() {
        return Yii::createObject(SelectItemBuilder::className());//, [get_called_class()]
    }
    
    public function buildMenu() {
       if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
       }
       
        $op_content = [
            'title' => $this->title,
            'query_params' => $this->query_params,
            'width' => $this->width,
            'image_wigth' => $this->image_wigth,
            'image' => $this->image,
            'search' => $this->search,
            'params_all' => $this->params,
            'ezf_id' => $this->ezf_id,
            'save_ezf_id' => $this->save_ezf_id,
            'reload_widget' => $this->reload_widget,
            'after_save_url' => $this->after_save_url,
            'key_id' => $this->key_id,
            'key_name' => $this->key_name,
            //'parent_name' => $this->parent_name,
            'data_column' => EzfFunc::arrayEncode2String($this->data_column),
            'page_size' => $this->page_size,
            'current_url' => $this->current_url,
            'template_items' => $this->template_items,
        ];
        
        $url = Url::to(['/ezforms2/ezform-data/select-sql',
                    'sql_id' => $this->sql_id,
                    'reloadDiv' => $this->reloadDiv,
                    'target' => $this->target,
                    'placeholder' => $this->placeholder,
                    'options' => EzfFunc::arrayEncode2String($op_content),
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        
        $view = Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }
    

}
