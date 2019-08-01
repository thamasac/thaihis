<?php

namespace backend\modules\tmf\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of NotifyWidget
 *
 * @author appxq
 */
class TmfWidget extends \backend\modules\ezforms2\classes\WidgetBuilder {

    public $ezf_type_id = '';
    public $ezf_name_id = '';
    public $ezf_detail_id = '';
    public $data_column_type = [];
    public $data_column_name = [];
    public $data_column_detail = [];
    public $action_column = [];
    public $order_column_type = [];
    public $order_column_name = [];
    public $order_column_detail = [];
    public $type_field_label = '';
    public $type_field_value = '';
    public $ref_form_detail = '';
    public $pageSize = 50;
    public $orderby = 4;
    public $module = '';
    public $type_id = 0;
    public $data_id = 0;

    /**
     * @param array $order_column order column
     * @return $this the query object itself
     */
    public function order_column_type($order_column_type) {
        $this->order_column_type = $order_column_type;
        return $this;
    }

    public function module($module) {
        $this->module = $module;
        return $this;
    }

    public function order_column_name($order_column_name) {
        $this->order_column_name = $order_column_name;
        return $this;
    }

    public function order_column_detail($order_column_detail) {
        $this->order_column_detail = $order_column_detail;
        return $this;
    }

    /**
     * @param string $ezf_id ezf id
     * @return $this the query object itself
     */
    public function ezf_type_id($ezf_type_id) {
        $this->ezf_type_id = $ezf_type_id;
        return $this;
    }

    public function ezf_name_id($ezf_name_id) {
        $this->ezf_name_id = $ezf_name_id;
        return $this;
    }

    public function ezf_detail_id($ezf_detail_id) {
        $this->ezf_detail_id = $ezf_detail_id;
        return $this;
    }

    /**
     * @param array $data_column show column
     * @return $this the query object itself
     */
    public function data_column_type($data_column_type) {
        $this->data_column_type = $data_column_type;
        return $this;
    }

    public function data_column_name($data_column_name) {
        $this->data_column_name = $data_column_name;
        return $this;
    }

    public function data_column_detail($data_column_detail) {
        $this->data_column_detail = $data_column_detail;
        return $this;
    }

    public function type_field_label($type_field_label) {
        $this->type_field_label = $type_field_label;
        return $this;
    }

    public function type_field_value($type_field_value) {
        $this->type_field_value = $type_field_value;
        return $this;
    }

    public function ref_form_detail($ref_form_detail) {
        $this->ref_form_detail = $ref_form_detail;
        return $this;
    }

    public function type_id($type_id) {
        $this->type_id = $type_id;
        return $this;
    }
    
    public function data_id($data_id) {
        $this->data_id = $data_id;
        return $this;
    }

    /**
     * @inheritdoc
     * @return WidgetBuilder the newly created [[WidgetBuilder]] instance.
     */
    public static function ui() {
        return Yii::createObject(TmfWidget::className()); //, [get_called_class()]
    }

    protected function buildUi() {
//        \appxq\sdii\utils\VarDumper::dump($this->action);
        if (empty($this->reloadDiv)) {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $url = Url::to(['/tmf/tmf/' . $this->action,
                    'ezf_type_id' => $this->ezf_type_id,
                    'ezf_name_id' => $this->ezf_name_id,
                    'ezf_detail_id' => $this->ezf_detail_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'data_column_type' => $this->data_column_type,
                    'data_column_name' => EzfFunc::arrayEncode2String($this->data_column_name),
                    'data_column_detail' => EzfFunc::arrayEncode2String($this->data_column_detail),
                    'popup' => 0,
                    'disabled' => $this->disabled,
                    'default_column' => $this->default_column,
//                    'order_column_type' => $this->order_column_type,
//                    'order_column_name' => $this->order_column_name,
//                    'order_column_detail' => EzfFunc::arrayEncode2String($this->order_column_detail),
                    'ref_form_detail' => $this->ref_form_detail,
                    'type_field_value' => $this->type_field_value,
                    'type_field_label' => $this->type_field_label,
                    'pageSize' => $this->pageSize,
                    'orderby' => $this->orderby,
                    'type_id' => $this->type_id,
                    'data_id' => $this->data_id,
                    'module' => $this->module
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

    public function buildGrid() {
        $this->action = 'view';
        return $this->buildUi();
    }

    public function buildEmrGrid() {
        $this->action = 'emr-popup';

        return $this->buildUi();
    }

}
