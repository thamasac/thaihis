<?php
    use yii\helpers\Html;
    use appxq\sdii\helpers\SDNoty;
    $storageUrl = Yii::getAlias('@storageUrl');
    $noImg = $storageUrl.'/ezform/img/no_icon.png';
    $img = (isset($model['ezm_icon'])) ? "{$storageUrl}/module/{$model['ezm_icon']}" : $noImg;
?>
<div>
    
</div>

<div class="media">
    <a href="#" data-id='<?= $model['ezm_id'] ?>' class="btnAddModuleSelect">
        <div class="media-left media-middle">
            <img src="<?= $img ?>" class="media-object"/>
        </div>
        <div class="media-body">
            <h4 class="media-heading"><?= $model['ezm_short_title'] ?></h4>
            <?= $model['ezm_name'] ?>
        </div>  
    </a>
</div>


<?php \richardfan\widget\JSRegister::begin();?>
<script>
     $('.btnAddModuleSelect').on('click', function(){
        let ezm_id = $(this).attr('data-id');
        //console.log(ezm_id);
        let url = '/site/add-short-module-select';
        $.post(url, {ezm_id:ezm_id}, function(result){
            if(result.status == 'success') {
                <?= SDNoty::show('result.message', 'result.status')?>
            } else {
                <?= SDNoty::show('result.message', 'result.status')?>
            } 
            initModuleSelect();
            
        });
        return false;
     });
</script>
<?php \richardfan\widget\JSRegister::end();?>
