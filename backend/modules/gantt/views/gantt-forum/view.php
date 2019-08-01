
<?php
//\appxq\sdii\utils\VarDumper::dump($model['id']);
?>
<div class="panel panel-info">
    <div class="panel-heading"><?= "<i class='fa fa-comments'></i> ".Yii::t('chanpan', 'Comment') ?></div>
    <div class="panel-body">
        <?php
        echo backend\modules\gantt\classes\ForumBuilder::Community()
                ->type('webboard')
                //->parent_id($id)
                ->reloadDiv('webboard')
                ->parent_id($parent_id)
                ->object_id($model['id']) //ezfid
                ->buildCommunity();
        ?>
    </div>
</div>

