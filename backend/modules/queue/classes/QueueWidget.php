<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\queue\classes;

use appxq\sdii\utils\VarDumper;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\utils\SDUtility;

/**
 * Description of QueueWidget
 *
 * @author AR9
 */
class QueueWidget extends \yii\base\Component
{

    //put your code here
    public $target = '';
    public $que_type = '1';
    public $bdate_field = '';
    public $status_field = '';
    public $template_content = '';
    public $reloadDiv = 'divQueue';
    public $options = [];
    public $current_url = '';
    public $dept_field = [];
    public $pic_field = '';
    public $title = '';
    public $vdate_field = '';
    public $ezf_main_id = '';
    public $ezf_ref_id = '';
    public $data_columns = [];
    public $param = [];
    public $custom_label = [];
    public $fields_search_one = [];
    public $fields_search_multi = [];
    public $icon = '';
    public $doc_field = '';
    public $action = '1';
    public $radio_check = false;
    public $position = ['position_type' => '1', 'height_static' => '100%', 'fixed_position' => 1, 'width' => '350'];
    public $element_id = 'element_id';
    public $ezf_ref_lavel2_id = [];
    public $condition = [];
    public $group_by = '';
    public $clearDiv = '';
    public $widget_que_type = 'queue';
    public $url_controller = '/queue';
    public $params_value = '';
    public $split_permission = false;
    public $btn_report = false;
    public $order_by = [];

    /**
     * @inheritdoc
     * @return QueueWidget the newly created [[QueueWidget]] instance.
     */
    public static function ui()
    {
        return Yii::createObject(QueueWidget::className());
    }

    /**
     *
     * @param type $ezf_main_id
     * @return $this
     */
    public function ezf_main_id($ezf_main_id)
    {
        $this->ezf_main_id = $ezf_main_id;
        return $this;
    }

    /**
     *
     * @param type $ezf_ref_id
     * @return $this
     */
    public function ezf_ref_id($ezf_ref_id)
    {
        $this->ezf_ref_id = $ezf_ref_id;
        return $this;
    }

    /**
     *
     * @param type $data_columns
     * @return $this
     */
    public function data_columns($data_columns)
    {
        $this->data_columns = $data_columns;
        return $this;
    }

    /**
     *
     * @param type $status_field
     * @return $this
     */
    public function status_field($status_field)
    {
        $this->status_field = $status_field;
        return $this;
    }

    public function dept_field($dept_field)
    {
        $this->dept_field = $dept_field;
        return $this;
    }

    /**
     *
     * @param type $queue_type
     * @return $this
     */
    public function que_type($que_type)
    {
        $this->que_type = $que_type;
        return $this;
    }

    /**
     *
     * @param type $bdate_field
     * @return $this
     */
    public function bdate_field($bdate_field)
    {
        $this->bdate_field = $bdate_field;
        return $this;
    }

    /**
     *
     * @param type $pic_field
     * @return $this
     */
    public function pic_field($pic_field)
    {
        $this->pic_field = $pic_field;
        return $this;
    }

    /**
     *
     * @param type $template_content
     * @return $this
     */
    public function template_content($template_content)
    {
        $this->template_content = $template_content;
        return $this;
    }

    /**
     *
     * @param type $target
     * @return $this
     */
    public function target($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     *
     * @param type $reloadDiv
     * @return $this
     */
    public function reloadDiv($reloadDiv)
    {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }

    /**
     * turn $this the query object itself
     */
    public function options($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     *
     * @param type $current_url
     * @return $this
     */
    public function current_url($current_url)
    {
        $this->current_url = $current_url;
        return $this;
    }

    /**
     *
     * @param type $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     *
     * @param type $visit_date
     * @return $this
     */
    public function vdate_field($vdate_field)
    {
        $this->vdate_field = $vdate_field;
        return $this;
    }

    /**
     *
     * @param type $param
     * @return $this
     */
    public function param($param)
    {
        $this->param = $param;
        return $this;
    }

    /**
     *
     * @param type $custom_label
     * @return $this
     */
    public function custom_label($custom_label)
    {
        $this->custom_label = $custom_label;
        return $this;
    }

    /**
     *
     * @param type $fields_search
     * @return $this
     */
    public function fields_search($fields_search)
    {
        $this->fields_search = $fields_search;
        return $this;
    }

    /**
     * @param $fields_search_one
     * @return $this
     */
    public function fields_search_one($fields_search_one)
    {
        $this->fields_search_one = $fields_search_one;
        return $this;
    }

    /**
     * @param $fields_search_multi
     * @return $this
     */
    public function fields_search_multi($fields_search_multi)
    {
        $this->fields_search_multi = $fields_search_multi;
        return $this;
    }

    /**
     * @param $clearDiv
     * @return $this
     */
    public function clearDiv($clearDiv)
    {
        $this->clearDiv = $clearDiv;
        return $this;
    }

    /**
     * @param $icon
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @param $doc_field
     * @return $this
     */
    public function doc_field($doc_field)
    {
        $this->doc_field = $doc_field;
        return $this;
    }

    /**
     * @param $action
     * @return $this
     */
    public function action($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param $radio_check
     * @return $this
     */
    public function radio_check($radio_check)
    {
        $this->radio_check = $radio_check;
        return $this;
    }

    /**
     * @param $position
     * @return $this
     */
    public function position($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @param $element_id
     * @return $this
     */
    public function element_id($element_id)
    {
        $this->element_id = $element_id;
        return $this;
    }

    /**
     * @param $ezf_ref_lavel2_id
     * @return $this
     */
    public function ezf_ref_lavel2_id($ezf_ref_lavel2_id)
    {
        $this->ezf_ref_lavel2_id = $ezf_ref_lavel2_id;
        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function condition($condition)
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * @param $group_by
     * @return $this
     */
    public function group_by($group_by)
    {
        $this->group_by = $group_by;
        return $this;
    }

    /**
     * @param type $widget_que_type
     * @return $this
     */
    public function widget_que_type($widget_que_type)
    {
        $this->widget_que_type = $widget_que_type;
        return $this;
    }

    /**
     * @param type $widget_que_type
     * @return $this
     */
    public function url_controller($url_controller)
    {
        $this->url_controller = $url_controller;
        return $this;
    }

    /**
     * @param type $widget_que_type
     * @return $this
     */
    public function params_value($params_value)
    {
        $this->params_value = $params_value;
        return $this;
    }

    public function split_permission($split_permission)
    {
        $this->split_permission = $split_permission;
        return $this;
    }

    public function btn_report($btn_report)
    {
        $this->btn_report = $btn_report;
        return $this;
    }

    public function order_by($order_by){
        $this->order_by = $order_by;
        return $this;
    }

    /**
     *
     * @return Html
     */
    public function buildUi()
    {

        if (empty($this->reloadDiv)) {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }
        if (empty($this->current_url) || $this->current_url == '') {
            $this->current_url = strrpos(Url::current(), '&target') > 0 ? substr(Url::current(), 0, strrpos(Url::current(), '&target')) : Url::current();
        }

        $url = Url::to([$this->url_controller,
            'ezf_main_id' => $this->ezf_main_id,
            'ezf_ref_id' => EzfFunc::arrayEncode2String($this->ezf_ref_id),
            'data_columns' => EzfFunc::arrayEncode2String($this->data_columns),
//            'status_field' => EzfFunc::arrayEncode2String($this->status_field),
            'condition' => EzfFunc::arrayEncode2String($this->condition),
            'group_by' => $this->group_by,
            'dept_field' => $this->dept_field,
            'doc_field' => $this->doc_field,
            'split_permission' => $this->split_permission,
            'bdate_field' => $this->bdate_field,
            'pic_field' => $this->pic_field,
            'template_content' => $this->template_content,
            'que_type' => $this->que_type,
            'target' => $this->target,
            'current_url' => $this->current_url,
            'action' => $this->action,
            'reloadDiv' => $this->reloadDiv,
            'title' => $this->title,
            'radio_check' => $this->radio_check,
            'icon' => $this->icon,
            'param' => EzfFunc::arrayEncode2String($this->param),
            'custom_label' => EzfFunc::arrayEncode2String($this->custom_label),
            'fields_search_one' => EzfFunc::arrayEncode2String($this->fields_search_one),
            'fields_search_multi' => EzfFunc::arrayEncode2String($this->fields_search_multi),
            'position' => EzfFunc::arrayEncode2String($this->position),
            'element_id' => $this->element_id,
            'search_field' => Yii::$app->request->get('search_field', ''),
            'searchBoxOne' => Yii::$app->request->get('searchBoxOne', ''),
            'clearDiv' => $this->clearDiv,
            'widget_que_type' => $this->widget_que_type,
            'params_value' => EzfFunc::arrayEncode2String($this->params_value),
            'btn_report' => $this->btn_report,
            'page' => Yii::$app->request->get('page',''),
            'order_by' => EzfFunc::arrayEncode2String($this->order_by)
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
