<?php

/**
 * Created by PhpStorm.
 * User: AR9
 * Date: 9/12/2561
 * Time: 17:58
 */

namespace backend\modules\thaihis\classes;

use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\classes\EzfFunc;
use yii\base\Component;
use yii\helpers\Url;
use Yii;
use yii\helpers\Html;

class BtnReportWidget extends Component {

    public $ezf_main_id = '';
    public $ezf_ref_id = [];
    public $condition = [];
    public $group_by = '';
    public $template_content = '';
    public $target = '';
    public $btn_text = '';
    public $btn_icon = '';
    public $btn_style = '';
    public $btn_color = 'btn-default';
    public $reloadDiv = 'btn-report';
    public $match_field = [];
    public $header_report = '';
    public $options = [];

    public static function ui() {
        return Yii::createObject(BtnReportWidget::className());
    }

    public function ezf_main_id($ezf_main_id) {
        $this->ezf_main_id = $ezf_main_id;
        return $this;
    }

    public function ezf_ref_id($ezf_ref_id) {
        $this->ezf_ref_id = $ezf_ref_id;
        return $this;
    }

    public function reloadDiv($reloadDiv) {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }

    public function condition($condition) {
        if (isset($condition) && is_array($condition) && !empty($condition)) {
            foreach ($condition as $kCon => $valCon) {
                $strCon = str_replace('{', '', $valCon['value']);
                $strCon = str_replace('}', '', $strCon);
                $condition[$kCon]['value'] = Yii::$app->request->get($strCon, $valCon['value']);
            }
        }
        $this->condition = $condition;
        return $this;
    }

    public function group_by($group_by) {
        $this->group_by = $group_by;
        return $this;
    }

    public function template_content($template_content) {
        $this->template_content = $template_content;
        return $this;
    }

    public function target($target) {
        $this->target = $target;
        return $this;
    }

    public function btn_text($btn_text) {
        $this->btn_text = $btn_text;
        return $this;
    }

    public function btn_icon($btn_icon) {
        $this->btn_icon = $btn_icon;
        return $this;
    }

    public function btn_color($btn_color) {
        $this->btn_color = $btn_color;
        return $this;
    }

    public function btn_style($btn_style) {
        $this->btn_style = $btn_style;
        return $this;
    }

    public function match_field($match_field) {
        $this->match_field = $match_field;
        return $this;
    }

    public function header_report($header_report) {
        $this->header_report = $header_report;
        return $this;
    }

    public function options($options) {
        $this->options = $options;
        return $this;
    }

    public function buildUi() {
        $txtUrl = isset($this->options['action_report']) && $this->options['action_report'] == '2' ? '/thaihis/btn-report/btn-report2' : '/thaihis/btn-report/btn-report';

        $visitid = Yii::$app->request->get('visitid', '');
        $url = Url::to([$txtUrl,
                    'ezf_main_id' => $this->ezf_main_id,
                    'ezf_ref_id' => EzfFunc::arrayEncode2String($this->ezf_ref_id),
                    'reloadDiv' => $this->reloadDiv,
                    'condition' => EzfFunc::arrayEncode2String($this->condition),
                    'group_by' => EzfFunc::arrayEncode2String($this->group_by),
                    'target' => $this->target,
                    'btn_text' => $this->btn_text,
                    'btn_style' => $this->btn_style,
                    'btn_color' => $this->btn_color,
                    'btn_icon' => $this->btn_icon,
                    'visitid' => $visitid,
                    'header_report' => $this->header_report,
                    'match_field' => EzfFunc::arrayEncode2String($this->match_field)
        ]);
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;

        $view = Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }

}
