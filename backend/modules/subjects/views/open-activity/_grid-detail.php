<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use appxq\sdii\widgets\ModalForm;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$widget_ref = SubjectManagementQuery::getWidgetById($schedule_id);
$scheduleOptions = appxq\sdii\utils\SDUtility::string2Array($widget_ref['options']);
$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);
$ezform_detail = EzfQuery::getEzformOne($subject_detail_ezf);

$visitAll = [];
$i = 0;

foreach ($visitSchedule as $key => $value) {
    $visitAll[$i]['key'] = $key;
    $visitAll[$i]['visit_name'] = $value['visit_name'];
    $visitAll[$i]['actual_date'] = $value['actual_date'];
    $visitAll[$i]['plan_distance'] = isset($value['plan_date']) ? $value['plan_date'] : '';
    $visitAll[$i]['visit_field'] = $value['visit_name_mapping'];
    $i++;
}

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];
$disabled = false;
if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) {
    $columns[] = [
        'class' => 'appxq\sdii\widgets\ActionColumn',
        'contentOptions' => ['style' => 'width:110px;min-width:110px;text-align: center;'],
        'template' => '{view} {update} {delete} ',
        'buttons' => [
            'view' => function ($url, $data, $key) use($subject_detail_ezf, $reloadDiv, $modal) {
                //if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                return \backend\modules\ezforms2\classes\EzfHelper::btn($subject_detail_ezf)
                                ->reloadDiv('display-detail')
                                ->modal($modal)
                                ->label('<i class="fa fa-eye"></i>')
                                ->options(['class' => 'btn btn-default btn-xs'])
                                ->reloadDiv($reloadDiv)
                                ->buildBtnView($data['id']);
                //}
                //}
            },
            'update' => function ($url, $data, $key) use($subject_detail_ezf, $reloadDiv, $modal) {
                //if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                return \backend\modules\ezforms2\classes\EzfHelper::btn($subject_detail_ezf)
                                ->reloadDiv('display-detail')
                                ->modal($modal)
                                ->label('<i class="fa fa-pencil"></i>')
                                ->options(['class' => 'btn btn-primary btn-xs'])
                                ->reloadDiv($reloadDiv)
                                ->buildBtnEdit($data['id']);
                //}
                //}
            },
            'delete' => function ($url, $data, $key) use($subject_detail_ezf, $reloadDiv, $modal) {
                //if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                return \backend\modules\ezforms2\classes\EzfHelper::btn($subject_detail_ezf)
                                ->reloadDiv('display-detail')
                                ->label('<i class="fa fa-trash"></i>')
                                ->options(['class' => 'btn btn-danger btn-xs'])
                                ->reloadDiv($reloadDiv)
                                ->buildBtnDelete($data['id']);
                //}
            },
        ],
    ];
}

foreach ($detail_column2 as $key => $value) {
    $columns[] = [
        'attribute' => $value,
        'format' => 'raw',
        'value' => function ($data) use($value, $subject_detail_ezf, $visitSchedule) {
            $ezf_field = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($subject_detail_ezf, $value);

            $result = "";

            if ($value == 'type_visit') {
                $ezf_field_data = \appxq\sdii\utils\SDUtility::string2Array($ezf_field['ezf_field_data']);
                $result = $ezf_field_data['items'][$data[$value]];
            } else if ($value == 'visit_name' || $value == 'next_visit_name') {
                if (isset($visitSchedule[$data[$value]]))
                    $result = $visitSchedule[$data[$value]]['visit_name'] == '' ? $data[$value] : $visitSchedule[$data[$value]]['visit_name'];
            } else {

                $result = $data[$value];
            }

            return "<span  data-toggle=\"tooltip\" title=\"{$result}\">{$result}</span>";
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ];
}

foreach ($detail_column2 as $key => $value) {
    if ($value == 'visit_name') {
        $columns[] = [
            'attribute' => $value,
            'header' => 'CRFs for visit',
            'format' => 'raw',
            'value' => function ($data) use($value, $visitSchedule) {
                $formList = [];
                if (isset($visitSchedule[$data[$value]]))
                    $formList = \appxq\sdii\utils\SDUtility::string2Array($visitSchedule[$data[$value]]['form_list']);

                $proData = SubjectManagementQuery::GetTableDataNotEzform('zdata_visit_procedure', ['visit_name' => $data['visit_name']]);

                $proDataVisit = null;
                $proForms = [];
                if ($proData) {

                    foreach ($proData as $keyPro => $valPro) {
                        $proDataVisit = SubjectManagementQuery::GetTableData('zdata_procedure', ['id' => $valPro['procedure_name']], 'one');

                        if (isset($proDataVisit['ezform_crf']) && $proDataVisit['ezform_crf'] != null) {
                            $formArr = appxq\sdii\utils\SDUtility::string2Array($proDataVisit['ezform_crf']);
                            $proForms = array_merge($proForms, $formArr);
                        }
                    }
                }

                if (count($formList) > 0) {
                    $formList = array_merge($formList, $proForms);
                } else {
                    $formList = $proForms;
                }

                if (count($formList) > 0) {
                    $addForm = [];
                    foreach ($formList as $valForm) {
                        if (!in_array($valForm, $addForm))
                            $addForm[] = $valForm;
                    }

                    $formList = $addForm;
                }

                $success = 0;
                $waiting = 0;
                $no_process = 0;
                $form_all = 0;
                foreach ($formList as $keyForm => $valForm) {
                    $ezformThis = EzfQuery::getEzformOne($valForm);
                    if ($ezformThis) {
                        $dataForm = SubjectManagementQuery::GetTableData($ezformThis, " (`target`='{$data['target']}' OR `subject_link`='{$data['target']}') AND `visit_link`='{$data['visit_name']}' ", 'one');
                        $form_all += 1;
                        if ($dataForm['rstat'] == '2') {
                            $success += 1;
                        } else if ($dataForm['rstat'] == '1') {
                            $waiting += 1;
                        } else {
                            $no_process += 1;
                        }
                    }
                }

                $html = Html::a($form_all, "javascript:void(0)", [
                            'data-type' => "1",
                            'data-form-list' => base64_encode(appxq\sdii\utils\SDUtility::array2String($formList)),
                            'class' => "label label-default status_button ",
                            'data-target' => $data['target'],
                            'data-visit' => $data[$value],
                            'data-toggle' => "tooltip",
                            'title' => "All Form",
                ]);
                $html .= " / " . Html::a($success, "javascript:void(0)", [
                            'data-type' => "2",
                            'data-form-list' => base64_encode(appxq\sdii\utils\SDUtility::array2String($formList)),
                            'class' => "label label-success status_button ",
                            'data-target' => $data['target'],
                            'data-visit' => $data[$value],
                            'data-toggle' => "tooltip",
                            'title' => "Completed",
                ]);
                $html .= " / " . Html::a($waiting, "javascript:void(0)", [
                            'data-type' => "3",
                            'data-form-list' => base64_encode(appxq\sdii\utils\SDUtility::array2String($formList)),
                            'class' => "label label-warning status_button ",
                            'data-target' => $data['target'],
                            'data-visit' => $data[$value],
                            'data-toggle' => "tooltip",
                            'title' => "Waiting",
                ]);
                $html .= " / " . Html::a($no_process, "javascript:void(0)", [
                            'data-type' => "4",
                            'data-form-list' => base64_encode(appxq\sdii\utils\SDUtility::array2String($formList)),
                            'class' => "label label-danger status_button ",
                            'data-target' => $data['target'],
                            'data-visit' => $data[$value],
                            'data-toggle' => "tooltip",
                            'title' => "Not Process",
                ]);

                if (count($formList) == 0) {
                    $html = "<div style='width:100%;background-color:#e6e6e6;' >" . $html . "</div>";
                } elseif ($success >= count($formList)) {
                    $html = "<div style='width:100%;background-color:#6fdc6f;' >" . $html . "</div>";
                }

                return $html;
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:100px;text-align: center;'],
        ];
    }
}

//$columns[] = [
//    'attribute' => 'Next Scheduled Visit',
//    'format' => 'raw',
//    'value' => function ($data) use($visitAll,$ezform_detail) {
//        $inx = 0;
//        foreach ($visitAll as $key => $val) {
//            if ($val['visit_name'] == $data['visit_name']) {
//                $inx = $key;
//            }
//        }
//        $detailAll = SubjectManagementQuery::GetTableData($ezform_detail, ['target' => $data['target']]);
//        $result = $visitAll[$inx + 1]['visit_name'];
//        if($inx < count($detailAll)-1){
//            return "<span style='font-size:14px;' class='label label-default'>{$result}</span>";
//        }else{
//            return "<span style='font-size:14px;' class='label label-warning'>{$result}</span>";
//        }
//    },
//    'headerOptions' => ['style' => 'text-align: center;'],
//    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
//];
//$columns[] = [
//    'attribute' => 'Planed Date Of Next Schedule Visit',
//    'format' => 'raw',
//    'value' => function ($data) use($visitAll, $ezform_detail,$scheduleOptions) {
//        $inx = 0;
//        foreach ($visitAll as $key => $val) {
//            if ($val['visit_name'] == $data['visit_name']) {
//                $inx = $key;
//            }
//        }
//         $plan_date = '';       
//        if($scheduleOptions['type_system']=='2'){
//            $detailAll = SubjectManagementQuery::GetTableData($ezform_detail, ['target' => $data['target']]);
//            if($inx<2){
//                $detail_data = SubjectManagementQuery::GetTableData($ezform_detail, ['target' => $data['target'], $visitAll[$inx + 1]['visit_field'] => $visitAll[0]['visit_name']], 'one');
//                $actual_date = $detail_data[$visitAll[0]['actual_date']];
//                $date = new DateTime($actual_date);
//                $date->modify('+ '.$visitAll[$inx + 1]['plan_distance'].' day');
//                $plan_date = $date->format('Y-m-d');
//            }else{
//                $detail_data = SubjectManagementQuery::GetTableData($ezform_detail, ['target' => $data['target'], $visitAll[$inx + 1]['visit_field'] => $visitAll[1]['visit_name']], 'one');
//                $actual_date = $detail_data[$visitAll[1]['actual_date']];
//                $date = new DateTime($actual_date);
//                $date->modify('+ '.$visitAll[$inx + 1]['plan_distance'].' day');
//                $plan_date = $date->format('Y-m-d');
//            }
//        }else{
//            
//        }
//        if($inx < count($detailAll)-1){
//            return "<span style='font-size:14px;' class='label label-default'>{$plan_date}</span>";
//        }else{
//            return "<span style='font-size:14px;' class='label label-warning'>{$plan_date}</span>";
//        }
//    },
//    'headerOptions' => ['style' => 'text-align: center;'],
//    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
//];

if (isset($default_column) && $default_column) {
    $columns[] = [
        'attribute' => 'xsourcex',
        'format' => 'raw',
        'value' => function ($data) {
            return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$data['sitename']}\">{$data['xsourcex']}</span>";
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ];
    $columns[] = [
        'attribute' => 'userby',
        'contentOptions' => ['style' => 'width:200px;'],
        'filter' => '',
    ];
    $columns[] = [
        'attribute' => 'rstat',
        'format' => 'raw',
        'value' => function ($data) {
            $alert = 'label-default';
            if ($data['rstat'] == 0) {
                $alert = 'label-info';
            } elseif ($data['rstat'] == 1) {
                $alert = 'label-warning';
            } elseif ($data['rstat'] == 2) {
                $alert = 'label-success';
            } elseif ($data['rstat'] == 3) {
                $alert = 'label-danger';
            }

            $rstat = backend\modules\core\classes\CoreFunc::itemAlias('rstat', $data['rstat']);
            return "<h4 style=\"margin: 0;\"><span class=\"label $alert\">$rstat</span></h4>";
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:120px;text-align: center;'],
        'filter' => Html::activeDropDownList($searchModel, 'rstat', backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class' => 'form-control', 'prompt' => 'All']),
    ];
}
?>
<h4>Subject Visit Log</h4>
<label class="label label-default">All Forms</label>
<label class="label label-success">Completed</label>
<label class="label label-warning">Waiting</label>
<label class="label label-danger">Not Process</label>
<?php
//backend\modules\ezforms2\classes\EzfStarterWidget::begin();
//echo EzfHelper::ui($subject_detail_ezf)->ezf_id($subject_detail_ezf)->target($data_id)->reloadDiv('display-detail')->target($data_id)->data_column($detail_column)->default_column(false)->buildEmrGrid();
//backend\modules\ezforms2\classes\EzfStarterWidget::end();
?>
<?=
yii\grid\GridView::widget([
    'id' => "$reloadDiv-detail-grid",
    'dataProvider' => $dataProvider,
    'filterModel' => isset($searchModel) ? $searchModel : null,
    'columns' => $columns,
]);
?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>

    $('.status_button').click(function () {
        var form_list = $(this).attr('data-form-list');
        var type = $(this).attr('data-type');
        var target = $(this).attr('data-target');
        var visit_id = $(this).attr('data-visit');
        var modal_show = $('#modal-show-formlist');
        var url = "/subjects/electronic-data/dashboard-modal?form_list=" + form_list + "&type=" + type + "&target=" + target + "&visit_id=" + visit_id;
        var modal_content = modal_show.find('.modal-content');
        modal_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        modal_content.attr('id', 'modal-content-formlist');
        modal_content.attr('data-url-old', url);
        modal_show.modal('show');
        modal_show.find('.modal-content').load(url);
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
