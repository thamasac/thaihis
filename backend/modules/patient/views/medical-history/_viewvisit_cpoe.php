<?php
$url = \yii\helpers\Url::to(['/patient/medical-history/show-detail-cpoe', 'target' => $target, 'visitid' => $model['id'],'options'=>$options,'modal'=>$modal,
            'visittype' => isset($model['visit_type'])?$model['visit_type']:null, 'visitan' => isset($model['visit_admit_an'])?$model['visit_admit_an']:null, 'visitdate' => isset($model['visit_date'])?$model['visit_date']:null]);
?>
<a href="<?= $url ?>" class="list-group-item" data-id="<?= $model['id'] ?>">
  <?= appxq\sdii\utils\SDdate::mysql2phpDate($model['visit_date']) ?> 
</a>
<?php
if ($index == 0) {
    $visit_id = $model['id'];
    $this->registerJS("
        $('.list-group a[data-id=\"$visit_id\"]').addClass('active');
        
        $.get('$url').done(function (result) {
            $('#$reloadChildDiv').html(result);
        }).fail(function() {
            console.log('server error');
        });
    ");
}
?>
