<?php
$url = \yii\helpers\Url::to(['/patient/medical-history/show-detail', 'dataid' => $dataid, 'visitid' => $model['id'],
            'visittype' => $model['visit_type'], 'visitan' => $model['visit_admit_an'], 'visitdate' => $model['visit_date']]);
?>
<a href="<?= $url ?>" class="list-group-item" data-id="<?= $model['id'] ?>">
  <span><i class="fa fa-calendar draggable"></i></span> <?= appxq\sdii\utils\SDdate::mysql2phpThDateSmall($model['visit_date']) ?></span>  
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
