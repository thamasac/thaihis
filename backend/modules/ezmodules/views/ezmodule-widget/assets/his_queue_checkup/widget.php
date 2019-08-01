<?php

use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

$current_url = strrpos(Url::current(), '&target') > 0 ? substr(Url::current(), 0, strrpos(Url::current(), '&target')) : Url::current();
$options['current_url'] = $current_url;
$reloadDiv = isset($options['reloadDiv']) ? $options['reloadDiv'] : 'que-checkup-' . SDUtility::getMillisecTime();

//\backend\modules\patient\Module::$formID['receipt_mas'];
echo \backend\modules\queue\classes\QueFixtWidget::ui()
    ->ezf_main_id('1503589101005614900')
    ->ezf_ref_id(['1506694193013273800','1536726852029196700','1504537671028647300','1514016599071774100','1503378440057007100'])
    ->data_columns(isset($options['fields']) ? $options['fields'] : [])
    ->fields_search_one(isset($options['fields_search_one']) ? $options['fields_search_one'] : [])
    ->fields_search_multi(isset($options['fields_search_multi']) ? $options['fields_search_multi'] : [])
    ->group_by(isset($options['group_by']) ? $options['group_by'] : '')
    ->dept_field(isset($options['dept_field']) ? $options['dept_field'] : '')
    ->doc_field(isset($options['doc_field']) ? $options['doc_field'] : '')
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
    ->reloadDiv($reloadDiv)->widget_que_type('queue_checkup')
    ->buildUi();
?>
