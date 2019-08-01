<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
?>
<div class="">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <form id="lineEdit">
            <div class="col-md-12 ">
                <?= Html::hiddenInput('id', isset($data['id']) ? $data['id'] : '') ?>
                <div class="col-md-7">
                    <?= Html::label('Line Name', '', ['class' => 'pull-left']); ?>
                    <?= Html::textInput('line_name', isset($data['line_name']) ? $data['line_name'] : '', ['class' => 'form-control','disabled' => isset($readonly) && $readonly == true?$readonly:false]) ?>
                </div>
            </div>
            <div class="col-md-12 " style="margin-top: 20px;">
                <div class="col-md-6">
                    <?= Html::label('Line Channel Secret', '', ['class' => 'pull-left']); ?>
                    <?= Html::textInput('line_secret', isset($data['line_secret']) ? $data['line_secret'] : '', ['class' => 'form-control','disabled' => isset($readonly) && $readonly == true?$readonly:false]) ?>
                </div>
                <div class="col-md-6">
                    <?= Html::label('Channel ID', '', ['class' => 'pull-left']); ?>
                    <?= Html::textInput('chanel_id', isset($data['chanel_id']) ? $data['chanel_id'] : '', ['class' => 'form-control','disabled' => isset($readonly) && $readonly == true?$readonly:false]) ?>
                </div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <div class="col-md-12">
                    <?= Html::label('Line Token', '', ['class' => 'pull-left']); ?>
                    <?= Html::textarea('line_token', isset($data['line_token']) ? $data['line_token'] : '', ['class' => 'form-control','disabled' => isset($readonly) && $readonly == true?$readonly:false]) ?>
                </div>

            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <div class="col-md-12 ">
                    <?= Html::label('Line QR Code (URL)', '', ['class' => 'pull-left']); ?>
                    <?= Html::textarea('line_qrcode', isset($data['line_qrcode']) ? $data['line_qrcode'] : '', ['class' => 'form-control','disabled' => isset($readonly) && $readonly == true?$readonly:false]) ?>
                </div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <div class="col-md-3">
                    <?= Html::label('Line Status', '', ['class' => 'pull-left']); ?>
                    <?= Html::dropDownList('line_status', isset($data['line_status']) ? $data['line_status'] : '', ['0' => Yii::t('line', 'Not Active'), '1' => Yii::t('line', 'Active')], ['class' => 'form-control','disabled' => isset($readonly)?$readonly:false]) ?>
                </div>

            </div>
            <div class="clearfix"></div>
        </form>
    </div>
    <div class="modal-footer">
        
        <?php echo isset($readonly) && $readonly == true ? '' : '<button class="btn btn-primary" id="btnLineCreate">'. Yii::t('app', 'Create').'</button>'?>
        <button class="btn btn-default" id="btnLineCencel"><?= isset($readonly) && $readonly == true ?  Yii::t('app', 'Close') : Yii::t('app', 'Cancel') ?></button>
    </div>
</div>


<script>

    $('#btnLineCreate').on('click', function () {
        $.post('/line/default/add?reloadDiv<?=$reloadDiv?>&modal=<?=$modal_line?>', $('#lineEdit').serializeArray(), function (data) {
            if (data) {
                $('#<?=$modal_line?>')
                        .find('.modal-content')
                        .load('/line/default/edit?reloadDiv<?=$reloadDiv?>&modal=<?=$modal_line?>');
<?= SDNoty::show("'" . Yii::t('ezform', 'Complete') . "'", '"success"') ?>;

            } else {
<?= SDNoty::show("'" . Yii::t('ezform', 'Error') . "'", '"error"') ?>;
            }
        });
        return false;
    });

    $('#btnLineCencel').on('click', function () {
        $('#<?=$modal_line?>')
                .find('.modal-content')
                .load('/line/default/edit?reloadDiv<?=$reloadDiv?>&modal=<?=$modal_line?>');
        return false;
    });

</script>
