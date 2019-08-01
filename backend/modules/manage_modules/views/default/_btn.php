<div class="row">
    <div class="col-md-12">
        <div class="col-md-3 pull-right">
            <?= yii\helpers\Html::button("<i class='fa fa-floppy-o '></i> ".Yii::t('chanpan','Save'), ['id'=>'btnSave','class'=>'btn btn-primary btn-block'])?>
        </div>
    </div>
</div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
     $('#btnSave').click(function(){
       let url = '<?= yii\helpers\Url::to(['/manage_modules/default/update']);?>';  
       $.get(url, {status:'view'}, function(data){
           if(data.status == 'success'){
               location.reload();
           }
       });
       return false;
     });
</script>
<?php \richardfan\widget\JSRegister::end();?>