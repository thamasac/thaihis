<?php

use backend\modules\ezmodules\classes\ModuleFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
    
<?php
if (isset($model) && !empty($model)) {
    foreach ($model as $key => $value) {
        $count_field = \backend\modules\ezforms2\models\EzformCommunity::find()
                    ->where('parent_id=:parent_id AND type=:type', [':parent_id'=>$value['id'], ':type'=>$value['type']])
                    ->count();
        $url = yii\helpers\Url::to(['/ezforms2/ezform-community/query-comment',
            'dataid' => $value['dataid'],
            'object_id' => $value['object_id'],
            'query_tool' => $value['query_tool'],
            'parent_id' => $value['id'],
            'field' => $value['field'],
            'type' => $value['type'],
            'limit' => $limit,
            'modal' => $modal,
            ]);
        
        echo $this->renderAjax('_query_item', [
                'value' => $value,
                'modal' => $modal,
                'dataid' => $dataid,
                'count_field' => $count_field,
                'url' =>$url,
            ]);
    }
}?>

<?php
if ($moreitem == 1) {
    ?>
    <a id="more_query-<?= $object_id ?>" data-start="<?= $start ?>" class="list-group-item text-center"><strong><?= Yii::t('ezmodule', 'More Query Tool') ?></strong></a>
<?php } ?>

<?php $this->registerJs("
$('#count-query-$object_id').html('$count');


"); ?>

