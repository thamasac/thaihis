<?php
//appxq\sdii\utils\VarDumper::dump($output);
//$output[$v['name']];
?>
<div class="flex-container">
    <div class="flex-items">
        <div class="flex-right"><?= Yii::t('report', 'Label') ?></div>
        <div class="flex-left"><?= Yii::t('report', 'Name') ?></div>

    </div>
    <?php foreach ($data as $k => $v): ?>

        <div class="flex-items">
            <div class="flex-right"><?= $v['ezf_field_label'] ?></div>
            <div class="flex-left text-right">
                <button draggable="true"  data-value="{<?= $v['ezf_field_name'] ?>}" class="btn btn-warning btn-sm  btnVar">{<?= $v['ezf_field_name'] ?>}</button>
            </div>

        </div>

    <?php endforeach; ?>

</div>
<?php \appxq\sdii\widgets\CSSRegister::begin(); ?>
<style>
    .flex-container{
        display: flex;
        flex-direction: column;
        height: 450px;
        /* overflow: hidden; */
        overflow-y: auto;
    }
    .flex-items{
        display: flex;
        margin-bottom: 6px;
        border-bottom: 1px solid #e8e8f778;
        padding-bottom: 35px;
        border-bottom-style: dashed;
    }
    .flex-left{flex-grow: 1}
    .flex-right{flex-grow: 2;}
</style>
<?php \appxq\sdii\widgets\CSSRegister::end(); ?>