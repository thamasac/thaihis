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

        if ($fieldName == $var && $var != 'readonly' && $var != 'readonly' && $var != 'ezf_id' && $var != 'data_id' && $var != 'module_id' && $var != 'status_view' && $var != 'assign_to') {
            $dataInput;
            $ezf_input;
            if (isset(Yii::$app->session['ezf_input'])) {
                $ezf_input = Yii::$app->session['ezf_input'];
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

//            $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
            if ($var == 'final_action') {
                $label = 'Action';
            }
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
                            if ($fieldName == 'sender') {
                                return "<div style='margin-top:5px;' class='label label-primary'>" . $dataText . "</div><br/>";
                            } else if ($fieldName == 'mandatory') {
                                $class = $data[$var] ? 'label-success' : 'label-danger';
                                return "<div style='margin-top:5px;' class='label " . $class . "'>" . $dataText . "</div><br/>";
                            } else if ($fieldName == 'action') {
                                if($data[$fieldName] == 1){
                                    return "<div style='margin-top:5px;' class='label label-primary'>Require</div><br/>";
                                }
                                return "<div style='margin-top:5px;' class='label label-primary'>" . $dataText . "</div><br/>";
                            } else if ($fieldName == 'file_upload') {
                                return $dataText != '' ? Html::a('Download', Yii::getAlias('@storageUrl') . "/ezform/fileinput/" . $data[$fieldName], [
                                            'target' => '_blank',
                                            'class' => 'btnViewFile ',
//                                            'data-status' => '2',
                                            'data-id' => $data['id'],
//                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        ]) : '';
//                            } else if($fieldName == 'detail'){
//                                return"<div class='text-sm-left'>$dataText</div>";
                            } else {
                                return $dataText == '' ? '' : $dataText;
                            }
                        }
                    }
                    return '';
                },
                'headerOptions' => ['class' => "text-center"],
                'contentOptions' => ['style' => "min-width:100px;"],
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

//$columns[] = [
//    'label' => 'Satus',
//    'format' => 'raw',
//    'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
//        $textReturn = '';
//        if ($data['action'] != '') {
//            if ($data['complete_date'] == '' && $data['status_view'] == '0') {
//                $textReturn = "<div style='margin-top:5px;' class='label label-danger'>New</div>";
//            } else if ($data['complete_date'] == '' && $data['status_view'] == '1') {
//                $textReturn = "<div style='margin-top:5px;' class='label label-warning'>Waiting</div>";
//            } else if (($data['complete_date'] != '' && $data['status_view'] == '1') || ($data['complete_date'] != '' && $data['status_view'] != '0')) {
//                $textReturn = "<div style='margin-top:5px;' class='label label-success'>Complete</div>";
//            }
//        } else {
//            if ($data['status_view'] == '0') {
//                $textReturn = "<div style='margin-top:5px;' class='label label-danger'>New</div>";
//            } else if ($data['status_view'] == '1') {
//                $textReturn = "<div style='margin-top:5px;' class='label label-success'>Viewed</div>";
//            }
//        }
//        return $textReturn;
//    },
//    'headerOptions' => ['class' => "text-center"],
//    'contentOptions' => ['style' => "min-width:100px;", 'class' => "text-center"],
//    'filter' => $var,
//];

//$columns[] = [
//    'class' => 'appxq\sdii\widgets\ActionColumn',
//    'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
//    'template' => '{view}', //'{view} {update} {delete} ',
//    'buttons' => [
//        'view' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal) {
////                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
//            $view = $data['readonly'] ? 'ezform-view' : 'ezform';
//            if ($data['type_link'] == '1') {
//                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $data['url'], [
//                            'title' => Yii::t('yii', 'View'),
//                            'data-action' => 'redirect',
//                            'data-view' => $data['status_view'],
//                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                            'class' => 'btn btn-default btn-xs btnDetail',
//                ]);
//            } else if ($data['type_link'] == '2') {
//                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/' . $view,
//                                    'ezf_id' => $data['ezf_id'],
//                                    'dataid' => $data['data_id'],
//                                    'id' => $data['id'],
//                                    'modal' => $modal,
//                                    'reloadDiv' => $reloadDiv,
//                                ]), [
//                            'data-id' => $data['id'],
//                            'data-complete' => $data['complete_date'] != '' ? '1' : '0',
//                            'data-button' => $data['action'],
//                            'data-ezf_id' => $ezform->ezf_id,
//                            'data-tb' => $data['ezf_id'],
//                            'data-form' => $data['data_id'],
//                            'data-view' => $data['status_view'],
//                            'data-action' => 'view',
//                            'title' => Yii::t('yii', 'View'),
//                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                            'class' => 'btn btn-default btn-xs btnDetail',
//                ]);
//            }
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


echo appxq\sdii\widgets\GridView::widget([
    'id' => "$reloadDiv-notify",
    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
    'columns' => $columns,
]);


echo appxq\sdii\widgets\ModalForm::widget([
    'id' => $reloadDiv . "-modal",
    'size' => 'modal-lg',
]);
?>

<?php

//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';

$this->registerJs("

$('.btnView').removeClass('ezform-main-open');


//$('#$reloadDiv-notify tbody tr td a').on('click', function() {
//    
//    var url = $(this).attr('href');
//    var action = $(this).attr('data-action');
//    if($(this).attr('data-view') == '0'){
//        $.ajax({
//                method: 'POST',
//                url: '/notify/notify/viewed?id=' + $(this).attr('data-id'),
//                dataType: 'HTML',
//                success: function (result, textStatus) {
//
//                }
//        });
//    }
//    if(action === 'redirect'){
//        window.location = url;
//    }else if(action === 'update' || action === 'create'){
//        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
//        $('#$modal').modal('show')
//        .find('.modal-content')
//        .load(url);
//    } else if(action === 'view') {
//        var btn = '';
//        var data_id = $(this).attr('data-id');
//        var data_complete = $(this).attr('data-complete');
//        var data_btn = $(this).attr('data-button');
//        var data_tb = $(this).attr('data-tb');
//        var data_form = $(this).attr('data-form');
//        var ezf_id = $(this).attr('data-ezf_id');
//        var url_data = '/notify/notify/grid-complete?modal=$modal&reloadDiv=grid-complete&ezf_id='+ezf_id+'&data_form='+data_form;
//        
//        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
//        $('#$modal').modal('show');
//        $.ajax({
//            method: 'POST',
//            url: url,
//            dataType: 'HTML',
//            success: function (result, textStatus) {
//                $('#$modal').find('.modal-content').html(result);
//                if(data_btn != ''){
//                    if(data_btn == 'Acknowledge'){
//                        btn = '<button data-value=\'1\' class=\'btn btn-success btnStatus\'>Acknowledge</button>'
//                    }else if(data_btn == 'Review'){
//                        btn = '<button data-value=\'1\' class=\'btn btn-success btnStatus\'>Review</button>'
//                    }else if(data_btn == 'Approve'){
//                        btn = '<button data-value=\'2\' class=\'btn btn-success approve\'>Approve</button> <button data-value=\'3\' class=\'btn btn-danger approve\'> Not Approve</button>'
//                    }
//                    if(data_complete == '0'){
//                        $('#$modal').find('.modal-footer').append(btn+'<hr/><div data-url='+url_data+' id=\'grid-complete\'><div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div></div>');
//                    }else{
//                        $('#$modal').find('.modal-body').append('<div data-url='+url_data+' id=\'grid-complete\'><div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div></div>');
//                    }
//
//                    getUiAjax(url_data,'grid-complete');
//
//                    $('.btnStatus').on('click',function(){
//                        UpdateResult(data_form,data_tb,$(this).attr('data-value'),$(this),data_id,ezf_id);
//                    });
//                    $('.approve').on('click',function(){
//                        UpdateResult(data_form,data_tb,$(this).attr('data-value'),$(this),data_id,ezf_id);
//                    }); 
//                }
//            }
//        });
////        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
////        $('#$modal').modal('show')
////        .find('.modal-content')
////        .load(url);
//    } else if(action === 'delete') {
//        yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
//                $.post(
//                        url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "'}
//                ).done(function(result){
//                        if(result.status == 'success'){
//                                " . SDNoty::show('result.message', 'result.status') . "
//                            var urlreload =  $('#$reloadDiv').attr('data-url');        
//                            getUiAjax(urlreload, '$reloadDiv');        
//                        } else {
//                                " . SDNoty::show('result.message', 'result.status') . "
//                        }
//                }).fail(function(){
//                        " . SDNoty::show("'" . "Server Error'", '"error"') . "
//                        console.log('server error');
//                });
//        });
//    }
//    return false;
//});
//
//$('#$reloadDiv-notify').on('beforeFilter', function(e) {
//    var \$form = $(this).find('form');
//    $.ajax({
//	method: 'GET',
//	url: \$form.attr('action'),
//        data: \$form.serialize(),
//	dataType: 'HTML',
//	success: function(result, textStatus) {
//	    $('#$reloadDiv').html(result);
//	}
//    });
//    return false;
//});
//
//$('#$reloadDiv-notify .pagination a').on('click', function() {
//    getUiAjax($(this).attr('href'), '$reloadDiv');
//    return false;
//});
//
//$('#$reloadDiv-notify thead tr th a').on('click', function() {
//    getUiAjax($(this).attr('href'), '$reloadDiv');
//    return false;
//});
//
//$('#$reloadDiv-notify tbody tr').on('mouseover', function() {
//    $(this).css('cursor','pointer');
//});
//
//$('#$reloadDiv-notify tbody tr').on('dblclick', function() {
//    var url = $(this).children('td').children('.btnDetail').attr('href');
//    var action = $(this).children('td').children('.btnDetail').attr('data-action');
//    if(action == 'view'){
//        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
//        $('#$modal').modal('show')
//        .find('.modal-content')
//        .load(url);
//    }
//    if(action == 'redirect'){
//        window.location = url;
//    }
//});
//
//$('#$reloadDiv-notify .btnViewFile').on('click',function(){
//    var url = $(this).prop('href');
//    var target = $(this).prop('target');
//    window.open(url, target);
//});
//
//
//
//function UpdateResult(id='',ezf_id='',value='',btn,id_notify,ezf_notify){
//
//     $.ajax({
//        method: 'POST',
//        url: '/tmf/tmf/update-result',
//        dataType: 'HTML',
//        data:{
//            ezf_id:ezf_id,
//            id:id,
//            value:value
//        },
//        success: function(result, textStatus) {
//            var url = $('#$reloadDiv').attr('data-url');
//            if(result){
//            " . SDNoty::show("'" . Yii::t('ezform', 'Complete') . "'", '"success"') . "
//                getUiAjax(url, '$reloadDiv');
//                    btn.hide();
//                    $('.approve').hide()
//                    UpdateNotify(id_notify,ezf_notify);
//                    getUiAjax($('#grid-complete').attr('data-url'),'grid-complete');
//            }else{
//            " . SDNoty::show("'" . Yii::t('ezform', 'Failed') . "'", '"error"') . "
//                getUiAjax(url, '$reloadDiv');
////                    btn.hide();
//            }
//        }
//    });
//}
//
//function UpdateNotify(id='',ezf_id=''){
//
//     $.ajax({
//        method: 'POST',
//        url: '/notify/notify/update-result',
//        dataType: 'HTML',
//        data:{
//            ezf_id:ezf_id,
//            id:id,
//        },
//        success: function(result, textStatus) {
//        }
//    });
//}

//$('#$modal').on('hidden.bs.modal', function () {
//    var urlreload =  $('#$reloadDiv').attr('data-url');
//    $('#$reloadDiv').html('<center>Loading...</center>');        
//    getUiAjax(urlreload, '$reloadDiv');
//    window.location.reload();
//});

//function getUiAjax(url, divid) {
//    $.ajax({
//        method: 'GET',
//        url: url,
//        dataType: 'HTML',
//        success: function(result, textStatus) {
//            $('#'+divid).html(result);
//        }
//    });
//}

");
?>