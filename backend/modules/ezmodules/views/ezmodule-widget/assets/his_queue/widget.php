<?php

use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

$options['current_url'] = Url::to('/ezmodules/ezmodule/view', ['id' => Yii::$app->request->get('id')]);
$reloadDiv = isset($options['reloadDiv']) ? $options['reloadDiv'] : 'que-cashier-' . SDUtility::getMillisecTime();
$options['params_value'] = Yii::$app->request->get();
//\appxq\sdii\utils\VarDumper::dump($options['split_permission']);

echo \backend\modules\queue\classes\QueueWidget::ui()
    ->ezf_main_id(isset($options['ezf_id']) ? $options['ezf_id'] : '')
    ->ezf_ref_id(isset($options['refform']) ? $options['refform'] : [])
    ->ezf_ref_lavel2_id(isset($options['refform_level2']) ? $options['refform_level2'] : [])
    ->data_columns(isset($options['fields']) ? $options['fields'] : [])
    ->fields_search_one(isset($options['fields_search_one']) ? $options['fields_search_one'] : [])
    ->fields_search_multi(isset($options['fields_search_multi']) ? $options['fields_search_multi'] : [])
    ->condition(isset($options['condition']) ? $options['condition'] : [])
    ->group_by(isset($options['group_by']) ? $options['group_by'] : '')
    ->order_by(isset($options['order_by']) ? $options['order_by'] : '')
    ->dept_field(isset($options['dept_field']) ? $options['dept_field'] : '')
    ->doc_field(isset($options['doc_field']) ? $options['doc_field'] : '')
    ->split_permission(isset($options['split_permission']) ? $options['split_permission'] : '')
    ->bdate_field(isset($options['bdate_field']) ? $options['bdate_field'] : '')
    ->pic_field(isset($options['image_field']) ? $options['image_field'] : '')
    ->custom_label(isset($options['custom_label']) ? $options['custom_label'] : [])
    ->template_content(isset($options['template_content']) ? $options['template_content'] : '')
    ->action(isset($options['action']) ? $options['action'] : '')
    ->current_url(isset($options['data_url']) ? $options['data_url'] : $current_url)
    ->param(isset($options['param']) ? $options['param'] : [])
    ->que_type(Yii::$app->request->get('que_type-' . $reloadDiv, '1'))
    ->target(Yii::$app->request->get('target', ''))
    ->title(isset($options['title']) ? $options['title'] : '')
    ->position(isset($options['position']) ? $options['position'] : '')
    ->radio_check(isset($options['radio_check']) ? $options['radio_check'] : false)
    ->icon(isset($options['icon']) ? $options['icon'] : '')
    ->element_id(isset($options['element_id']) ? $options['element_id'] : 'element_id')
    ->reloadDiv($reloadDiv)->clearDiv(isset($options['fields_search_cleardiv']) ? $options['fields_search_cleardiv'] : '')
    ->params_value(isset($options['params_value']) ? $options['params_value'] : '')
    ->btn_report(isset($options['btn_report']) ? $options['btn_report']:false)
    ->buildUi();
?>
