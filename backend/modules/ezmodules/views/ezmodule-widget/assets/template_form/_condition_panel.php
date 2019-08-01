<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$key_index=isset($key_index)?$key_index:'';

$value_ref2 = isset($value_ref2)?$value_ref2:null;
?>
<div class="form-group row">
    <div class="  panel panel-warning">
        <div class="panel-heading">
            <div class="sdbox-col">
                <?= Html::label(Yii::t('thaihis', 'Condition')) ?>
                <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success pull-right', 'id' => 'btn-add-condition' . $key_index]) ?>
            </div>
        </div>
        <div class="panel-body">

            <div class="sdbox-col" id="display-condition<?= $key_index ?>">
                <?php
                if (isset($conditions) && is_array($conditions)):
                    foreach ($conditions as $key => $val):

                        $sub_index = $key;
                        if ($key_index == ''){
                            $set_key_index = $key;
                            $sub_index = null;
                        }
                        echo $this->renderAjax('_condition_form', [
                            'key_index' => $set_key_index,
                            'sub_index' => $sub_index,
                            'ezf_id' => $value_ref,
                            'ezf_id2' => $value_ref2,
                            'main_ezf_id' => $value_ezf_id,
                            'val' => $val,
                        ]);
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
$urlDrugAll = yii\helpers\Url::to(['/pis/pis-item/drug-allergy']);
?>
<script>
    $('#btn-add-condition<?= $key_index ?>').on('click', function () {
        var key_index = '<?= $key_index ?>';
        var sub_index ;
        if (key_index != '' || key_index ){
            sub_index = getMilisecTime();
        }else{
            key_index = getMilisecTime();
        }
        onLoadCondition<?= $key_index ?>(key_index,sub_index, 'addNew');
    });

    function onLoadCondition<?= $key_index ?>(key_index,index, act) {
        var ezf_id = $('#config_ref_form').val();
        var ezf_id2 = $('#config_left_ref_form').val();
        var main_ezf_id = $('#config_ezf_id<?= $key_index ?>').val();
        var value_ref = <?= json_encode($value_ref) ?>;
        var value_ref2 = <?= json_encode($value_ref2) ?>;
        var div_condition = $('#display-condition<?= $key_index ?>');
        if (ezf_id) {
            value_ref = ezf_id;
        }

        if (ezf_id2) {
            value_ref2 = ezf_id2;
        }

        var url = '<?= Url::to(['/thaihis/configs/add-newcondition', 'conditions' => $conditions]) ?>';
        $.get(url, {ezf_id: value_ref,ezf_id2: value_ref2, main_ezf_id: main_ezf_id, act: act, key_index: key_index, sub_index: index}, function (result) {
            div_condition.append(result);
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
