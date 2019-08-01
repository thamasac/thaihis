<?php

use kartik\tabs\TabsX;
?>

<div id="print-<?= $modelEzf->ezf_id ?>" style="background-color: #fff">
    <div class="modal-header" style="background-color: #fff">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="itemModalLabel"><?= Yii::t('drug_input', 'Add Drug Home')?></h3>
    </div>
    <div class="modal-body">
        <ul class="nav nav-tabs">
            <?php
            if ($drug_ipd == 1) {
                $active_ipd = 'active';
                ?>
                <li class="<?= $active_ipd ?>"><a data-toggle="tab" href="#drug_ipd" id="drug_ipd"><?=Yii::t('drug_input', 'Drug IPD')?></a></li>
                <?php
            }
            if ($drug_ipd != 1 && $drug_opd == 1) {
                $active_opd = 'active';
            }
            if ($drug_opd == 1) {
                ?>
                <li class="<?= $active_opd ?>"><a data-toggle="tab" href="#drug_opd" id="drug_opd"><?=Yii::t('drug_input', 'Drug OPD')?></a></li>
                <?php
            }
            ?>

        </ul>
        <?php if ($drug_ipd !=0 || $drug_opd != 0) { ?>
        <div class="tab-content" style="margin-top: 10px;">
            <div  class="tab-pane fade in active">
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading"><?=Yii::t('drug_input', 'Date')?> <span id="drugName"></span></div>
                        <div class="panel-body" id="dateDrug" style='overflow:auto; height:300px;'>

                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="col-md-6"><?=Yii::t('drug_input', 'Name')?></div>
                            <div class="col-md-3"><?=Yii::t('drug_input', 'Amount')?></div>
                            <div class="col-md-3"><?=Yii::t('drug_input', 'Unit Packing')?></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div id="dataDrug" style='overflow:auto; height:300px;'>

                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>
        <?php } ?>
    </div>
</div>

</div> 
<div class="modal-footer" >
    <div class="pull-right">
        <button type="button" style="display:none" data-dismiss="modal" aria-hidden="true" class="btn btn-success btnAddDrug">
            <i class="glyphicon glyphicon-plus"> </i> <?=Yii::t('drug_input', 'Add')?>
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">
            <i class="glyphicon glyphicon-remove"></i> <?=Yii::t('app', 'Close')?>
        </button>
    </div>
    <div class="clearfix"></div>
</div>
</div>
</div>
<?php \richardfan\widget\JSRegister::begin(['position' => yii\web\View::POS_READY]); ?>
<script>

    var dataDrug = '';
<?php
if ($drug_ipd == 1) {
    echo "getDate('ipd');";
}
if ($drug_ipd != 1 && $drug_opd == 1) {
    echo "getDate('opd');";
}
?>
    function getDate(type = '') {
        $('#dateDrug').html("<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>");
        $.ajax({
            url: '<?= yii\helpers\Url::to(['/ezforms2/drug-input/date']) ?>',
            type: "get",
            data: {type: type},
            success: function (response) {
                $('#dateDrug').html(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {

                $('#dateDrug').html("<code><?= Yii::t('app', 'Can not load data.')?> !</code");
            }


        });
    }

    function getData(date = '', type = '') {
        dataDrug = '';
        $('#dataDrug').html("<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>");
        $.ajax({
            url: '<?= yii\helpers\Url::to(['/ezforms2/drug-input/add-drug']) ?>',
            type: "get",
            data: {date: date, type: type},
            dataType: 'json',
            success: function (response) {
                var html = '';
                for (var res of response) {
                    html += "<div class='col-md-6'>" + res['DNAME'] + "</div>";
                    html += "<div class='col-md-3'>" + res['AMOUNT'] + "</div>";
                    html += "<div class='col-md-3'>" + res['UNIT_PACKING'] + "</div>";
                    dataDrug += res['DNAME'] + "                                " + res['AMOUNT'] + "   " + res['UNIT_PACKING'] + "\n"
                }

                $('#dataDrug').html(html);
                $('.btnAddDrug').show();
            },
            error: function (jqXHR, textStatus, errorThrown) {


                $('#dataDrug').html("<code><?= Yii::t('app', 'Can not load data.')?> !</code>");
            }


        });
    }

    $('.btnAddDrug').on('click', function () {
        $('#<?= $id ?>').val($('#<?= $id ?>').val() + dataDrug);
    });
    $('#drug_ipd').on('click', function () {
        dataDrug = '';
        $('.btnAddDrug').hide();
        $('#dataDrug').html("");
        getDate('ipd');
    });
    $('#drug_opd').on('click', function () {
        dataDrug = '';
        $('.btnAddDrug').hide();
        $('#dataDrug').html("");
        getDate('opd');
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>




