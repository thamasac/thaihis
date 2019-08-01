<code><span style="color: #000000">
        <span style="color: #0000BB"></span><span style="color: #007700">[<br />&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #DD0000">'ezf_id'&nbsp;</span><span style="color: #007700">=&gt;&nbsp;</span><span style="color: #DD0000">'1519736596024146600'<br />&nbsp;&nbsp;&nbsp;&nbsp;'modal'&nbsp;</span><span style="color: #007700">=&gt;&nbsp;</span><span style="color: #DD0000">'grid-widget-custom-sub-modal'<br />&nbsp;&nbsp;&nbsp;&nbsp;'dataid'&nbsp;</span><span style="color: #007700">=&gt;&nbsp;</span><span style="color: #DD0000">'1520672658012889500'<br />&nbsp;&nbsp;&nbsp;&nbsp;'reloadDiv'&nbsp;</span><span style="color: #007700">=&gt;&nbsp;</span><span style="color: #DD0000">'grid-widget-custom'<br />&nbsp;&nbsp;&nbsp;&nbsp;'v'&nbsp;</span><span style="color: #007700">=&gt;&nbsp;</span><span style="color: #DD0000">'v1'<br /></span><span style="color: #007700">]</span>
    </span>
</code>

<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];
//if (!$disabled) {
$columns[] = [
    'class' => 'appxq\sdii\widgets\ActionColumn',
    'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
    'template' => '{update}', //{view}',
    'buttons' => [
        'view' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal) {
            if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['assign_name'])) {
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/ezform-view',
                                    'ezf_id' => $ezform['ezf_id'],
                                    'dataid' => $data['id'],
                                    'modal' => $modal,
                                    'reloadDiv' => $reloadDiv,
                                ]), [
                            'data-action' => 'update',
                            'title' => Yii::t('yii', 'View'),
                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            'class' => 'btn btn-primary btn-xs',
                ]);
            }
        },
        'update' => function ($url, $data, $key) use($ezform, $ezform_detail, $reloadDiv, $modal, $type_id) {
//                appxq\sdii\utils\VarDumper::dump($data['ptid']);
//            if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform_name, Yii::$app->user->id, $data['user_create'])) {
            return backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezform['ezf_id'])->target($data['ptid'])
                            ->reloadDiv($reloadDiv)->label('<span class="glyphicon glyphicon-eye-open"></span>')->options(['class' => 'btn btn-primary btn-xs'])->buildBtnEdit($data['id']);
//            }
        },
    ],
];
//}
if ($default_column) {
    $columns[] = [
        'attribute' => 'create_date',
        'value' => function ($data) {
            return !empty($data['create_date']) ? \appxq\sdii\utils\SDdate::mysql2phpDateTime($data['create_date']) : '';
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
        'filter' => \kartik\daterange\DateRangePicker::widget([
            'model' => $searchModel,
            'attribute' => 'create_date',
            'convertFormat' => true,
            //'useWithAddon'=>true,
            'options' => ['id' => 'dr_' . $reloadDiv . '_' . $modal, 'class' => 'form-control'],
            'pluginOptions' => [
                'locale' => [
                    'format' => 'd-m-Y',
                    'separator' => ' to ',
                //'language'=>'TH',
                ],
            //'opens'=>'left'
            ]
        ]),
    ];
}

$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id OR ezf_id = :ezf_name', [':ezf_id' => $ezform->ezf_id, ':ezf_name' => $ezf_name['ezf_id']])
        ->orderBy(['ezf_field_order' => SORT_ASC])
        ->all();

$fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform->ezf_version);
if (!isset(Yii::$app->session['ezf_input'])) {
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}
array_unshift($data_column, 'F2v1');
//\appxq\sdii\utils\VarDumper::dump($data_column);

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
            try {
                $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
            } catch (Exception $ex) {
//                \appxq\sdii\utils\VarDumper::dump($ex);
            }

            if (in_array($fieldName, $column_status)) {
                if ($keyStatus == '')
                    $keyStatus = $key;
            }
            $colTmp = [
                'attribute' => $var,
                'label' => $label,
                'format' => 'raw',
                'value' => function ($data) use($dataInput, $value, $fieldName, $modelFields, $ezf_input, $column_download, $column_status, $ezform) {

                    if (in_array($fieldName, $column_status)) {
                        $data_check = SDUtility::string2Array($data['check_user']);
                        foreach ($data_check as $value) {
                            if ($value == Yii::$app->user->id) {
                                if ($data['action_required'] == 1) {
                                    return Html::tag('label', 'Completed', ['class' => 'label label-success']);
                                } else if ($data['action_required'] == 2) {
                                    return Html::tag('label', 'Reviewed', ['class' => 'label label-success']);
                                } else if ($data['action_required'] == 3) {
                                    if ($data['final_status'] == 1) {
                                        return Html::tag('label', 'Approved', ['class' => 'label label-success']);
                                    } else if ($data['final_status'] == 2) {
                                        return Html::tag('label', 'Not Approve', ['class' => 'label label-danger']);
                                    }
                                }
                            }
                        }
                        return Html::tag('label', 'Waiting', ['class' => 'label label-warning']);
                    }if (in_array($fieldName, $column_download)) {
                        $imgPath = Yii::getAlias('@storageUrl/ezform/fileinput/');
                        $result = "";
                        if ($data[$fieldName] != '') {//
                            $result = Html::a('Download', '#', ['class' => 'btn-download', 'data-value' => $data[$fieldName], 'data-id' => $data['id'], 'ezf_id' => $ezform->ezf_id]);
                        }
                        return $result;
                    } else {
                        if ($fieldName == 'owner') {
                            $name = backend\modules\ezforms2\classes\MyWorkbenchFunc::GetUserName($data[$fieldName]);
                            return $name == '' ? '' : "<div style='margin-top:5px;' class='label label-primary'>" . $name['firstname'] . ' ' . $name['lastname'] . "</div>";
                        } else if ($fieldName == 'final_action') {
//                            \appxq\sdii\utils\VarDumper::dump($data['check_user']);
                            $data_check = SDUtility::string2Array($data['check_user']);
                            $disabled = FALSE;
                            foreach ($data_check as $value) {
                                if ($value == Yii::$app->user->id) {
                                    $disabled = TRUE;
                                }
                            }
                            if ($data[$fieldName] == '' || $data[$fieldName] == 0) {
                                return Html::button($disabled ? 'Acknowledged' : 'Acknowledge', [
                                            'data-id' => $data['id'],
                                            'data-value' => '1',
                                            'data-tb' => $ezform['ezf_table'],
                                            'disabled' => $disabled,
                                            'class' => $disabled ? 'btn btn-success btnStatus' : 'btn btn-warning btnStatus'
                                ]);
                            } else if ($data[$fieldName] == 2 || $data[$fieldName] == 3) {
                                return Html::button($disabled ? 'Reviewed' : 'Review', [
                                            'data-id' => $data['id'],
                                            'data-value' => '1',
                                            'data-tb' => $ezform['ezf_table'],
                                            'disabled' => $disabled,
                                            'class' => $disabled ? 'btn btn-success btnStatus' : 'btn btn-warning btnStatus'
                                ]);
                            } else if ($data[$fieldName] == 4 || $data[$fieldName] == 5) {
                                return \kartik\select2\Select2::widget([
                                            'id' => 'approve',
                                            'name' => 'approve',
                                            'hideSearch' => true,
                                            'data' => ['1' => 'Yes', '2' => 'No'],
                                            'options' => [
                                                'placeholder' => $disabled ? 'Approved' : 'Approve',
                                                'data-id' => $data['id'],
                                                'data-tb' => $ezform['ezf_table'],
                                                'disabled' => $disabled,
                                                'class' => 'approve'
                                            ],
                                ]);
                            }
                        } else {
//                            \appxq\sdii\utils\VarDumper::dump($modelFields);
                            foreach ($modelFields as $key => $field) {

                                $var = $field['ezf_field_name'];
                                $version = $field['ezf_version'];
                                if ($fieldName == $var && ($data['ezf_version'] == $version || $version == 'all')) {
//                                    \appxq\sdii\utils\VarDumper::dump($version);
                                    if ($ezf_input) {
                                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                                    }
                                    $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data);
                                    return $dataText == '' ? '' : $dataText;
                                }
                            }
                            return '';
                        }
                    }
                },
                'contentOptions' => ['style' => "min-width:100px;"],
                'filter' => $htmlFilter,
            ];

            if (is_array($field) && isset($field['attribute'])) {
                $colTmp = \yii\helpers\ArrayHelper::merge($colTmp, $field);
            }

            $changeField = FALSE;

            $columns[] = $colTmp;

            break;
        }
    }

    if ($changeField) {
        if (is_array($field) && isset($field['attribute'])) {
            $columns[] = $field;
        } else {
            $columns[] = [
                'attribute' => $field,
                'label' => $field,
            ];
        }
    }
}


//$columns[] = [
//    'label'=>'Action Require',
//    //'attribute' => 'status',
//    'format'=>'raw',
//    'value'=>function($data){
//             return $data['status'];
//        
//    },
//    'filter' => Html::dropDownList('status', '', ['Acknowledge'=>'Acknowledge','Review'=>'Review','Approve'=>'Approve'], ['class' => 'form-control', 'prompt' => 'All']),
//    'headerOptions' => ['style' => 'text-align: center;'],
//    'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
//];
//$columns[] = [
//    'label'=>'Status',
//    //'attribute' => 'status',
//    'format'=>'raw',
//    'value'=>function($data){
//             return "<label class='label label-warning'>Waiting</label>";
//        
//    },
//    'headerOptions' => ['style' => 'text-align: center;'],
//    'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
//];


if ($default_column) {
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
//appxq\sdii\utils\VarDumper::dump($dataProvider);
?>

<?=
yii\grid\GridView::widget([
    'id' => "$reloadDiv-emr-grid",
    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
    'columns' => $columns,
]);
?>

<?php
//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';

$this->registerJs("

$('#$reloadDiv-emr-grid tbody tr td a').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    
    if(action === 'update' || action === 'create'){
        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$modal').modal('show')
        .find('.modal-content')
        .load(url);
    } else if(action === 'delete') {
        yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
                $.post(
                        url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "'}
                ).done(function(result){
                        if(result.status == 'success'){
                            " . SDNoty::show('result.message', 'result.status') . "
                            var urlreload =  $('#$reloadDiv').attr('data-url');        
                            getUiAjax(urlreload, '$reloadDiv');        
                        } else {
                                " . SDNoty::show('result.message', 'result.status') . "
                        }
                }).fail(function(){
                        " . SDNoty::show("'" . "Server Error'", '"error"') . "
                        console.log('server error');
                });
        });
    }
    return false;
});

$('#$reloadDiv-emr-grid').on('beforeFilter', function(e) {
    var \$form = $(this).find('form');
    $.ajax({
	method: 'GET',
	url: \$form.attr('action'),
        data: \$form.serialize(),
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#$reloadDiv').html(result);
	}
    });
    return false;
});

$('#$reloadDiv-emr-grid .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv-emr-grid thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

function getUiAjax(url, divid) {
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+divid).html(result);
        }
    });
}

$('.btnStatus').on('click',function(){
    UpdateResult($(this).attr('data-id'),$(this).attr('data-tb'),$(this).attr('data-value'));
});

$('.approve').on('change',function(){
        var id = $(this).attr('data-id');
        var tb = $(this).attr('data-tb');
        var value = $(this).val();
        var text = '';
        if(value != ''){
            if(value == 1){
                text = '" . Yii::t('ezform', 'Confirm Approve') . "'+'<code>Approve</code>'
            }else{
                text = '" . Yii::t('ezform', 'Confirm Approve') . "'+'<code>Not Approve</code>'
            }
                bootbox.confirm({
                    title: '<h4><b>Confirm</b></h4>',
                    message: '<font size=\'+2\'>'+text+'</font>',
                    buttons: {
                        cancel: {
                            label: '<i class=\'fa fa-times\'></i> '+'" . Yii::t('ezform', 'Cancel') . "'
                        },
                        confirm: {
                            label: '<i class=\'fa fa-check\'></i> '+'" . Yii::t('ezform', 'OK') . "'
                        }
                    },
                    callback: function (result) {
                        if(result){
                            UpdateResult(id,tb,value);
                        }else{
                            $('.approve').val('').trigger('change')
                        }
                        
                    }
        
                });
        }
    
});

function UpdateResult(id='',ezf_table='',value=''){

     $.ajax({
        method: 'POST',
        url: '/ezforms2/my-workbench/update-result',
        dataType: 'HTML',
        data:{
            ezf_table:ezf_table,
            id:id,
            value:value
        },
        success: function(result, textStatus) {
            var url = $('#$reloadDiv').attr('data-url');
            if(result){
            " . SDNoty::show("'" . Yii::t('ezform', 'Complete') . "'", '"success"') . "
                getUiAjax(url, '$reloadDiv');
            }else{
            " . SDNoty::show("'" . Yii::t('ezform', 'Failed') . "'", '"error"') . "
                getUiAjax(url, '$reloadDiv');
            }
        }
    });
}

$('#$modal').on('shown.bs.modal', function () {
    setTimeout(()=>{
        $('input').attr('disabled','disabled');
        $('#h2c,.btn-submit').hide();
        $('select').attr('disabled', 'disabled');
    },100);
    
    
});

$('.btn-download').click(function(){
    var file_name = $(this).attr('data-value');
    window.location.href= '" . Yii::getAlias('@storageUrl/ezform/fileinput/') . "'+file_name;
    var dataid = $('.btn-download').attr('data-id');
    var ezf_id = $('.btn-download').attr('ezf_id');
    
    $.ajax({
        url:'/ezforms2/my-workbench/update-result',
        method:'get',
        type:'html',
        data:{dataid:dataid,ezf_id:ezf_id,result:'1',type:'download'},
        success:function(data){
            var urlreload =  $('grid-workbench-emr-grid').attr('data-url');
            getUiAjax(urlreload, 'grid-workbench-emr-grid');
            return false;
        }
        
    })
})

");
?>