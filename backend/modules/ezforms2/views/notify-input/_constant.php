<?php

use yii\helpers\Html;
?>


<div class="modal-header" style="background-color: #fff">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel">
        <?= Yii::t('ezform', 'Constatnt value') ?>
    </h3>    
</div>
<div class="modal-body">
    <?php
    echo Html::beginTag('div', ['class' => 'col-md-4', 'style' => 'margin-top:1%']);
    echo Html::tag('strong', 'Assign name') . " : " . Html::button("{assign}", ['class' => 'btn btn-warning btn-xs add-constant']);
    echo Html::endTag('div');
    echo Html::beginTag('div', ['class' => 'col-md-4', 'style' => 'margin-top:1%']);
    echo Html::tag('strong', 'Ezform name') . " : " . Html::button("{ezf_name}", ['class' => 'btn btn-warning btn-xs add-constant']);
    echo Html::endTag('div');
    echo Html::beginTag('div', ['class' => 'col-md-4', 'style' => 'margin-top:1%']);
    echo Html::tag('strong', 'Project name') . " : " . Html::button("{project_name}", ['class' => 'btn btn-warning btn-xs add-constant']);
    echo Html::endTag('div');
//    echo Html::beginTag('div', ['class' => 'col-md-4', 'style' => 'margin-top:1%']);
//    echo Html::tag('strong', 'Notify ID') . " : " . Html::button("{notify_id}", ['class' => 'btn btn-warning btn-xs add-constant']);
//    echo Html::endTag('div');
    echo Html::tag('div', '', ['class' => 'clearfix']);
    echo Html::tag('hr');
    if (!empty($fieldData)) {
        foreach ($fieldData as $value) {
            if ($value['ezf_field_type'] != '57') {
                echo Html::beginTag('div', ['class' => 'col-md-4', 'style' => 'margin-top:1%']);
                echo Html::tag('strong', $value['ezf_field_label']) . " : " . Html::button("{" . $value['ezf_field_name'] . "}", ['class' => 'btn btn-warning btn-xs add-constant']);
                echo Html::endTag('div');
            }
        }
    } else {
        echo Html::tag('div', Yii::t('app', 'No results found.'), ['class' => 'alert alert-danger']);
    }
    ?>
    <div class="clearfix"></div>
</div>

<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $(".add-constant").on('click', function () {
        var $txt = $('#<?= $id_input ?>');
        var caretPos = $txt[0].selectionStart;
        var textAreaTxt = $txt.val();
        var txtToAdd = $(this).text();
        if($('#<?= $id_input ?>').parents('.redactor-box').children('.redactor-editor').length){
            $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
            $('#<?= $id_input ?>').parents('.redactor-box').children('.redactor-editor').html($txt.val());
        }else{var textAreaTxt = $txt.val();
            $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
        }
        $('#<?= $modal ?>').modal('hide');
    });
</script>
<?php
\richardfan\widget\JSRegister::end();


