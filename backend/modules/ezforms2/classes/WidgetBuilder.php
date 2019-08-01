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
class WidgetBuilder extends Component {

    //put your code here
    public $ezf_id = '';
    public $disabled = 0;
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $data_column = [];
    public $action_column = [];
    public $target = '';
    public $targetField = '';
    public $options = [];
    public $action = 'none'; //create, update, list, emr, del, view
    public $default_column = 1;
    public $order_column = [];
    public $pageSize = 50;
    public $orderby = SORT_DESC;
    public $db2 = 0;
    public $addbtn = 1;
    public $search_column = [];
    public $varname = 0;
    public $header = [];
    public $actions = [];
    public $rawdata = 0;
    public $title = '';
    public $theme = 'default';
    public $filter = 1;

    /**
     * @param string $filter filter
     * @return $this the query object itself
     */
    public function filter($filter) {
        $this->filter = $filter;
        return $this;
    }
    /**
     * @param string $theme default
     * @return $this the query object itself
     */
    public function theme($theme) {
        $this->theme = $theme;
        return $this;
    }
    /**
     * @param string $title ezf id
     * @return $this the query object itself
     */
    public function title($title) {
        $this->title = $title;
        return $this;
    }
    
    /**
     * @param boolean $db2 allow edit form
     * @return $this the query object itself
     */
    public function db2($db2) {
        $this->db2 = $db2;
        return $this;
    }
    
    /**
     * @param boolean $addbtn allow edit form
     * @return $this the query object itself
     */
    public function addbtn($addbtn) {
        $this->addbtn = $addbtn;
        return $this;
    }
    
    /**
     * @param string $rawdata show double data
     * @return $this the query object itself
     */
    public function rawdata() {
        $this->rawdata = 1;
        return $this;
    }
    /**
     * @param string $varname show double data
     * @return $this the query object itself
     */
    public function varname() {
        $this->varname = 1;
        return $this;
    }
    /**
     * @param array $search_column show column ['field_name'=>'value']
     * @return $this the query object itself
     */
    public function search_column($search_column) {
        $this->search_column = $search_column;
        return $this;
    }
    /**
     * @param array $search_column show column ['field_name'=>'value']
     * @return $this the query object itself
     */
    public function header($header) {
        $this->header = $header;
        return $this;
    }
    
    /**
     * @param array $search_column show column ['field_name'=>'value']
     * @return $this the query object itself
     */
    public function actions($actions) {
        $this->actions = $actions;
        return $this;
    }
    /**
     * @param string $orderby order by
     * @return $this the query object itself
     */
    public function orderby($orderby) {
        $this->orderby = $orderby;
        return $this;
    }
    
    /**
     * @param int $pageSize pageSize
     * @return $this the query object itself
     */
    public function pageSize($pageSize) {
        $this->pageSize = $pageSize;
        return $this;
    }
    
    /**
     * @param array $order_column order column
     * @return $this the query object itself
     */
    public function order_column($order_column) {
        $this->order_column = $order_column;
        return $this;
    }
    
    /**
     * @param boolean $default_column show column
     * @return $this the query object itself
     */
    public function default_column($default_column) {
        $this->default_column = $default_column;
        return $this;
    }
    
    /**
     * @param boolean $disabled allow edit form
     * @return $this the query object itself
     */
    public function disabled($disabled) {
        $this->disabled = $disabled;
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
     * @param string $action use Action [create, update, list, emr, del, view] default none
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
     * @param array $data_column show column
     * @return $this the query object itself
     */
    public function data_column($data_column) {
        $this->data_column = $data_column;
        return $this;
    }
    
    /**
     * @param array $action_column  btn list
     * @return $this the query object itself
     */
    public function action_column($action_column) {
        $this->action_column = $action_column;
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
     * @return WidgetBuilder the newly created [[WidgetBuilder]] instance.
     */
    public static function ui() {
        return Yii::createObject(WidgetBuilder::className());//, [get_called_class()]
    }

    protected function buildUi() {
        if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
        }
       
        $url = Url::to(['/ezforms2/ezform-data/' . $this->action,
                    'ezf_id' => $this->ezf_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'data_column' => EzfFunc::arrayEncode2String($this->data_column),
                    'popup' => 0,
                    'target' => $this->target,
                    'targetField' => $this->targetField,
                    'disabled' => $this->disabled,
                    'default_column' => $this->default_column,
                    'order_column' => EzfFunc::arrayEncode2String($this->order_column),
                    'pageSize' => $this->pageSize,
                    'orderby' => $this->orderby,
                    'db2' => $this->db2,
                    'addbtn' => $this->addbtn,
                    'filter' => $this->filter,
                    'varname' =>$this->varname,
                    'rawdata' =>$this->rawdata,
                    'title' => $this->title,
                    'theme' => $this->theme,
                    'search_column' => EzfFunc::arrayEncode2String($this->search_column),
                    'header' => EzfFunc::arrayEncode2String($this->header),
                    'actions' => EzfFunc::arrayEncode2String($this->actions),
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        
        $view = Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', $options);
    }
    
    public function buildGrid() {
        $this->action = 'view';
        
        return $this->buildUi();
    }
    
    public function buildEmrGrid() {
        $this->action = 'emr-popup';
        
        return $this->buildUi();
    }
    
    public function buildDb2Grid() {
        $this->action = 'view';
        $this->db2 = 1;
        
        return $this->buildUi();
    }
    
    public function buildCompareGrid() {
        $this->action = 'compare';
        
        return $this->buildUi();
    }

}
