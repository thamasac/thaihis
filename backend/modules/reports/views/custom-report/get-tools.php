<?php 
    use yii\helpers\Html;
?>
<div class="row">
    
    <div class="col-md-12" id="2">
        <?= Html::encode("<div></div>")?>
    </div>
    
</div>

<?php \appxq\sdii\widgets\CSSRegister::begin()?>
<style>
    #cutom-tools {
        background: #d1d1d1;
        border-radius: 3px;
        padding: 5px;
        height: 600px;
    }
    .img-tools{
        height: 30px;
    }
</style>
<?php \appxq\sdii\widgets\CSSRegister::end();?>