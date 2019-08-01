<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

$items_role = (new yii\db\Query())->select(['role_name', 'CONCAT(role_detail,\' (\',role_name,\')\') as role_detail'])->from('zdata_role')->all();
$items_user = common\modules\user\models\Profile::find()->select(['user_id', 'CONCAT(firstname,\' \',lastname) as name'])->where('sitecode = :sitecode', [':sitecode' => Yii::$app->user->identity->profile->sitecode])->all();
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
$items_field_name = [];
$items_field_name = (new yii\db\Query())->select(['ezf_field_name AS `id`', 'concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`name`', 'ezf_version', 'ezf_field_name', 'ezf_field_label'])->from('ezform_fields')->where(['ezf_id' => $ezf_id, 'ezf_field_type' => '906'])->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])->all();
if ($ezf_id) {
    $items_field_name = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($items_field_name, $ezf_version);
}
$items_field_role = [];
$items_field_role = (new yii\db\Query())->select(['ezf_field_name AS `id`', 'concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`name`', 'ezf_version', 'ezf_field_name', 'ezf_field_label'])->from('ezform_fields')->where(['ezf_id' => $ezf_id, 'ezf_field_type' => '907'])->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])->all();
if ($ezf_id) {
    $items_field_role = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($items_field_role, $ezf_version);
}
?>

<div class="mainConfig row">
    <?php echo Html::button('<span aria-hidden="true">&times;</span>', ['class' => 'pull-right close btn-remove-basic', 'style' => 'margin-top:5px;']) ?>
    <div class="clearfix" style="margin-bottom:10px;"></div>
    <div class="col-md-6" style="margin-bottom:10px;">
        <?php
        echo Html::label(Yii::t('ezform', 'Fix assign names'));
        echo Select2::widget([
            'id' => 'fix_ass_names' . $id,
            'name' => 'options[options][config_basic][fix_ass_names][]',
            'data' => ArrayHelper::map($items_user, 'user_id', 'name'),
            'value' => NULL,
            'options' => [
                'placeholder' => 'Select user ...',
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6" style="margin-bottom:10px;">
        <?php
        echo Html::label(Yii::t('ezform', 'Fix assign roles'));
        echo Select2::widget([
            'id' => 'fix_ass_role' . $id,
            'name' => 'options[options][config_basic][fix_ass_role][]',
            'data' => ArrayHelper::map($items_role, 'role_name', 'role_detail'),
            'value' => NULL,
            'options' => [
                'placeholder' => 'Select user ...',
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6" style="margin-bottom:10px;">
        <?php
        echo Html::label(Yii::t('ezform', 'Field assign name'));
        echo Select2::widget([
            'id' => 'field_ass_names' . $id,
            'name' => 'options[options][config_basic][field_ass_names][]',
            'data' => ArrayHelper::map($items_field_name, 'id', 'name'),
            'value' => NULL,
            'options' => [
                'placeholder' => 'Select field ...',
//                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6" style="margin-bottom:10px;">
        <?php
        echo Html::label(Yii::t('ezform', 'Field assign role'));
        echo Select2::widget([
            'id' => 'field_ass_role' . $id,
            'name' => 'options[options][config_basic][field_ass_role][]',
            'data' => ArrayHelper::map($items_field_role, 'id', 'name'),
            'value' => NULL,
            'options' => [
                'placeholder' => 'Select field ...',
//                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-3" style="margin-bottom:10px;">
        <?= Html::label(Yii::t('ezform', 'Action')) ?>
        <?= Html::dropDownList('options[options][config_basic][actin][]', '', ['1' => 'Submit', '2' => 'Save Darft', '3' => 'Delete'], ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-3" style="margin-bottom:10px;">
        <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[options][config_basic][send_system][]', true, ['label' => 'nCRC Notification Center']) ?>
        <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[options][config_basic][send_email][]', '', ['label' => 'E-Mail (Free)']) ?>
        <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[options][config_basic][send_line][]', '', ['label' => 'Line (Free)']) ?>
    </div>
    <div class="col-md-12" style="margin-bottom:10px;">
        <?= Html::label(Yii::t('ezform', 'Message')) ?>
        <?= Html::textInput('options[options][config_basic][message][]', '', ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-12" style="margin-bottom:10px;">
        <?= Html::label(Yii::t('ezform', 'Detail')) ?>
        <?=
        \vova07\imperavi\Widget::widget([
            'id' => 'detail' . $id,
            'name' => 'options[options][config_basic][detail][]',
            'value' => '',
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
    </div>

    <div class="divMainFormat">
        <div class="col-md-3" style="margin-bottom:10px;">
            <?= Html::label(Yii::t('ezform', 'Format'), 'options[title]', ['class' => 'control-label']) ?>
            <?=
            Html::dropDownList("options[options][config_basic][format][]", isset($options['options']['config_basic']['format']) ? $options['options']['config_basic']['format'] : NULL, ['1' => 'Redirect', '2' => 'Open Form'], [
                'class' => 'form-control',
                'id' => 'input-format'.$id
            ])
            ?>
        </div>
        <div class="col-md-6" style="margin-bottom:10px;">
            <div class="divReadonly<?=$id?>" style="display: none">
                <?= Html::label(Yii::t('ezform', 'Url'), 'options[title]', ['class' => 'control-label']) ?>
                <?=
                Html::textarea("options[options][config_basic][url][]", isset($options['options']['config_basic']['url']) ? $options['options']['config_basic']['url'] : NULL, [
                    'class' => 'form-control',
                    'id'=>'input-format'.$id
                ])
                ?>
            </div>
            <div class="divUrl<?=$id?>" style="display: none">
                <?= backend\modules\ezforms2\classes\EzformWidget::checkbox("options[options][config_basic][readonly][]", isset($options['options']['config_basic']['readonly']) ? $options['options']['config_basic']['readonly'] : NULL, ['label' => 'Readonly Mode']) ?>
            </div>
        </div>
        <div class="col-md-3" style="margin-bottom:10px;">
            
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $('.btn-remove-basic').click(function () {
        $(this).parents('.mainConfig').remove();
    });
    
    $('#input-format<?=$id?>').on('change', function () {
        if ($(this).val() == '1') {
            $('.divUrl<?=$id?>').show('slow');
            $('.divReadonly<?=$id?>').hide('slow');
        } else {
            $('.divUrl<?=$id?>').hide('slow');
            $('.divReadonly<?=$id?>').show('slow');
            $('#input-url<?=$id?>').val('');
        }
    });

    if ($('#input-format<?=$id?>').val() == '1') {
        $('.divUrl<?=$id?>').show();
        $('.divReadonly<?=$id?>').hide();
    } else {
        $('.divUrl<?=$id?>').hide();
        $('.divReadonly<?=$id?>').show();
        $('#input-url<?=$id?>').val('');
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
