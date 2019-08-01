<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>

<div id="print-<?= $reloadDiv ?>" style="background-color: #fff">
    <div class="modal-header" style="background-color: #fff">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="itemModalLabel">

        </h3>    
    </div>
    <div class="modal-body">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Select Category'), ['class' => 'control-label']) ?>
            <?=
            kartik\select2\Select2::widget([
                'id' => "$reloadDiv-select2-category",
                'name' => "$reloadDiv-select2-category",
                'data' => $dataCat,
                'options' => ['placeholder' => 'Select a Category ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Select Sub-category'), ['class' => 'control-label']) ?>
            <?=
            \kartik\depdrop\DepDrop::widget([
                'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                'name' => 'province',
                'options' => ['id' => "$reloadDiv-select2-sub"],
                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                'pluginOptions' => [
                    'depends' => ["$reloadDiv-select2-category"],
                    'placeholder' => 'Select Sub Category ...',
                    'url' => '/ce/default/get-subcat',
                    'allowClear' => true,
                ]
            ]);
            ?>
        </div>
        <div class="clearfix">
        </div>
        <hr/>
        <div id="div-get-ezform"
    </div>
</div>

<?php
$url = Url::to(['/ezforms2/ezform-data/ezform',
            'ezf_id' => $ezf_id_event,
            'modal' => $modal,
            'reloadDiv' => $reloadDiv,]);

\richardfan\widget\JSRegister::begin();
?>
<script>
    $("#<?= $reloadDiv ?>-select2-sub").on('change', function () {
        if ($(this).val() != '') {
            $.ajax({
            method: 'POST',
            url: '<?=$url?>&target='+$(this).val(),
            dataType: 'HTML',
            success: function (result, textStatus) {
                $('#div-get-ezform').html(result);
                $('#<?=$modal?> .modal-header').find('button .close').remove();
            }
        });
        }
    });


    function getUiAjax(url, divid) {
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'HTML',
            success: function (result, textStatus) {
                $('#' + divid).html(result);
            }
        });
    }
</script>

<?php \richardfan\widget\JSRegister::end(); ?>

