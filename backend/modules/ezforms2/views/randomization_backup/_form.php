<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
?>
<div class="templateForm" >
    <div class="col-md-10">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Auto Number'), 'options[data-id]', ['class' => 'control-label']) ?>
            <?=
            \kartik\select2\Select2::widget([
                'id' => 'sitecode' . appxq\sdii\utils\SDUtility::getMillisecTime(),
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
                <?= Html::label(Yii::t('ezform', 'Auto Number'), 'options[data-id]', ['class' => 'control-label']) ?>
                <div class="input-group">
                    <?=
                    \kartik\select2\Select2::widget([
                        'id' => 'sitecode' . appxq\sdii\utils\SDUtility::getMillisecTime(),
                        'name' => 'options[options][random_code][]',
                        'data' => ArrayHelper::map($dataSitecode, 'id', 'sitecode'),
                        'value' => '',
                        'options' => [
                            'class' => 'selectCode',
                            'placeholder' => 'Select sitecode ...',
//            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 0,
                            'ajax' => [
                                'url' => '/ezforms2/randomization/select',
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                            'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                        ],
                    ])
                    ?>
                    <span class="input-group-btn">
                        <?php
                        echo Html::button('<i class="glyphicon glyphicon-pencil"></i> ', ['data-toggle' => 'tooltip', 'title' => Yii::t('app', 'Update'), 'class' => 'btn btn-primary btn-open-addon-edit', 'data-url' => Url::to(['/ezforms2/randomization/update-code', 'modal' => 'modal-gridtype']), 'style' => 'display: none;']) . ' ';
                        echo Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['data-toggle' => 'tooltip', 'title' => Yii::t('app', 'New'), 'class' => 'btn btn-success btn-open-addon-add', 'data-url' => Url::to(['/ezforms2/randomization/add-code', 'modal' => 'modal-gridtype'])]) . ' ';
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <?= Html::button('x', ['class' => 'btn btn-danger btnRemoveInput', 'style' => 'margin-top:25px;']) ?>
    </div>
    <div class="clearfix"></div>
</div>


<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $('.btnRemoveInput').on('click', function () {
        $(this).parents('.templateForm').remove();
    });
    $('.btn-open-addon-edit').click(function () {
        var btn = $(this).parents('.input-group');
        modalGridtype($(this).attr('data-url')+'&id='+btn.children('.selectCode').val());
    });

    $('.btn-open-addon-add').click(function () {
        modalGridtype($(this).attr('data-url'));
    });

    $('.selectCode').change(function () {
        if ($(this).val()) {
            var btn = $(this).parents('.input-group');
            btn.children('.input-group-btn').children('.btn-open-addon-edit').show();
        } else {
            var btn = $(this).parents('.input-group');
            btn.children('.input-group-btn').children('.btn-open-addon-edit').hide();
        }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
