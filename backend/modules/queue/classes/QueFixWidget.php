<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\queue\classes;

use appxq\sdii\utils\VarDumper;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\utils\SDUtility;

/**
 * Description of QueueWidget
 *
 * @author AR9
 */
class QueFixtWidget extends QueueWidget
{

    /**
     * @inheritdoc
     * @return CounterListWidget the newly created [[CounterListWidget]] instance.
     */
    public static function ui()
    {
        return Yii::createObject(QueFixtWidget::className());
    }

    /**
     *
     * @return Html
     */
    public function buildUi()
    {

        if (empty($this->reloadDiv)) {
            $this->reloadDiv = 'que-fix-' . Yii::$app->uniqueId;
        }
        if (empty($this->current_url) || $this->current_url == '') {
            $this->current_url = strrpos(Url::current(), '&target') > 0 ? substr(Url::current(), 0, strrpos(Url::current(), '&target')) : Url::current();
        }
        $check_active = '';
        if (!empty($this->param) && is_array($this->param)) {
            foreach ($this->param as $vParam) {
                if (isset($vParam['param_active']) && $vParam['param_active'] == 1) {
                    $check_active = Yii::$app->request->get($vParam['name'], '');
                }
            }
        }
        $url = Url::to(['/queue',
            'ezf_main_id' => $this->ezf_main_id,
            'ezf_ref_id' => EzfFunc::arrayEncode2String($this->ezf_ref_id),
            'data_columns' => EzfFunc::arrayEncode2String($this->data_columns),
            'condition' => EzfFunc::arrayEncode2String($this->condition),
            'group_by' => $this->group_by,
            'dept_field' => $this->dept_field,
            'doc_field' => $this->doc_field,
            'bdate_field' => $this->bdate_field,
            'pic_field' => $this->pic_field,
            'template_content' => $this->template_content,
            'que_type' => $this->que_type,
            'target' => $this->target,
            'current_url' => $this->current_url,
            'action' => $this->action,
            'reloadDiv' => $this->reloadDiv,
            'title' => $this->title,
            'radio_check' => $this->radio_check,
            'icon' => $this->icon,
            'param' => EzfFunc::arrayEncode2String($this->param),
            'custom_label' => EzfFunc::arrayEncode2String($this->custom_label),
            'fields_search_one' => EzfFunc::arrayEncode2String($this->fields_search_one),
            'fields_search_multi' => EzfFunc::arrayEncode2String($this->fields_search_multi),
            'position' => EzfFunc::arrayEncode2String($this->position),
            'element_id' => $this->element_id,
            'search_field' => Yii::$app->request->get('search_field', ''),
            'searchBoxOne' => Yii::$app->request->get('searchBoxOne', ''),
            'check_active' => $check_active,
            'widget_que_type' => $this->widget_que_type,
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

}
