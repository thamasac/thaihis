<?php 
    use yii\helpers\Html;
?>
<div  > 
    <strong class="clearfix">
        <img src="<?= $model['projecticon']; ?>" style="width:90px;border-radius:5px;" class="img-rounded"/>
    </strong>
    <strong class="clearfix">
        <?= cpn\chanpan\classes\utils\CNUtils::lengthName($model['projectacronym']) ?>
    </strong>
</div>
 