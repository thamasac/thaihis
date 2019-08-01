<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of MyWorkbenchWidget
 *
 */
class MyWorkbenchWidget extends WidgetBuilder {

    //put your code here
    public $ezf_id = '';
    public $ezf_match_id = '';
    public $ezf_name_id = '';
    public $disabled = 0;
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $options = [];
    public $pageSize = '20';
    public $dataOptions = [];
    public $docTypeId = '';
    public $docNameId = '';
    public $docDetailId = '';
    public $field_value = '';
    public $field_label = '';
    public $field_column = [];
    public $column_download;
    public $column_status;
    public $field_taget = '';

    /**
     * @param int $pageSize pageSize
     * @return $this the query object itself
     */
    public function columnDownload($column_download) {
        $this->column_download = $column_download;
        return $this;
    }

    /**
     * @param int $column_status columnStatus
     * @return $this the query object itself
     */
    public function columnStatus($column_status) {
        $this->column_status = $column_status;
        return $this;
    }

    public function ezf_match_id($ezf_match_id) {
        $this->ezf_match_id = $ezf_match_id;
        return $this;
    }

    public function ezf_name_id($ezf_name_id) {
        $this->ezf_name_id = $ezf_name_id;
        return $this;
    }

    /**
     * @inheritdoc
     * @return MyWorkbenchWidget the newly created [[MyWorkbenchWidget]] instance.
     */
    public static function ui() {
        return Yii::createObject(MyWorkbenchWidget::className()); //, [get_called_class()]
    }

    public function pageSize($pageSize) {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @param string $action use Action [create, update, list, emr, del, view] default none
     * @return $this the query object itself
     */
    public function dataOptions($dataOptions) {
        $this->docTypeId = $dataOptions['ezf_id'];
        $this->docNameId = $dataOptions['ezf_id_name'];
        $this->docDetailId = $dataOptions['ezf_id_detail'];
        $this->field_label = $dataOptions['field_label'];
        $this->field_value = $dataOptions['field_value'];
        $this->field_column = $dataOptions['field_column'];
        $this->field_taget = $dataOptions['field_taget'];
        return $this;
    }

    public function buildUiWorkbench() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $view = \Yii::$app->getView();

        $url = Url::to(['/ezforms2/my-workbench/' . $this->action,
                    'ezf_id' => $this->ezf_id,
                    'ezf_match_id' => $this->ezf_match_id,
                    'ezf_name_id' => $this->ezf_name_id,
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
                    'column_download' => $this->column_download,
                    'column_status' => $this->column_status,
        ]);
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;

        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', $options);
    }

    public function buildGrid() {
        $this->action = 'grid-workbench';

        return $this->buildUiWorkbench();
    }

    public function buildEmrGrid() {
        $this->action = 'emr-popup';

        return $this->buildUiWorkbench();
    }

}
