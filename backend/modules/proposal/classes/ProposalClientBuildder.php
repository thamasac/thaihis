<?php

namespace backend\modules\proposal\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

class ProposalClientBuildder extends Component {
     //put your code her
    public $widget_id = '';
    public $reloadDiv = '';
    public $options = [];
    public $main_ezf_id = "";
    public $action = "";
    public $randomize_id = "";
    public $user_create = "";
    public $user_update = "";
    public $group_name = "";
    public $module_id = "";
    
    /**
     * @param string $module_id target id
     * @return $this the query object itself
     */
    public function moduleId($module_id = '') {
        $this->module_id = $module_id;
        return $this;
    }

    /**
     * @param string $reloadDiv target id
     * @return $this the query object itself
     */
    public function reloadDiv($reloadDiv = '') {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }

    /**
     * @param string $proposal_ezf_id proposal_ezf_id
     * @return $this the query object itself
     */
    public function proposal_ezf_id($proposal_ezf_id) {
        $this->proposal_ezf_id = $proposal_ezf_id;
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
     * @param int $node_id pageSize
     * @return $this the query object itself
     */
    public function widget_id($widget_id) {
        $this->widget_id = $widget_id;
        return $this;
    }
    
    /**
     * @param int $options pageSize
     * @return $this the query object itself
     */
    public function options($options) {
        $this->options = $options;
        return $this;
    }
    


    /**
     * @inheritdoc
     * @return ProposalClientBuildder the newly created [[ProposalClientBuildder]] instance.
     */
    public static function ui() {
        return Yii::createObject(ProposalClientBuildder::className()); //, [get_called_class()]
    }

    public function buildPropersalClient() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }
        
        if($this->action == ''){
            $this->action = '/proposal/proposal/index';
        }

        $view = \Yii::$app->getView();

        $url = Url::to([$this->action,
                'options'=>$this->options,
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

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }
    
}
