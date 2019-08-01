
<label class="list-group-item">
  <input type="checkbox" name="sitecode[]" value="<?= $model['code'] ?>">
    <span><i class="fa fa-hospital-o draggable"></i> <?= $model['name'] ?></span>  
</label>
<?php
if ($index == 0) {
    $url = \yii\helpers\Url::to(['/patient/medical-history/visit', 'dataid' => $dataid]);
    $this->registerJS("
        $('.list-group-item input[type=\"checkbox\"]').attr('checked','');
        
        $.post('$url', $('#form-visit-list').serialize()).done(function (result) {
            $('#$reloadChildDiv').html(result);
        }).fail(function() {
            console.log('server error');
        });
        
    ");
}
?>
