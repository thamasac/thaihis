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
class ListViewBuilder extends Component {

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
    public $class_css = '';
    public $style = '';
    public $layout = '';
    
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
     * @param string $class_css  class_css
     * @return $this the query object itself
     */
    public function class_css($class_css = '') {
        $this->class_css = $class_css;
        return $this;
    }
    /**
     * @param string $style  style
     * @return $this the query object itself
     */
    public function style($style = '') {
        $this->style = $style;
        return $this;
    }
    /**
     * @param string $layout  layout
     * @return $this the query object itself
     */
    public function layout($layout = '') {
        $this->layout = $layout;
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
     * @return ListViewBuilder the newly created [[ListViewBuilder]] instance.
     */
    public static function contentDisplay() {
        return Yii::createObject(ListViewBuilder::className());//, [get_called_class()]
    }

    public function buildListView() {
       if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
       }
       
        $op_content = [
            'query_params' => $this->query_params,
            'image_wigth' => $this->image_wigth,
            'image' => $this->image,
            'search' => $this->search,
            'params_all' => $this->params,
            'key_id' => $this->key_id,
            'page_size' => $this->page_size,
            'placeholder' => $this->placeholder,
            'template_content' => $this->template_content,
            'class_css' => $this->class_css,
            'style' => $this->style,
            'layout' => $this->layout,
            'placeholder' => $this->placeholder,
        ];
        
        $url = Url::to(['/ezforms2/ezform-data/listview',
                    'sql_id' => $this->sql_id,
                    'reloadDiv' => $this->reloadDiv,
                    'target' => $this->target,
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
