<?php
if ($model['visit_app_id'] && $model['visit_date'] > date('Y-m-d')) {
//    if ($model['visit_date'] > date('Y-m-d')) {
        $url = yii\helpers\Url::to(['/ezforms2/ezform-data/ezform', 'dataid' => $model['visit_app_id'], 'modal' => 'modal-ezform-main',
                    'reloadDiv' => 'order-history-lists', 'ezf_id' => '1506908933027139000']);
//    } else {
//        $url = yii\helpers\Url::to(['/ezforms2/ezform-data/ezform-view', 'dataid' => $model['visit_app_id'], 'modal' => 'modal-ezform-main',
//                    'reloadDiv' => 'order-history-lists', 'ezf_id' => '1506908933027139000']);
//    }
} else {
    $url = \yii\helpers\Url::to(['/patient/medical-history/show-detail', 'dataid' => $pt_id, 'visitid' => $model['id'],
                'visittype' => $model['visit_type']]);
}
?>
<a href="<?= $url ?>" class="list-group-item" data-modal="modal-ezform-main" data-id="<?= $model['id'] ?>">
  <?= appxq\sdii\utils\SDdate::mysql2phpThDate($model['visit_date']) ?> 
</a>
