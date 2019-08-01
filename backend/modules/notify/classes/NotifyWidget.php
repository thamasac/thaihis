<?php

namespace backend\modules\notify\classes;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of NotifyWidget
 *
 * @author appxq
 */
class NotifyWidget extends \backend\modules\ezforms2\classes\WidgetBuilder {

    //put your code here
    public $ezf_id = '';
    public $disabled = 0;
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $data_column = [];
    public $target = '';
    public $targetField = '';
    public $options = [];
    public $action = 'none'; //create, update, list, emr, del, view
    public $pageSize = '20';
    public $module = '';
    public $actionRequire = 0;
    public $tab = 'to_me';
    public $data_id = '';
    public $hide_tab = 0;
    public $notify_id = '';

    /**
     * @inheritdoc
     * @return NotifyWidget the newly created [[NotifyWidget]] instance.
     */
    public static function ui() {
        return Yii::createObject(NotifyWidget::className()); //, [get_called_class()]
    }

    public function module($module) {
        $this->module = $module;
        return $this;
    }

    public function actionRequire($actionRequire) {
        $this->actionRequire = $actionRequire;
        return $this;
    }

    public function hideTab($hide_tab) {
        $this->hide_tab = $hide_tab;
        return $this;
    }

    public function pageSize($pageSize) {
        $this->pageSize = $pageSize;
        return $this;
    }

    public function tab($tab) {
        $this->tab = $tab;
        return $this;
    }

    public function data_id($data_id) {
        $this->data_id = $data_id;
        return $this;
    }

    public function notify_id($notify_id) {
        $this->notify_id = $notify_id;
        return $this;
    }

    protected function buildUi() {
        if (empty($this->reloadDiv)) {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $url = Url::to(['/notify/notify/view',
                    'ezf_id' => $this->ezf_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'data_column' => EzfFunc::arrayEncode2String($this->data_column),
                    'popup' => 0,
                    'target' => $this->target,
                    'targetField' => $this->targetField,
                    'disabled' => $this->disabled,
                    'page_size' => $this->pageSize,
                    'module' => $this->module,
                    'tab' => $this->tab,
                    'actionRequire' => $this->actionRequire,
                    'data_id' => $this->data_id,
                    'notify_id' => $this->notify_id,
                    'hide_tab' => $this->hide_tab
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        $view = \Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }

}
