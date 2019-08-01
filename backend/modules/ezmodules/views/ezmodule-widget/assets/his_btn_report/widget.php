<?php

use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
  'options' => $options,
  'widget_config' => $widget_config,
  'model' => $model,
  'modelOrigin'=>$modelOrigin,
  'menu' => $menu,
  'module' => $module,
  'addon' => $addon,
  'filter' => $filter,
  'reloadDiv' => $reloadDiv,
  'dataFilter' => $dataFilter,
  'modelFilter' => $modelFilter,
  'target' => $target,
 */

//$options['current_url'] = Url::to('/ezmodules/ezmodule/view', ['id' => Yii::$app->request->get('id')]);
$reloadDiv = isset($options['reloadDiv']) ? $options['reloadDiv'] : 'btn-report-' . SDUtility::getMillisecTime();
//$options['params_value'] = Yii::$app->request->get();
//\appxq\sdii\utils\VarDumper::dump($options);

if(Yii::$app->request->get('target') != '') {
    echo \backend\modules\thaihis\classes\BtnReportWidget::ui()
        ->ezf_main_id(isset($options['ezf_id']) ? $options['ezf_id'] : '')
        ->ezf_ref_id(isset($options['refform']) ? $options['refform'] : [])
        ->condition(isset($options['condition']) ? $options['condition'] : [])
        ->group_by(isset($options['group_by']) ? $options['group_by'] : '')
        ->template_content(isset($options['template_content']) ? $options['template_content'] : '')
        ->target(Yii::$app->request->get('target', ''))
        ->btn_text(isset($options['btn_text']) ? $options['btn_text'] : '')
        ->btn_color(isset($options['btn_color']) ? $options['btn_color'] : '')
        ->btn_style(isset($options['btn_style']) ? $options['btn_style'] : '')
        ->btn_icon(isset($options['btn_icon']) ? $options['btn_icon'] : '')
        ->match_field(isset($options['match_field']) ? $options['match_field'] : [])
        ->header_report(isset($options['header_report']) ? $options['header_report'] : '')
        ->options($options)
        ->buildUi();
}
?>
