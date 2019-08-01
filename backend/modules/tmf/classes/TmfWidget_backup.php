<?php

namespace backend\modules\ezforms2\classes;

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
class TmfWidget_backup extends WidgetBuilder {

    //put your code here
    public $ezf_id = '';
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
    public $field_taget = '';

    /**
     * @inheritdoc
     * @return NotifyWidget the newly created [[NotifyWidget]] instance.
     */
    public static function ui() {
        return Yii::createObject(TmfWidget_backup::className()); //, [get_called_class()]
    }

    public function pageSize($pageSize) {
        $this->pageSize = $pageSize;
        return $this;
    }

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

    public function buildUi() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }
        $view = \Yii::$app->getView();
        $url = Url::to(['/ezforms2/tmf-backup/view',
                    'ezf_id' => $this->ezf_id,
                    'docTypeId' => $this->docTypeId,
                    'docNameId' => $this->docNameId,
                    'docDetailId' => $this->docDetailId,
                    'field_value' => $this->field_value,
                    'field_label' => $this->field_label,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'pageSize' => $this->pageSize,
                    'field_column' => $this->field_column,
                    'field_taget'=>$this->field_taget
        ]);
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', $options);
    }

}
