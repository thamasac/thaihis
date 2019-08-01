 
<?php 
    \cpn\chanpan\assets\JsGenConditionAssets::register($this);
?>
<div class="row">
    <div class="col-md-12">
        <h3><?= Yii::t('project','Assign to me')?></h3>
    </div>
</div>

<?php if(!empty($data)): ?>
<?php echo $this->render('_item',['data'=>$data, 'status'=>$status,'dataProvider'=>$dataProvider])?> 
<?php endif; ?> 