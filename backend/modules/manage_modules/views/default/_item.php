<?php
    use yii\helpers\Url;
    use backend\modules\manage_modules\components\CNMyModule;
    $imgPath = Yii::getAlias('@storageUrl');
    $noImage = $imgPath . '/ezform/img/no_icon.png';
?>
<?php if ($status == 'update'): ?>
 
<div data-id='<?= $rs['id'] ?>' class="dads-children  drags col-md-6 col-xs-6" style="    margin-bottom: 15px;">
        <div id="btn-<?= $rs['id'] ?>" class="btn-item pull-right" style="    margin-bottom: 5px;">
            <button class="btn btn-default btn-edit" data-action='update' data-url="<?= Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1528936267089555700&dataid=' . $rs['id'] . '&modal=modal-project&reloadDiv=modal-divview-1528936267089555700&db2=0']) ?>"><i class="fa fa-pencil"></i></button>
            <button class="btn btn-default btn-delete" data-action='delete' data-id='<?= $rs['id'] ?>' data-url="<?= Url::to(['/manage_modules/default/delete']) ?>"><i class="fa fa-trash"></i></button> 
        </div>
        <div class="draggable" > 
             <?php 
                echo CNMyModule::classNames()
                    ->setImgPath($imgPath)
                    ->setNoImage($noImage)
                    ->setCardWidth(12)
                    ->setDataModule($rs)
                    ->setLink(FALSE)
                    ->buildCard();
            ?>
        </div>
    </div>
 
    <?php \appxq\sdii\widgets\CSSRegister::begin(); ?>
    <style>

        #sortable .draggable {
            background-repeat: no-repeat;
            background-position: center;
            width: 100%;
            height: 100%; 
            display: inline-block;
            float: left;
            cursor: move;
        }
        .dads-children:hover {
            background-color: #eee;
            border-radius: 3px;
        }

    </style>
    <?php \appxq\sdii\widgets\CSSRegister::end(); ?>
<?php else: ?>
      <?php 
//                echo CNMyModule::classNames()
//                    ->setImgPath($imgPath)
//                    ->setNoImage($noImage)
//                    ->setCardWidth(12)
//                    ->setDataModule($rs)
//                    ->setLink(FALSE)
//                    ->buildCard();
      ?>
    
<?php endif; ?>


