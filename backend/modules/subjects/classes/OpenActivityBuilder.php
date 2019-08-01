<?php

namespace backend\modules\subjects\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of OpenActivityBuilder
 *
 */
class OpenActivityBuilder extends Component {

    //put your code here
    public $subject_profile_ezf = '';
    public $subject_detail_ezf = '';
    public $field_subject = '';
    public $schedule_id='';
    public $profile_column = [];
    public $detail_column = [];
    public $detail_column2 = [];
    public $reloadDiv = '';
    public $action = '';
    public $modal = 'modal-ezform-activity';
    public $module_id = '';
    

    /**
     * @param string $reloadDiv target id
     * @return $this the query object itself
     */
    public function reloadDiv($reloadDiv = '') {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }
    
    /**
     * @param string $reloadDiv target id
     * @return $this the query object itself
     */
    public function modal($modal = '') {
        $this->modal = $modal;
        return $this;
    }
    
    /**
     * @param string $reloadDiv target id
     * @return $this the query object itself
     */
    public function moduleId($module_id = '') {
        $this->module_id = $module_id;
        return $this;
    }

    /**
     * @param string $subject_profile_ezf ezf id
     * @return $this the query object itself
     */
    public function subjectProfileEzf($subject_profile_ezf) {
        $this->subject_profile_ezf = $subject_profile_ezf;
        return $this;
    }
    
    /**
     * @param string $subject_profile_ezf ezf id
     * @return $this the query object itself
     */
    public function fieldSubject($field_subject) {
        $this->field_subject = $field_subject;
        return $this;
    }
    
    /**
     * @param string $subject_detail_ezf ezf id
     * @return $this the query object itself
     */
    public function subjectDetailEzf($subject_detail_ezf) {
        $this->subject_detail_ezf = $subject_detail_ezf;
        return $this;
    }
    
    /**
     * @param string $schedule_id ezf id
     * @return $this the query object itself
     */
    public function scheduleId($schedule_id) {
        $this->schedule_id = $schedule_id;
        return $this;
    }
    
    /**
     * @param string $profile_column ezf id
     * @return $this the query object itself
     */
    public function profileColumn($profile_column) {
        $this->profile_column = $profile_column;
        return $this;
    }
    
    /**
     * @param string $detail_column ezf id
     * @return $this the query object itself
     */
    public function detailColumn($detail_column) {
        $this->detail_column = $detail_column;
        return $this;
    }

    /**
     * @param string $detail_column ezf id
     * @return $this the query object itself
     */
    public function detailColumn2($detail_column2) {
        $this->detail_column2 = $detail_column2;
        return $this;
    }
    /**
     * @param string $cate_ezf_id ezf id
     * @return $this the query object itself
     */
    public function action($action) {
        $this->action = $action;
        return $this;
    }


    /**
     * @inheritdoc
     * @return OpenActivityBuilder the newly created [[OpenActivityBuilder]] instance.
     */
    public static function ui() {
        return Yii::createObject(OpenActivityBuilder::className()); //, [get_called_class()]
    }

    public function buildOpenActivity() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $view = \Yii::$app->getView();
        $url = Url::to(['/subjects/open-activity/index',
                    'reloadDiv' => $this->reloadDiv,
                    'subject_profile_ezf' => $this->subject_profile_ezf,
                    'subject_detail_ezf' => $this->subject_detail_ezf,
                    'schedule_id'=>$this->schedule_id,
                    'profile_column'=>$this->profile_column,
                    'detail_column'=>$this->detail_column,
                    'detail_column2'=>$this->detail_column2,
                    'field_subject'=>$this->field_subject,
                    'modal'=>$this->modal,
                    'module_id'=>$this->module_id,

        ]);
        $options = [];

        if (is_array($options)) {
            $options['action'] = 'index';
            $options["id"] = $this->reloadDiv;
            $options['data-url'] = $url;
        }

        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', $options);
    }

}
