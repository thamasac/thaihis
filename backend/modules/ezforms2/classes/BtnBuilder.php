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
class BtnBuilder extends Component {

    //put your code here
    public $tag = 'button';
    public $ezf_id = '';
    public $dataid = '';
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $initdata = [];
    public $target = '';
    public $targetField = '';
    public $label = '';
    public $data_column = [];
    public $popup = 1;
    public $addbtn = 1;
    public $options = [];
    public $action = 'none'; //create, update, list, emr, del, view
    public $readonly = 0;
    public $version = '';
    public $db2 = 0;
    public $varname = 0;
    public $reloadPage = '';
    
    /**
     * @param string $db2 show double data
     * @return $this the query object itself
     */
    public function varname() {
        $this->varname = 1;
        return $this;
    }
    
    /**
     * @param string $db2 show double data
     * @return $this the query object itself
     */
    public function db2() {
        $this->db2 = 1;
        return $this;
    }
    
    /**
     * @param boolean $readonly view readonly
     * @return $this the query object itself
     */
    public function readonly($readonly) {
        $this->readonly = $readonly;
        return $this;
    }
    
    /**
     * @param boolean $popup view or emr popup
     * @return $this the query object itself
     */
    public function popup($popup) {
        $this->popup = $popup;
        return $this;
    }

    /**
     * @param boolean $addbtn view or emr popup
     * @return $this the query object itself
     */
    public function addbtn($addbtn) {
        $this->addbtn = $addbtn;
        return $this;
    }

    /**
     * @param string $tag button or a
     * @return $this the query object itself
     */
    public function tag($tag = null) {
        if (isset($tag)) {
            $this->tag = $tag;
        }
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
     * @param string $label show icon and label
     * @return $this the query object itself
     */
    public function label($label = '') {
        $this->label = $label;
        return $this;
    }
    
    public function version($version = '') {
        $this->version = $version;
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
     * @param string $reloadPage div id for reload page
     * @return $this the query object itself
     */
    public function reloadPage($reloadPage = '') {
        $this->reloadPage = $reloadPage;
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
     * @param array $initdata default data input
     * @return $this the query object itself
     */
    public function initdata($initdata) {
        $this->initdata = $initdata;
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
     * @param array $options options Html
     * @return $this the query object itself
     */
    public function options($options) {
        $this->options = $options;
        return $this;
    }

    /**
     * @inheritdoc
     * @return BtnBuilder the newly created [[BtnBuilder]] instance.
     */
    public static function btn() {
        return Yii::createObject(BtnBuilder::className());//, [get_called_class()]
    }

    protected function buildBtnOpenForm() {
        $options = $this->options;
        $options['data-modal'] = $this->modal;
        $options['data-url'] = Url::to(['/ezforms2/ezform-data/' . $this->action,
                    'ezf_id' => $this->ezf_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'target' => $this->target,
                    'dataid' => $this->dataid,
                    'targetField' => $this->targetField,
                    'version' =>$this->version,
                    'db2' =>$this->db2,
                    'reloadPage' => base64_encode($this->reloadPage),
                    'initdata' => EzfFunc::arrayEncode2String($this->initdata),
        ]);

        if (isset($options['class'])) {
            if ($this->action == 'delete') {
                $options['class'] .= ' ezform-delete';
            } else {
                $options['class'] .= ' ezform-main-open';
            }
        } else {
            $options['class'] = 'btn btn-success ezform-main-open';

            if (!empty($this->dataid)) {
                $options['class'] = 'btn btn-primary ezform-main-open';
            }
            if ($this->action == 'ezform-view') {
                $options['class'] = 'btn btn-default ezform-main-open';
            } elseif ($this->action == 'delete') {
                $options['class'] = 'btn btn-danger ezform-delete';
            }
        }

        return Html::tag($this->tag, $this->label, $options);
    }
    
    public function buildBtnAdd() {
        $this->action = 'ezform';
        
        if (empty($this->label)) {
            $this->label = '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'New');
        }
        
        return $this->buildBtnOpenForm();
    }
    
    public function buildBtnEdit($dataid) {
        $this->action = 'ezform';
        $this->dataid = $dataid;
        
        if (empty($this->label)) {
            $this->label = '<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('app', 'Update');
        }
        
        return $this->buildBtnOpenForm();
    }
    
    public function buildBtnDoubleData($dataid) {
        $this->action = 'ezform';
        $this->dataid = $dataid;
        $this->db2 = 1;
        
        if (empty($this->label)) {
            $this->label = '<i class="glyphicon glyphicon-duplicate"></i> ' . Yii::t('app', 'Double Data');
        }
        
        return $this->buildBtnOpenForm();
    }
    
    public function buildBtnDelete($dataid) {
        $this->action = 'delete';
        $this->dataid = $dataid;
        
        if (empty($this->label)) {
            $this->label = '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', 'Delete');
        }
        
        return $this->buildBtnOpenForm();
    }
    
    public function buildBtnView($dataid) {
        $this->action = 'ezform-view';
        $this->dataid = $dataid;
        
        if (empty($this->label)) {
            $this->label = '<i class="glyphicon glyphicon-eye-open"></i> ' . Yii::t('ezform', 'Open Form');
        }
        
        return $this->buildBtnOpenForm();
    }

    protected function buildBtnOpenGrid() {
        $options = $this->options;
        $options['data-modal'] = $this->modal;
        $options['data-url'] = Url::to(['/ezforms2/ezform-data/' . $this->action,
                    'ezf_id' => $this->ezf_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'data_column' => EzfFunc::arrayEncode2String($this->data_column),
                    'target' => $this->target,
                    'popup' => $this->popup,
                    'addbtn' => $this->addbtn,
                    'targetField' => $this->targetField,
                    'disabled' => $this->readonly,
                    'db2' =>$this->db2,
                    'varname' =>$this->varname,
        ]);

        if (isset($options['class'])) {
            $options['class'] .= ' ezform-main-open';
        } else {
            $options['class'] = 'btn btn-info ezform-main-open';
        }

        if (empty($this->label)) {
            $this->label = '<i class="glyphicon glyphicon-th-list"></i> ' . Yii::t('app', 'View');

            if ($this->action == 'emr-popup') {
                $this->label = '<i class="glyphicon glyphicon-th-list"></i> ' . Yii::t('ezform', 'EMR');
            }
        }

        return Html::tag($this->tag, $this->label, $options);
    }
    
    public static function btnCreateEzForm($modal = 'modal-ezform', $options=[]) {
        $options['data-modal'] = $modal;
        $options['data-url'] = Url::to(['/ezforms2/ezform/create']);
        
        if (isset($options['class'])) {
            $options['class'] .= ' ezform-create';
        } else {
            $options['class'] = 'btn btn-success ezform-create';
        }

        $label = isset($options['label'])?$options['label']:'';
        $tag = isset($options['tag'])?$options['tag']:'a';
        unset($options['label']);
        unset($options['tag']);
        
        if (empty($label)) {
                $label = '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('ezform', 'New EzForm');
        }

        return Html::tag($tag, $label, $options);
    }
    
    public function buildBtnGrid() {
        $this->action = 'view';
        
        return $this->buildBtnOpenGrid();
    }
    
    public function buildBtnEmr() {
        $this->action = 'emr-popup';
        
        return $this->buildBtnOpenGrid();
    }
    
    public function buildBtnAnnotated() {
        $this->action = 'ezform-annotated';
        $this->modal = 'modal-ezform-info';
        
        if (empty($this->label)) {
            $this->label = '<i class="glyphicon glyphicon-info-sign"></i> ' . Yii::t('ezform', 'Annotated');
        }
        
        return $this->buildBtnOpenForm();
    }
    
    public function buildBtnDictionary() {
        $this->action = 'ezform-dictionary';
        $this->modal = 'modal-ezform-info';
        
        if (empty($this->label)) {
            $this->label = '<i class="fa fa-book"></i> ' . Yii::t('ezform', 'Dictionary');
        }
        
        return $this->buildBtnOpenForm();
    }
}
