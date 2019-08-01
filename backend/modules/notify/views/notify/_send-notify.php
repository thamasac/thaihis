<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzformWidget;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use appxq\sdii\helpers\SDNoty;

//$items_role = (new yii\db\Query())->select(['role_name', 'CONCAT(role_detail,\' (\',role_name,\')\') as role_detail'])->from('zdata_role')->all();
//$items_user = common\modules\user\models\Profile::find()->select(['user_id', 'CONCAT(firstname,\' \',lastname) as name'])->where('sitecode = :sitecode', [':sitecode' => Yii::$app->user->identity->profile->sitecode])->all();

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= Yii::t('ezforms', 'Send Notify') ?></h4>
</div>
<div class="modal-body">

    <form id="send-notify-form" name="send-notify-form" method="post" enctype="multipart/form-data">
        <div class="form-group row notify">
            <!--            <div class="modal-header divHeadConfigMessage" style="background-color:#b9b5b8 ">-->
            <!--            <h4 class="modal-title">--><?php //echo Yii::t('ezforms', 'Send Notify') ?><!--</h4>-->
            <!--            </div>-->

            <div class="divConfigMessage">
                <div class="col-md-12">
                    <div class="col-md-6" style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Title'), '', ['class' => 'control-label']) ?>

                        <?= Html::textInput("notify", '', ['class' => 'form-control', 'id' => 'notify_savedarft']) ?>

                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12" style="margin-top:10px;">
                        <?= Html::label(Yii::t('ezform', 'Detail'), 'options[title]', ['class' => 'control-label']) ?>

                        <?=
                        \vova07\imperavi\Widget::widget([
                            'id' => 'topic',
                            'name' => 'detail',
                            'value' => '',
                            'class' => 'textarea',
                            'settings' => [
                                'minHeight' => 30,
                                'imageManagerJson' => '../../ezforms2/text-editor/images-get',
                                'fileManagerJson' => '../../ezforms2/text-editor/files-get',
                                'imageUpload' => '../../ezforms2/text-editor/image-upload',
                                'fileUpload' => '../../ezforms2/text-editor/file-upload',
                                'plugins' => [
                                    'fontcolor',
                                    'fontfamily',
                                    'fontsize',
                                    'textdirection',
                                    'textexpander',
                                    'counter',
                                    'table',
                                    'definedlinks',
                                    'video',
                                    'imagemanager',
                                    'filemanager',
                                    'limiter',
                                    'fullscreen',
                                ],
                                'paragraphize' => false,
                                'replaceDivs' => false,
                            ],
                        ])
                        ?>

                        <?php // editor('options[options][topic]', isset($options['options']['topic']) ? $options['options']['topic'] : '', ['class' => 'form-control', 'rows' => 7])  ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="form-group row notify">
            <!--<hr/>-->
            <!--        <div class="modal-header divHeadConfigAssign" style="background-color:#b9b5b8; margin-bottom:10px;">-->
            <!--            <h4 class="modal-title">--><? //= Yii::t('ezforms', 'Config Assign') ?><!--</h4>-->
            <!--        </div>-->
            <div class="divConfigAssign col-md-12">
                <div class="col-md-6" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Assign Name'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'name-fix',
                        'name' => 'name_fix',
                        'data' => ArrayHelper::map($items_user, 'user_id', 'name'),
                        'value' => NULL,
                        'options' => [
                            'placeholder' => 'Select user ...',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-6 sdbox-col" style="margin-top:10px;">
                    <?= Html::label(Yii::t('ezform', 'Assign Role'), 'options[title]', ['class' => 'control-label']) ?>
                    <?=
                    Select2::widget([
                        'id' => 'role-fix',
                        'name' => 'role_fix',
                        'data' => ArrayHelper::map($items_role, 'role_name', 'role_detail'),
                        'value' => NULL,
                        'options' => [
                            'placeholder' => 'Select role ...',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>


                <div class="col-md-12 notify" style="margin-top:10px;">
                    <?= EzformWidget::checkbox("send_system", true, ['label' => Yii::t('ezforms', 'nCRC Notification Center'), 'id' => 'send-system']) ?>
                    <!--<code>** <?php // echo Yii::t('notify', 'Line Condition')                                     ?> **</code>-->
                </div>
                <div class="col-md-12 notify" style="margin-top:10px;">
                    <?= EzformWidget::checkbox("send_email", NULL, ['label' => Yii::t('ezforms', 'E-Mail (Free)'), 'id' => 'send_mail']) ?>
                    <?php // echo Html::textInput("options[options][val_email]", isset($options['options']['val_email']) ? $options['options']['val_email'] : '', ['class' => 'form-control', 'placeholder' => 'Email to..', 'style' => 'display:none', 'id' => 'input-email'])   ?>
                </div>
                <div class="col-md-12 notify" style="margin-top:10px;">
                    <?= EzformWidget::checkbox("send_line", NULL, ['label' => Yii::t('ezforms', 'Line (Free)'), 'id' => 'send-line']) ?>
                    <!--<code>** <?php // echo Yii::t('notify', 'Line Condition')                                     ?> **</code>-->
                </div>

                <div class="clearfix"></div>

                <div class="col-md-4 send-type" style="margin-top:10px;">
                    <label>Type</label>
                    <?=
                    Html::dropDownList('type_url', '3', ['3' => 'None', '1' => 'Redirect'], ['class' => 'form-control', 'id' => 'send-type']);
                    ?>
                </div>
                <div class="col-md-8" style="margin-top:10px;">
                    <div class="send-url">
                        <label>Url</label>
                        <?= Html::textarea('url', '', ['class' => 'form-control', 'id' => 'send-url']) ?>
                    </div>
                </div>

            </div>

        </div>

    </form>
    <!--    <div class="col-md-12">-->
    <!--        <button class="btn btn-success pull-right" type="submit" id="send-notify"><i-->
    <!--                    class="glyphicon glyphicon-send"></i> Send-->
    <!--        </button>-->
    <!--    </div>-->

</div>
<div class="modal-footer">
    <button class="btn btn-success pull-right" type="submit" id="send-notify"><i
                class="glyphicon glyphicon-send"></i> Send
    </button>
    <button style="margin-right: 5px;" type="button" class="btn btn-default"
            data-dismiss="modal"><?= Yii::t('app', 'Cancel') ?></button>
</div>
<div class="clearfix"></div>
<?php
\richardfan\widget\JSRegister::begin();
?>
<script>
    $('#send-notify').on('click', function () {

        $.ajax({
            url: '/notify/default/send-notify',
            type: "POST",
            data : $('#send-notify-form').serialize(),
            // processData: false, // tell jQuery not to process the data
            // contentType: false, // tell jQuery not to set contentType
            dataType:'JSON',
            success: function(data){
                if (data.status == '1') {
                    <?=SDNoty::show('"Send notify success"', '"success"')?>
                    $('#<?=$modal?>').modal('hide');
                } else {
                    <?=SDNoty::show('"Send notify error"', '"error"')?>
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
        return false;
    });

    if ($('#send-type').val() == '1') {
        $('.send-url').show('slow');
    } else {
        $('.send-url').hide('slow');
    }

    $('#send-type').change(function () {
        if ($(this).val() == '1') {
            $('.send-url').show('slow');
        } else {
            $('.send-url').hide('slow');
        }
    });

</script>
<?php
\richardfan\widget\JSRegister::end();
?>
