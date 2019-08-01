<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];




$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id', [':ezf_id' => $ezform->ezf_id])
        ->orderBy(['ezf_field_order' => SORT_ASC])
        ->all();

$fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform->ezf_version);
if (!isset(Yii::$app->session['ezf_input'])) {
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}


//yii\helpers\VarDumper::dump($dataProvider->getModels());
foreach ($data_column as $field) {

    $fieldName = $field;
    if (is_array($field) && isset($field['attribute'])) {
        $fieldName = $field['attribute'];
    }

    $changeField = TRUE;
    foreach ($fieldsGroup as $key => $value) {
        $var = $value['ezf_field_name'];
        $label = $value['ezf_field_label'];

        if ($fieldName == $var) {
            $dataInput;
            $ezf_input;
            if (isset(Yii::$app->session['ezf_input'])) {
                $ezf_input = Yii::$app->session['ezf_input'];
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

//            $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
//           
            $colTmp = [
                'attribute' => $var,
                'label' => $label,
                'format' => 'raw',
                'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
                    foreach ($modelFields as $key => $field) {
                        $var = $field['ezf_field_name'];
                        $version = $field['ezf_version'];
                        if ($fieldName == $var && ($data['ezf_version'] == $version || $version == 'all')) {
                            if ($ezf_input) {
                                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                            }
                            $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data);
                            if ($fieldName == 'sender' || $fieldName == 'action') {
                                return "<div style='margin-top:5px;' class='label label-primary'>" . $dataText . "</div><br/>";
                            } else if ($fieldName == 'mandatory') {
                                $class = $data[$var] ? 'label-success' : 'label-danger';
                                return "<div style='margin-top:5px;' class='label " . $class . "'>" . $dataText . "</div><br/>";
//                            } else if ($fieldName == 'file_upload') {
//                                return Html::a('Download', Yii::getAlias('@storageUrl') . "/ezform/fileinput/" . $data[$var], [
//                                            'target' => '_blank',
//                                            'class' => 'btnViewFile ',
////                                            'data-status' => '2',
//                                            'data-id' => $data['id'],
////                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                ]);
                            } else {
                                return $dataText == '' ? '' : $dataText;
                            }
                        }
                    }
                    return '';
                },
                'headerOptions' => ['class' => "text-center"],
                'contentOptions' => ['style' => "min-width:100px;", 'class' => "text-center"],
                'filter' => $var,
            ];



            if (is_array($field) && isset($field['attribute'])) {
                $colTmp = \yii\helpers\ArrayHelper::merge($colTmp, $field);
            }

            $changeField = FALSE;
            $columns[] = $colTmp;
            break;
        }
    }

    if ($changeField && $fieldName && $fieldName != 'readonly' && $fieldName != 'readonly' && $fieldName != 'ezf_id' && $fieldName != 'data_id' && $fieldName != 'module_id' && $fieldName != 'status_view' && $fieldName != 'assign_to') {
        if (is_array($field) && isset($field['attribute'])) {
            $columns[] = $field;
        } else {
            if ($field != 'target')
                $columns[] = [
                    'attribute' => $field,
                    'label' => $field,
                ];
        }
    }
}

$columns[] = [
    'label' => 'Satus',
    'format' => 'raw',
    'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
        $textReturn = '';
        if ($data['complete_date'] == '' && $data['status_view'] == '0') {
            $textReturn = "<div style='margin-top:5px;' class='label label-danger'>New</div>";
        }else if ($data['complete_date'] == '' && $data['status_view'] == '1') {
            $textReturn = "<div style='margin-top:5px;' class='label label-warning'>Waiting</div>";
        }else if (($data['complete_date'] != '' && $data['status_view'] == '1') || ($data['complete_date'] != '' && $data['status_view'] != '0')) {
            $textReturn = "<div style='margin-top:5px;' class='label label-success'>Completed</div>";
        }
        return $textReturn;
    },
    'headerOptions' => ['class' => "text-center"],
    'contentOptions' => ['style' => "min-width:100px;", 'class' => "text-center"],
    'filter' => $var,
];

//$columns[] = [
//    'class' => 'appxq\sdii\widgets\ActionColumn',
//    'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
//    'template' => '{view}', //'{view} {update} {delete} ',
//    'buttons' => [
//        'view' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal) {
////                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
//            $view = $data['readonly'] ? 'ezform-view' : 'ezform';
//            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/' . $view,
//                                'ezf_id' => $data['ezf_id'],
//                                'dataid' => $data['data_id'],
//                                'id' => $data['id'],
//                                'modal' => $modal,
//                                'reloadDiv' => $reloadDiv,
//                            ]), [
//                        'data-id' => $data['id'],
//                        'data-complete' => $data['complete_date'],
//                        'data-button' => $data['action'],
//                        'data-ezf_id' => $ezform->ezf_id,
//                        'data-tb' => $data['ezf_id'],
//                        'data-form' => $data['data_id'],
//                        'data-action' => 'view',
//                        'title' => Yii::t('yii', 'View'),
//                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                        'class' => 'btn btn-primary btn-xs',
//            ]);
////                }
//        },
//        'update' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal) {
//            if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
//                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/ezform-data/ezform',
//                                    'ezf_id' => $ezform->ezf_id,
//                                    'dataid' => $data['id'],
//                                    'modal' => $modal,
//                                    'reloadDiv' => $reloadDiv,
//                                ]), [
//                            'data-action' => 'update',
//                            'title' => Yii::t('yii', 'Update'),
//                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                            'class' => 'btn btn-primary btn-xs',
//                ]);
//            }
//        },
//        'delete' => function ($url, $data, $key) use($ezform, $reloadDiv) {
//            if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
//                return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/ezform-data/delete',
//                                    'ezf_id' => $ezform->ezf_id,
//                                    'dataid' => $data['id'],
//                                    'reloadDiv' => $reloadDiv,
//                                ]), [
//                            'data-action' => 'delete',
//                            'title' => Yii::t('yii', 'Delete'),
//                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
//                            'data-method' => 'post',
//                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                            'class' => 'btn btn-danger btn-xs',
//                ]);
//            }
//        },
//    ],
//];
?>
<div id="test"></div>
<?=
appxq\sdii\widgets\GridView::widget([
    'id' => "$reloadDiv-data-complete",
    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
    'columns' => $columns,
]);
?>

<?=
appxq\sdii\widgets\ModalForm::widget([
    'id' => $reloadDiv . "-modal",
    'size' => 'modal-lg',
]);
?>

<?php
//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';

$this->registerJs("

$('.btnView').removeClass('ezform-main-open');




$('#$reloadDiv-data-complete .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv-data-complete thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv-data-complete .btnViewFile').on('click',function(){
    var url = $(this).prop('href');
    var target = $(this).prop('target');
    window.open(url, target);
});




function getUiAjax(url, divid) {
    $.ajax({
        method: 'GET',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+divid).html(result);
        }
    });
}

");
?>