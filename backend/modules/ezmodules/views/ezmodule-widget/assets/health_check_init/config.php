<?php

use kartik\widgets\Select2;
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
?>

    <div class="modal-header" style="margin-bottom: 15px;">
        <h4 class="modal-title"><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
    </div>
<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Javascript Callback(WINDOW Function Name)'), 'options[callback]', ['class' => 'control-label']) ?>
    <?= Html::textarea('options[callback]', isset($options['callback'])?$options['callback']:'', ['class' => 'form-control', 'row'=>3])?>
</div>
<?php

?>
    <br>
    <div id="ref_field_box">
    </div>
