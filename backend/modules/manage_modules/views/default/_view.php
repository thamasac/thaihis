<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
 
$url = ['/ezforms2/data-lists/index'];
$queryParams = Yii::$app->request->getQueryParams();

$imgPath = Yii::getAlias('@storageUrl');
$noImage = $imgPath.'/ezform/img/no_icon.png';
?>
 
<a href="#" data-id="<?= $model->ezm_id?>" class="media" style="position: relative;padding:10px;">
    <div class="media-body"> 
        <h4 class="list-group-item-heading">
            <span>
                <?php if(!empty($model->ezm_icon)):?>
                    <img src="<?= $model->icon_base_url.'/'.$model->ezm_icon?>" class="img-rounded" width="30" height="30">
                <?php else: ?>
                    <img src="<?= $noImage?>" class="img-rounded" width="30" height="30">
                <?php endif; ?>    
            </span> 
            <?= $model->ezm_name ?>
        </h4> 
        <p class="list-group-item-text">
            <div class="">
                <strong><?= Yii::t('ezform', 'Date') ?> : </strong>
                <?= \appxq\sdii\utils\SDdate::mysql2phpDateTime($model->updated_at) ?>
            </div>
        </p>
         
    </div>
</a>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
     $('a.media').on('click', function(){
        getModuleAll(); 
        let ezm_id = $(this).attr('data-id');
        let url = '<?= Url::to(['/manage_modules/default/create'])?>';
        $.post(url, {ezm_id:ezm_id},function(res){console.log(res);
            <?= appxq\sdii\helpers\SDNoty::show('res.message', 'res.status')?>
            getManageModules();
            hideLoadings();
            
        });
        return false;
     });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>