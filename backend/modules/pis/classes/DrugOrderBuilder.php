<?php

namespace backend\modules\pis\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of DrugOrderBuilder
 *
 * @author appxq
 */
class DrugOrderBuilder extends Component {

    //put your code here
    public $ezf_id = '';
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $target = '';
    public $targetField = '';
    public $initdata = 0;
    public $dataid = '';
    public $visitid = '';
    public $widget_id = '';
    public $fields = [];
    public $forms = [];
    public $title = '';
    public $readonly = 0;
    public $disabled = 0;
    public $action = [];
    public $visit_form = '';
    public $addon = 0;
    public $theme = 'default'; //default, primary, success, info, warning, danger
    public $graphdisplay = 0; // set checkbox graphdisplay
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
     * @param string $reloadDiv div id for reload html
     * @return $this the query object itself
     */
    public function forms($forms = '') {
        $this->forms = $forms;
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
     * @param string $visit_form visit_form
     * @return $this the query object itself
     */
    public function visit_form($visit_form = '') {
        $this->visit_form = $visit_form;
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
     * @param string $visitid  visitid
     * @return $this the query object itself
     */
    public function visitid($visitid = '') {
        $this->visitid = $visitid;
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
     * @param widget_id
     * @return $this the query object itself
     */
    public function widget_id($widget_id) {
        $this->widget_id = $widget_id;
        return $this;
    }

    /**
     * @inheritdoc
     * @return DrugOrderBuilder the newly created [[DrugOrderBuilder]] instance.
     */
    public static function contentBuilding() {
        return Yii::createObject(DrugOrderBuilder::className()); //, [get_called_class()]
    }

    public function buildBox($main_url) {
        if (empty($this->reloadDiv)) {
            $this->reloadDiv = 'div-' . \appxq\sdii\utils\SDUtility::getMillisecTime();
        }

        $op_content = [
            'title' => $this->title,
            'readonly' => $this->readonly,
            'disabled' => $this->disabled,
            'action' => $this->action,
            'addon' => $this->addon,
            'theme' => $this->theme,
            'visit_form' => $this->visit_form,
            'refform' => $this->forms,
            'fields' => $this->fields,
            'ezf_id' => $this->ezf_id,
        ];

        $url = Url::to([$main_url,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'target' => $this->target,
                    'visit_id' => $this->visitid,
                    'targetField' => $this->targetField,
                    'initdata' => $this->initdata,
                    'dataid' => $this->dataid,
                    'widget_id' => $this->widget_id,
                    'options' => EzfFunc::arrayEncode2String($this->options) //EzfFunc::arrayEncode2String($op_content),
        ]);

        $options = [];//$this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        $options['data-id'] = $this->dataid;
        $options['kk-test'] = 'Okts';

        $view = Yii::$app->getView();
        $view->registerJs("
            //getUiAjax('{$url}', '{$this->reloadDiv}');
            $.ajax({
                      method: 'GET',
                      url: '{$url}',
                      dataType: 'HTML',
                      success: function (result, textStatus) {
                        $('#{$this->reloadDiv}').html(result);
                      }
            });
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }

}
