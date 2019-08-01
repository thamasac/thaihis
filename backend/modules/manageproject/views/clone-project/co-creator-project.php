<?php 
    \cpn\chanpan\assets\JsGenConditionAssets::register($this);
?>
<div class="row">
    <div class="col-md-12">
        <h3><?= Yii::t('project','Co-Creator Projects')?></h3>
    </div>
</div>

<?php if(!empty($data)): ?>
<?php echo $this->render('_item',['data'=>$data, 'status'=>'co','dataProvider'=>$dataProvider, 'element_id'=>'co-creator'])?> 
<?php endif; ?> 
