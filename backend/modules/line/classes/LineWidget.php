<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\line\classes;

use yii\helpers\Url;
use Yii;
use yii\helpers\Html;

/**
 * Description of LineWidget
 *
 * @author AR9
 */
class LineWidget extends \backend\modules\ezforms2\classes\WidgetBuilder {
    
    public $ezf_id = '';
    public $disabled = 0;
    public $modal = 'modal-ezform-main';
    public $title = '';


    /**
     * @inheritdoc
     * @return LineWidget the newly created [[NotifyWidget]] instance.
     */
    public static function ui() {
        return Yii::createObject(LineWidget::className()); //, [get_called_class()]
    }
    
    public function title($title){
        $this->title = $title;
        return $this;
    }

    //put your code here
    protected function buildUi() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $url = Url::to(['/line/default/ajax','title' => $this->title,'reloadDiv'=>$this->reloadDiv]);
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
