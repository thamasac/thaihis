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
if(!isset($ezm_id) || $ezm_id == '')$ezm_id = $model['ezm_id'];

$contents = isset($options['contents']) ? $options['contents'] : [];

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->

<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('thaihis', 'Column of content')) ?>
        <?= Html::input('number', 'options[column]', isset($options['column'])?$options['column']:null, ['class'=>'form-control','max'=>4, 'min'=>1])?>
    </div>
    <div class="col-md-6">
        <?= Html::checkbox('options[readonly]', isset($options['readonly'])?$options['readonly']:null, [])?>
        <?= Html::label(Yii::t('thaihis', 'Read only')) ?>
    </div>
</div>
<div class="form-group row">
    <div class="panel panel-warning">
        <div class="panel-heading">
            <div class="sdbox-col">
                <?= Html::label(Yii::t('thaihis', 'Widget')) ?>
                <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-content']) ?>
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
                            'val' => $val,
                            'ezm_id'=>$ezm_id,
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

    $(function () {
        var contents = <?= json_encode($contents) ?>;
        var firstIndex;
        var url = '<?= Url::to(['/thaihis/configs/add-newcontainer-widget', 'ezm_id' => $ezm_id, 'contents' => $contents]) ?>';
        var div_content = $('#display-contents');
        
    });

    $('#btn-add-content').on('click', function () {
        var key_index = getMilisecTime();
        onLoadContent(key_index, 'addNew');
    });

    function onLoadContent(index, act) {
        var div_content = $('#display-contents');

        var url = '<?= Url::to(['/thaihis/configs/add-newcontainer-widget', 'ezm_id' => $ezm_id, 'contents' => $contents]) ?>';
        $.get(url, {act: act, key_index: index}, function (result) {
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