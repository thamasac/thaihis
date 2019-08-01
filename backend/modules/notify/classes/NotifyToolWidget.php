<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\notify\classes;

use yii\helpers\Url;
use Yii;
use yii\helpers\Html;

/**
 * Description of NotifyToolWidget
 *
 * @author AR9
 */
class NotifyToolWidget extends \backend\modules\ezforms2\classes\WidgetBuilder {
    
    public $ezf_id = '';
    public $disabled = 0;
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $data_column = [];
    public $target = '';
    public $targetField = '';
    public $options = [];
    public $action = 'none'; //create, update, list, emr, del, view
    
    
    /**
     * @inheritdoc
     * @return NotifyToolWidget the newly created [[NotifyWidget]] instance.
     */
    public static function ui() {
        return Yii::createObject(NotifyToolWidget::className()); //, [get_called_class()]
    }

    //put your code here
    protected function buildUi() {
        if (empty($this->reloadDiv)) {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $url = Url::to(['/notify/default/notify-tool']);
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
