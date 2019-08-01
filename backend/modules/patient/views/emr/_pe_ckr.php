<?php

if ($model) {
    echo yii\helpers\Html::a('<span class="fa fa-male"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-info ezform-main-open ', 'data-modal' => 'modal-ezform-main',
        'data-url' => yii\helpers\Url::to(['/ezforms2/ezform-data/ezform-view',
            'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'dataid' => $model['id']])]);
} else {
    //echo Yii::t('patient', 'No results'); ?>
<script type="text/javascript">
    $('input[name="EZ1514016599071774100[ckr_pe]"][value="2"]').prop('checked',true);
    //$('input[name="EZ1514016599071774100[ckr_breast]"][value="2"]').prop('checked',true);
</script>
<?php    
}
?>