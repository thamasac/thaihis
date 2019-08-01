<?php

use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$approvedData = SubjectManagementQuery::GetTableData('subject_visit_approved', ['subject_target_id' => $data_id, 'visit_name' => $visit_id], 'all');

$ezform_section = EzfQuery::getEzformOne($section_ezf_id);
$ezform_budget = EzfQuery::getEzformOne($budget_id);
$subjectList = [];

foreach ($sectionProcedure as $keyPro => $valPro) {
    if (isset($valPro['approved']) && count($valPro['approved']) >= count($valPro['all'])) {
        $subjectList[$keyPro] = $valPro['approved'];
    }
}

$this->registerCssFile("@web/css/checkbox-style.css?2");
?>
<?php
$form = EzActiveForm::begin([
            'id' => 'form-submit',
            'action' => ['/subjects/reports/print-invoice',
                'group_name' => isset($group_name) ? $group_name : '',
                'group_id' => isset($group_id) ? $group_id : '',
                'procedure_id' => isset($procedure_id) ? $procedure_id : '',
                'section_ezf_id' => isset($section_ezf_id) ? $section_ezf_id : '',
                'budget_id' => isset($budget_id) ? $budget_id : '',
                'data_id' => isset($data_id) ? $data_id : '',
                'subject_id' => isset($subject_id) ? $subject_id : '',
                'visit_name' => isset($visit_name) ? $visit_name : '',
                'visit_id' => isset($visit_id) ? $visit_id : '',
                'total_invoice'=>$total_invoice,
                'total_budget' => $total_budget,
                'total_invoice'=>$total_invoice,
                'subjectList' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($subjectList)),
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'ezf_id' => isset($ezf_id) ? $ezf_id : '',
                'widget' => isset($widget_id) ? $widget_id : '',
                'dataid' => isset($data_id) ? $data_id : '',
                'target' => '_blank',
            ]
        ]);
?>
<div class="modal-header">
    <h4>Configuration for print</h4>
</div>
<div class="modal-body">
    <div class="row" style="margin: 0 50px 0 50px;">
        <div class="col-md-6">
            <?= Html::label("Customer Name : ", 'customer_name') ?>
            <?= Html::textInput('customer_name', '', ['class' => 'form-control']) ?>
        </div>

    </div>
    <div class="row" style="margin: 0 50px 0 50px;">
        <div class="col-md-6">
            <?= Html::label("Received by : ", 'received_by') ?>
            <?= Html::textInput('received_by', '', ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-6">
            <?= Html::label("Paid by : ", 'paid_by') ?>
            <?= Html::textInput('paid_by', '', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="row" style="margin: 0 50px 0 50px;">
        <div class="col-md-12">
            <?= Html::label("Address : ", 'address') ?>
            <?= Html::textarea('address', '', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr/>
    <div class="row" style="margin: 0 50px 0 50px;">
        <h5><strong>Items For Print</strong></h5>
    </div>
    
    <?= Html::hiddenInput('sectionProcedure', \appxq\sdii\utils\SDUtility::array2String($sectionProcedure)) ?>
    <?php

    foreach ($subjectList as $key => $value):
        $section = [];
        if(isset($financial_type) && $financial_type=='additional'){
            $section['section_name'] = "Additional Payment";
        }elseif($key == 'other'){
            $section['section_name'] = 'Other';
        }else{
            $section = SubjectManagementQuery::GetTableData($ezform_section, ['id' => $key], 'one');
        }

        ?>
        <div class="row" style="margin-left: 50px">
            <div class="col-md-1 sdbox-col" style="margin-top: 0">
                <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-default btn-xs pull-right btn_show_hide', 'data-section' => $key]) ?>
            </div>
            <div class="col-md-11 sdbox-col" id="section-<?= $key ?>">
                <div class="checkbox1 checkbox1-success">
                    <?=
                    Html::checkbox('checkbox[' . $key . ']', 'true', [
                        'id' => 'checkbox-' . $key,
                        'value' => $key,
                        'class' => "check-subject-active",
                        'data-visit' => $visit_id,
                        'data-pro_name' => $section['section_name'],
                        'data-widget_id' => $procedure_id,
                    ])
                    ?>
                    <?= Html::label($section['section_name'], 'checkbox-' . $key, ['style' => 'font-weight:bold;']) ?>
                </div>
                <div class="col-md-12 procedure_childen" style="display: none;">
                    <?php foreach ($value as $key2 => $value2):
                        ?>

                        <div class="checkbox1 checkbox1-success">
                            <?=
                            Html::checkbox('checkbox[' . $key . '][' . $value2['id'] . ']', 'true', [
                                'id' => 'checkbox-' . $key . '-' . $value2['id'],
                                'value' => $value2['id'],
                                'class' => "check-procedure-active",
                                'data-visit' => $visit_id,
                                'data-pro_name' => $value2['procedure_name'],
                                'data-section' => $key,
                                'data-widget_id' => $procedure_id,
                            ])
                            ?>
                            <?= Html::label($value2['procedure_name'], 'checkbox-' . $key . '-' . $value2['id']) ?>
                        </div>

                    <?php endforeach; ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php endforeach; ?>

</div>
<div class="modal-footer">
    <?= Html::button("Close", ['class' => 'btn btn-defualt pull-right', 'data-dismiss' => 'modal']); ?>
    <?= Html::submitButton("Submit", ['class' => 'btn btn-primary pull-right', 'style' => 'margin-right:10px;']); ?>
</div>
<?php EzActiveForm::end(); ?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $('.btn_show_hide').click(function () {
        var sec_id = $(this).attr('data-section');
        var div_section = $('#section-' + sec_id);
        if ($(this).find("i").hasClass("fa-plus")) {
            $(this).find("i").removeClass("fa-plus");
            $(this).find("i").addClass('fa-minus');

            div_section.find(".procedure_childen").css("display", "");
        } else {
            $(this).find("i").addClass("fa-plus");
            $(this).find("i").removeClass('fa-minus');
            div_section.find(".procedure_childen").css("display", "none");
        }
    });

    $('.check-subject-active').click(function () {
        var childen = $(this).parent().parent().find(".procedure_childen");
        var sec_id = $(this).attr('data-section');
        var pro_id = $(this).val();
        if ($(this).is(":checked")) {
            childen.find(".check-procedure-active").each(function (i, e) {
                $(e).prop('checked', true);
            });
        } else {

            childen.find(".check-procedure-active").each(function (i, e) {
                $(e).prop('checked', false);
            });
        }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>