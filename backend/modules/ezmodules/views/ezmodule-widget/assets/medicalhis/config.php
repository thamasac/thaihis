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
if (!isset($ezm_id) || $ezm_id == '')
    $ezm_id = $model['ezm_id'];

$widget_id = $model['widget_id'];
$itemWidgets = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByModule($ezm_id);

$contents = isset($options['contents']) ? $options['contents'] : [];
$value_ref = isset($options['refform']) && is_array($options['refform']) ? $options['refform'] : null;
$value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : null;
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->
<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Title'), 'action_title') ?>
        <?= Html::textInput('options[action_title]', isset($options['action_title']) ? $options['action_title'] : null, ['class' => 'form-control', 'id' => 'title_input']) ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Widget'), 'options[widget_id]') ?>
        <?php
        $attrname_widget_id = 'options[widget_id]';
        $value_widget_id = isset($options['widget_id']) ? $options['widget_id'] : '';
        echo kartik\select2\Select2::widget([
            'name' => $attrname_widget_id,
            'value' => $value_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widgets'), 'id' => 'config_widget_id'],
            'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>
<?php 
    echo $this->renderAjax('../template_form/config',[
        'itemsEzform'=>$itemsEzform,
        'options'=>$options,
    ]);
?>
<div class="form-group row">
    <div class="panel panel-warning">
        <div class="panel-heading">
            <div class="sdbox-col">
                <?= Html::label(Yii::t('thaihis', 'Content of medical')) ?>
                <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success pull-right', 'id' => 'btn-add-content']) ?>
            </div>
        </div>
        <div class="panel-body">

            <div class="sdbox-col" id="display-contents">
                <?php
                if (isset($contents) && is_array($contents)):
                    foreach ($contents as $key => $val):
                        $key_index = $key;

                        echo $this->renderAjax('_form', [
                            'key_index' => $key_index,
                            'value_ref' => $value_ref,
                            'value_ezf_id' => $value_ezf_id,
                            'val' => $val,
                        ]);
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</div>
<!--config end-->

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script

    $('#btn-add-content').on('click', function () {
        var key_index = getMilisecTime();
        onLoadContent(key_index, 'addNew');
    });

    function onLoadContent(index, act) {
        var ezf_id = $('#config_ref_form').val();
        var main_ezf_id = $('#config_ezf_id').val();
        var value_ref = <?= json_encode($value_ref) ?>;
        var div_content = $('#display-contents');

        var url = '<?= Url::to(['/thaihis/configs/add-newcontent-medical', 'contents' => $contents]) ?>';
        $.get(url, {act: act, key_index: index, value_ref: value_ref}, function (result) {
            div_content.append(result);
        });
    }

    function getMilisecTime() {
        var d = new Date();
        var key_index = d.getFullYear() + '' + d.getMonth() + '' + d.getDate() + '' + d.getHours() + '' + d.getMinutes() + '' + d.getSeconds() + '' + d.getMilliseconds();
        return key_index;
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>