<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4>Add Items</h4>
</div>
<div class="modal-body">
    <form id="add-grade">
        <?= Html::hiddenInput('id', isset($data['id']) ? $data['id'] : '', ['class' => 'form-control']) ?>
        <?= Html::hiddenInput('soc_id', isset($socId) ? $socId : '', ['class' => 'form-control']) ?>
        <?= Html::hiddenInput('ctcae_id', isset($ctcaeId) ? $ctcaeId : '', ['class' => 'form-control']) ?>
        <div class="col-md-12" style="margin-top: 10px;">
            <?= Html::label(Yii::t('chanpan', 'Grade'), 'options[title]', ['class' => 'control-label']) ?>
            <?= Html::textInput('grade', isset($data['grade']) ? $data['grade'] : '', ['class' => 'form-control', 'id' => 'grade']) ?>
        </div>
        <div class="col-md-12" style="margin-top: 10px;">
            <?= Html::label(Yii::t('chanpan', 'Grade Detail'), 'options[title]', ['class' => 'control-label']) ?>
            <?= Html::textarea('grade_detail', isset($data['grade_detail']) ? $data['grade_detail'] : '', ['class' => 'form-control', 'id' => 'grade_detail','rows'=>6]) ?>
        </div>
        <div class="clearfix"></div>
    </form>
</div>
<div class="modal-footer">
    <?= Html::button(isset($data) ? '<span class="glyphicon glyphicon-ok"></span> Update' : '<span class="glyphicon glyphicon-plus"></span> Add', ['class' => 'btn btn-success', 'id' => 'btnAddData', 'data-action' => isset($data) ? 'update' : 'add']) ?>
    <?= Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
</div>


<?php
\richardfan\widget\JSRegister::begin();
?>
<script>
    $('#btnAddData').click(function () {
        $.post('/ezforms2/ctcae/save-grade', $('#add-grade').serializeArray(), function (data) {
            if (data) {
                $('#btnAddData').parents('.modal').modal('hide');
<?= SDNoty::show("'" . Yii::t('ezform', 'Complete') . "'", '"success"') ?>
            } else {
<?= SDNoty::show("'" . Yii::t('ezform', 'Failed') . "'", '"error"') ?>
            }
        }).fail(function () {
<?= SDNoty::show('"Server Error"', '"error"') ?>
        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
