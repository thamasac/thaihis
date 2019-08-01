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

$itemWidgets = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByModule($ezm_id, $widget_id);

?>

<?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[report_send]', isset($options['report_send'])?$options['report_send']:'0', ['label' => 'Report send'])?>
<?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[checkup_config]', isset($options['checkup_config'])?$options['checkup_config']:'0', ['label' => 'Checkup config'])?>
<?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[patient_config]', isset($options['patient_config'])?$options['patient_config']:'0', ['label' => 'Patient config'])?>
<?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[report2doc]', isset($options['report2doc'])?$options['report2doc']:'0', ['label' => 'Report to doctor'])?>
<?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[report_opd]', isset($options['report_opd'])?$options['report_opd']:'0', ['label' => 'Report OPD'])?>
<?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[report_app_checkup]', isset($options['report_app_checkup'])?$options['report_app_checkup']:'0', ['label' => 'Report appiont checkup'])?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>

</script>
<?php \richardfan\widget\JSRegister::end(); ?>