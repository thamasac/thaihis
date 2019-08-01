<?php

use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

$options['current_url'] = Url::to('/ezmodules/ezmodule/view', ['id' => Yii::$app->request->get('id')]);
$reloadDiv = isset($options['reloadDiv']) ? $options['reloadDiv'] : 'que-cashier-' . SDUtility::getMillisecTime();
$options['params_value'] = Yii::$app->request->get();

echo \backend\modules\queue\classes\QueueWidget::ui()->url_controller('/queue/default/queue-cashier')
        ->ezf_main_id($options['ezf_main_id'])
        ->ezf_ref_id($options['ezf_ref_id'])
        ->data_columns(isset($options['fields']) ? $options['fields'] : [])
        ->fields_search_one(isset($options['fields_search_one']) ? $options['fields_search_one'] : [])
        ->bdate_field(isset($options['bdate_field']) ? $options['bdate_field'] : '')
        ->pic_field(isset($options['image_field']) ? $options['image_field'] : '')
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
        ->reloadDiv($reloadDiv)->widget_que_type('queue_cashier')
        ->condition(isset($options['condition']) ? $options['condition'] : [])
        ->clearDiv(isset($options['fields_search_cleardiv']) ? $options['fields_search_cleardiv'] : '')
        ->params_value(isset($options['params_value']) ? $options['params_value'] : '')
        ->buildUi();
?>
