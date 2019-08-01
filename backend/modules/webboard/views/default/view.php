<div class="panel panel-default">
    <div class="panel-heading">
        <a class="btn btn-default" href="<?= \yii\helpers\Url::to(['/ezmodules/ezmodule/view?id=1521647584047559700&tab=1521647660065871800&addon=0'])?>">  
            <i class="fa fa-chevron-left"></i> <?= Yii::t('chanpan','Back')?>
        </a>  
        <label style="    font-size: 14pt;
    color: #717171;
    font-weight: normal;
    font-family: monospace;"> <?= $model['title']?> <i class="glyphicon glyphicon-calendar"></i> <?= $model['create_date']?></label> 
    </div>
    <div class="panel-body">
        <?= $model['detail']?>
        
    </div>
</div>
<?php 
    //\appxq\sdii\utils\VarDumper::dump($model['id']);
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('chanpan','Comment')?></div>
    <div class="panel-body">
       <?php echo backend\modules\ezforms2\classes\CommunityBuilder::Community()
        ->type('webboard')
        //->parent_id($id)
        ->reloadDiv('webboard')
        ->object_id($model['id']) //ezfid
        ->buildCommunity();?>
    </div>
</div>
 
