<?php 
    use yii\helpers\Html;
    use appxq\sdii\helpers\SDNoty;
    backend\modules\ezforms2\assets\ListdataAsset::register($this);
    backend\modules\ezforms2\assets\EzfToolAsset::register($this);
?> 

<div  style="margin-top:2px;margin-left: 4px;display: flex;flex-wrap: wrap" class="icon-rights" id="ezf_dad">
<?php foreach ($model as $m): ?>
    <?php
    $ezmShortTitle = cpn\chanpan\classes\utils\CNUtils::lengthName($m['ezm_short_title'], 3);
    $storageUrl = Yii::getAlias('@storageUrl');
    $noImg = $storageUrl . '/ezform/img/no_icon.png';
    $img = ($m['ezm_icon'] != '') ? "{$storageUrl}/module/{$m['ezm_icon']}" : $noImg;
    ?>
    <?php if($edit == '10'): ?>
        <?php if(!empty($m['ezm_id'])):?>
        <div data-id="<?= $m['ezm_id']?>" id='ezm-<?= $m['ezm_id']?>' class="dad" style="width:100px;">
            <div style="padding-left: 25%;">
                <button  class="btn btn-danger btn-xs mb-5 btnDeleteShortModule" data-id='<?= $m['ezm_id']?>'><i class="fa fa-minus"></i></button>
            </div>

            <div style="margin:5px;">
                <img src="<?= $img ?>" style="width: 65px;">    
                <li class="fa fa-arrow-right" style="margin-top:15px;"></li>&nbsp; 
            </div>
            <div><?= $ezmShortTitle ?></div>  
        </div>
        
        <?php endif; ?>
    
    <?php else: ?>
        <?php if(!empty($m['ezm_id'])):?>
            <a title="<?= $m['ezm_name'] ?>" href="/ezmodules/ezmodule/view?id=<?= $m['ezm_id']?>">  
                <img title="<?= $m['ezm_name'] ?>" src="<?= $img ?>" style="width: 45px;" class="img img-responsive img-rounded">    
                <div title="<?= $m['ezm_name'] ?>" class="mt-5"><?= $ezmShortTitle ?></div>            
            </a>
            <?php if ($m != end($model)): ?>
                &nbsp;<li class="fa fa-arrow-right" style="margin-top:15px;"></li>&nbsp; 
            <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>
    
    
<?php endforeach; ?>
</div>


<?php \richardfan\widget\JSRegister::begin();?>
<script>
     $('.btnDeleteShortModule').on('click', function(){
        let ezm_id = $(this).attr('data-id');
        let url = '/site/delete-short-module-select';
        $.post(url, {ezm_id:ezm_id}, function(data){
            console.log(data);
//            initModuleSelect();
            $('#ezm-'+ezm_id).remove();
            if(data.status == 'success') {
                <?= SDNoty::show('data.message', 'data.status')?>
            } else {
                <?= SDNoty::show('data.message', 'data.status')?>
            } 
        });
        return false;
     });
</script>
<?php \richardfan\widget\JSRegister::end();?>







<?php \appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    .mt-5{margin-top:5px;}
    .mb-5{margin-bottom: 5px;}
</style>
<?php \appxq\sdii\widgets\CSSRegister::end();?>
