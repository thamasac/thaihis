<?php
$current_url = \yii\helpers\Url::to(['/ezmodules/ezmodule/view','id'=>$module,'addon'=>$addon]);

echo backend\modules\ezforms2\classes\TargetBuilder::targetWidget()
        ->ezf_id($options['ezf_id'])
        ->fields($options['fields'])
        ->fields_search($options['fields_search'])
        ->image_field($options['image_field'])
        ->age_field(isset($options['age_field'])?$options['age_field']:'')
        ->dataid($target)
        ->current_url($current_url)
        ->template_items($options['template_items'])
        ->template_selection($options['template_selection'])
        ->buildTarget();
?>
