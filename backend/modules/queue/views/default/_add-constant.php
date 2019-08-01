<?php
use yii\helpers\Html;


$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-title"><?= Yii::t('queue', 'Add Constant') ?></div>
</div>
<div class="modal-body">
    <p>
        <strong><?= Yii::t('queue', 'Deparment') ?></strong> :
        <button class="btn btn-warning btn-xs btn-constant-val" data-input="<?= $input ?>"
                data-val="{department}"><?= Yii::t('queue', '{department}') ?></button>
    </p>
    <!--    <p>-->
    <!--        <strong>-->
    <?php //echo Yii::t('queue','Permission')?><!--</strong> : <button class="btn btn-warning btn-xs btn-constant-val" data-input="-->
    <?php //echo $input?><!--" data-val="{permission}">--><?php //echo Yii::t('queue','{permission}')?><!--</button>-->
    <!--    </p>-->
    <p>
        <strong><?= Yii::t('queue', 'User ID') ?></strong> :
        <button class="btn btn-warning btn-xs btn-constant-val" data-input="<?= $input ?>"
                data-val="{sitecode}"><?= Yii::t('queue', '{sitecode}') ?></button>
    </p>
    <p>
        <strong><?= Yii::t('queue', 'User ID') ?></strong> :
        <button class="btn btn-warning btn-xs btn-constant-val" data-input="<?= $input ?>"
                data-val="{user_id}"><?= Yii::t('queue', '{user_id}') ?></button>
    </p>
    <p>
        <strong><?= Yii::t('queue', 'Today') ?></strong> :
        <button class="btn btn-warning btn-xs btn-constant-val" data-input="<?= $input ?>"
                data-val="{today}"><?= Yii::t('queue', '{today}') ?></button>
    </p>
    <p>
        <strong><?= Yii::t('queue', 'Param form url') ?></strong> :
    <div class="input-group">
        <?= Html::textInput('val-form-param', '', ['class' => 'form-control', 'id' => 'val-form-param-' . $id]) ?>
        <span class="input-group-btn">
          <button class="btn btn-success btn-constant-val" id="btn-constant-val<?=$id?>" data-input="<?=$input?>" data-val=""><?=Yii::t('queue','Add')?></button>
        </span>
    </div>
    </p>
    <div class="clearfix"></div>
</div>
<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
</div>

<?php \richardfan\widget\JSRegister::begin();?>
<script>
    $('#val-form-param-<?=$id?>').keyup(function(){
        let val_param = '{'+$(this).val()+'}';
        $('#btn-constant-val<?=$id?>').attr('data-val',val_param);
    });
</script>
<?php \richardfan\widget\JSRegister::end();?>

