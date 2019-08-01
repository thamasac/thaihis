<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

$user_id = \Yii::$app->user->id;
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->
<h4>Proposal Form</h4>
<div class="form-group row">

    <div class="col-md-4 ">
        <?php
        $attrname_ezf_id = 'options[proposal_ezf_id]';
        $value_ezf_id = isset($options['proposal_ezf_id']) ? $options['proposal_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_proposal_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_display_fields = 'options[display_fields]';
        $display_fields = isset($options['display_fields']) ? $options['display_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Display Fields'), $attrname_display_fields, ['class' => 'control-label']) ?>
        <div id="display_field_box">

        </div>
    </div>
    <div class="clearfix"></div>

</div>

<!--config end-->

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    fieldDisplayField($('#config_proposal_ezf_id').val());

    $('#config_proposal_ezf_id').on('change', function () {
        var ezf_id = $(this).val();
        fieldDisplayField(ezf_id);
    });

    function fieldDisplayField(ezf_id) {
        var value = <?= json_encode($display_fields) ?>;
        $.post('<?= Url::to(['/ezforms2/target/get-fields']) ?>', {ezf_id: ezf_id, multiple: 1, name: '<?= $attrname_display_fields ?>', value: value, id: 'display_field_proposal'}
        ).done(function (result) {
            $('#display_field_box').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>