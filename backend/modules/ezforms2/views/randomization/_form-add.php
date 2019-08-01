<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
//\appxq\sdii\utils\VarDumper::dump($ezf_id);
?>
<div class="templateForm divParent" id="templateForm-<?= $id ?>">
    <div class="col-md-11">
        <div class="col-md-6 ">
            <?= Html::label(Yii::t('ezform', 'Sitecode'), 'options[data-id]', ['class' => 'control-label']) ?>
            <?=
            \kartik\select2\Select2::widget([
                'id' => 'sitecode-' . $id,
                'name' => 'options[options][sitecode][]',
                'data' => ArrayHelper::map($dataSitecode, 'id', 'sitecode'),
                'value' => '',
                'options' => [
                    'class' => 'selectSiteCode',
                    'placeholder' => 'Select sitecode ...',
//            'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])
            ?>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?= Html::label(Yii::t('ezform', 'Code Name'), 'options[data-id]', ['class' => 'control-label']) ?>
                <div class="input-group">
                    <?=
                    \kartik\select2\Select2::widget([
                        'id' => 'random-' . $id,
                        'name' => 'options[options][random_code][]',
//                        'data' => ArrayHelper::map($dataSitecode, 'id', 'sitecode'),
                        'value' => '',
                        'options' => [
                            'class' => 'selectCode',
                            'placeholder' => 'Select code ...',
//            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 0,
                            'ajax' => [
                                'url' => '/ezforms2/randomization/select',
                                'dataType' => 'json',
                                'type' => 'POST',
                                'data' => new JsExpression('function(params) { return {q:params.term,data_from:$(\'#EzformFields\').serializeArray(),ezf_id:"' . $ezf_id . '"}; }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                            'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                        ],
                    ])
                    ?>
                    <span class="input-group-btn">
                        <?php
                        echo Html::button('<i class="glyphicon glyphicon-pencil"></i> ', ['id' => 'btn-edit-' . $id, 'data-toggle' => 'tooltip', 'title' => Yii::t('app', 'Update'), 'class' => 'btn btn-primary btn-open-addon-edit', 'data-url' => Url::to(['/ezforms2/randomization/update-code', 'modal' => 'modal-gridtype', 'ezf_id' => $ezf_id]), 'style' => 'display: none;']) . ' ';
                        echo Html::button('<i class="glyphicon glyphicon-trash"></i> ', ['id' => 'btn-delete-' . $id,'data-toggle' => 'tooltip', 'title' => Yii::t('app', 'Delete'), 'class' => 'btn btn-danger btn-delete-code', 'data-url' => Url::to(['/ezforms2/randomization/delete', 'modal' => 'modal-gridtype', 'ezf_id' => $ezf_id]),'style' => 'display: none;']) . ' ';
                        echo Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['id' => 'btn-add-' . $id, 'data-toggle' => 'tooltip', 'title' => Yii::t('app', 'New'), 'class' => 'btn btn-success btn-open-addon-add', 'data-url' => Url::to(['/ezforms2/randomization/add-code', 'modal' => 'modal-gridtype', 'ezf_id' => $ezf_id])]) . ' ';
                       ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1">
        <?= Html::button('&times;', ['id' => 'btn-remove-' . $id, 'class' => 'close btnRemoveInput', 'style' => 'margin-top:25px;']) ?>
    </div>
    <div class="clearfix"></div>
</div>


<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $('#btn-remove-<?=$id?>').on('click', function () {
        $(this).parents('.templateForm').remove();
    });
    $('#btn-edit-<?=$id?>').on('click', editCode);

    $('#btn-add-<?=$id?>').on('click', addCode);

    $('#btn-delete-<?=$id?>').on('click', deleteCode);

    $('#random-<?=$id?>').change(function () {
        if ($(this).val()) {
            var btn = $(this).parents('.input-group');
            btn.children('.input-group-btn').children('.btn-open-addon-edit').show();
            btn.children('.input-group-btn').children('.btn-delete-code').show();
        } else {
            var btn = $(this).parents('.input-group');
            btn.children('.input-group-btn').children('.btn-open-addon-edit').hide();
            btn.children('.input-group-btn').children('.btn-delete-code').hide();
        }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
