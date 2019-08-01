<?php

use backend\modules\ezforms2\classes\EzfFunc;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

echo \yii\bootstrap\Tabs::widget([
    'id' => $reloadDiv . '-eztabs',
    'items' => $items,
    'encodeLabels' => false
]);

?>
<div class="clearfix"></div>
<br/>
<?php
$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];

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
if (!$disabled) {
    $columns[] = [
        'class' => 'appxq\sdii\widgets\ActionColumn',
        'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
        'template' => '{view} {update} {delete} ',
        'buttons' => [
            'view' => function ($url, $data, $key) use($ezform_name, $reloadDiv, $modal, $type_id, $data_url) {
                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform_name, Yii::$app->user->identity->profile->sitecode, $data['sitecode'])) {
                    return Html::a('<span class="glyphicon glyphicon-th-list"></span>', $data_url . "&data_id=" . $data['id'] . "&type_id=" . $type_id, [
                                'data-action' => 'view',
                                'title' => Yii::t('ec', 'Detail'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => 'btn btn-default btn-xs btnView btnDetail',
                    ]);
                }
            },
            'update' => function ($url, $data, $key) use($ezform_name, $ezform_detail, $reloadDiv, $modal, $type_id) {
//                appxq\sdii\utils\VarDumper::dump($data['ptid']);
                if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform_name, Yii::$app->user->id, $data['user_create'])) {
                    return backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($ezform_name['ezf_id'])->target($data['ptid'])
                                    ->reloadDiv($reloadDiv)->label('<span class="glyphicon glyphicon-pencil"></span>')->options(['class' => 'btn btn-primary btn-xs'])->buildBtnEdit($data['id']);
                }
            },
            'delete' => function ($url, $data, $key) use($ezform_name, $reloadDiv) {
                if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($ezform_name, Yii::$app->user->id, $data['user_create'])) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/ezform-data/delete',
                                        'ezf_id' => $ezform_name['ezf_id'],
                                        'dataid' => $data['id'],
                                        'reloadDiv' => $reloadDiv,
                                    ]), [
                                'data-action' => 'delete',
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => 'btn btn-danger btn-xs',
                    ]);
                }
            },
        ],
    ];
}

$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id_name OR ezf_id = :ezf_id_detail '
                . 'OR ezf_id = :ezf_id_type'
                , [':ezf_id_name' => $ezf_name_id,
            ':ezf_id_detail' => $ezf_detail_id,
            ':ezf_id_type' => $ezf_type_id
        ])
        ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
        ->all();

$fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform_detail->ezf_version);
if (!isset(Yii::$app->session['ezf_input'])) {
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}

foreach ($data_column as $field) {

    $fieldName = $field;
    if (is_array($field) && isset($field['attribute'])) {
        $fieldName = $field['attribute'];
    }

    $changeField = TRUE;
    foreach ($fieldsGroup as $key => $value) {
        $var = $value['ezf_field_name'];
        $label = $value['ezf_field_label'];

        if ($fieldName == $var && $var != 'doc_up') {
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

                            return $dataText == '' ? '' : $dataText;
                        }
                    }
                    return '';
                },
                'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: initial;"],
                'contentOptions' => ['class' => "", 'style' => "min-width:100px;max-width:  200px;white-space: initial;"],
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

    if ($changeField && $fieldName != 'doc_up') {
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
//appxq\sdii\utils\VarDumper::dump($dataProvider->getModels());
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
        'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: pre-wrap;"],
        'contentOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: pre-wrap;"],
        'filter' => Html::activeDropDownList($searchModel, 'rstat', backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class' => 'form-control', 'prompt' => 'All']),
    ];
}

$columns[] = [
//    'attribute' => $var,
    'label' => 'Progress',
    'format' => 'raw',
    'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
        $num = $data['ecstep1'] + $data['ecstep2'] + $data['ecstep3'] + $data['ecstep4'] + $data['ecstep5'] + $data['ecstep6'];
        $num = round($num * 16.66666666666667, 1);
        $num = is_nan($num) ? 0.0 : $num;
        $progress = '<div class="text-center bg-dark"> ' . $num . '% </div><div class="progress" >
            
  <div class="progress-bar progress-bar-success" role="progressbar" style="width: ' . $num . '%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>';
        return $progress;
    },
    'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;"],
    'contentOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;"],
    'filter' => $var,
];

$columns[] = [
    'attribute' => 'doc_up',
    'label' => 'File Upload',
    'format' => 'raw',
    'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
        return $data['doc_up'] != '' ? Html::a('Download', Yii::getAlias('@storageUrl') . "/ezform/fileinput/" . $data['doc_up'], [
                    'target' => '_blank',
                    'class' => 'btnViewFile ',
//                                            'data-status' => '2',
                    'data-id' => $data['id'],
//                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                ]) : '';
    },
    'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: pre-wrap;"],
    'contentOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: pre-wrap;"],
    'filter' => $var,
];
?>

<?=
\appxq\sdii\widgets\GridView::widget([
    'id' => "$reloadDiv-view-grid",
    'panelBtn' => \backend\modules\ezforms2\classes\BtnBuilder::btn()
            ->ezf_id($ezf_name_id)
            ->target($type_id)
            ->reloadDiv($reloadDiv)
            ->label('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('ec', 'Add Sub'))
            ->buildBtnAdd(),
    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
    'columns' => $columns,
]);
?>

<?php
$subModal = 'modal-sub-' . $reloadDiv;
$submodal = '<div id="' . $subModal . '" class="fade modal" role="dialog"><div class="modal-dialog modal-xs"><div class="modal-content"></div></div></div>';
?>
<!--<div id="ezf-modal-box2"></div>-->
<?php
$this->registerJs("
var hasMyModal = $( 'body' ).has( '#$subModal' ).length;
if(!hasMyModal){
    $('#ezf-modal-box').append('$submodal');
}
$('#$subModal').on('hidden.bs.modal', function(e){
    $('#$subModal .modal-content').html('');
 });

$('#$reloadDiv').attr('data-url',$('#$reloadDiv').attr('data-url')+'&type_id='+'$type_id');

$('#$reloadDiv-view-grid tbody tr td a').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    
    if(action === 'update' || action === 'create'){
        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$modal').modal('show')
        .find('.modal-content')
        .load(url);
    } else if(action === 'view') {
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

$('#$reloadDiv-view-grid').on('beforeFilter', function(e) {
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

$('#$reloadDiv-view-grid .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv-view-grid thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv-view-grid tbody tr').on('mouseover', function() {
    $(this).css('cursor','pointer');
});

$('#$reloadDiv-view-grid tbody tr').on('dblclick', function() {
    var url = $(this).children('td').children('.btnDetail').attr('href');
    $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$modal').modal('show')
        .find('.modal-content')
        .load(url);
});

$('#$reloadDiv .tabHeader').on('click',function(){
    var url = $(this).attr('data-url')+'&type_id='+$(this).attr('data-id');
    $('#$reloadDiv-view-grid').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    getUiAjax(url, '$reloadDiv');
    url = window.location;
    url = url.toString();
    if(url.indexOf('&type_id') > 0){
        url.indexOf('&type_id');
        url = url.substr(0, url.indexOf('&type_id'));
    }
    if(url.indexOf('&data_id') > 0){
        url.indexOf('&data_id');
        url = url.substr(0, url.indexOf('&data_id'));
    }
    window.history.replaceState({}, \"Payment And Cost\", url+'&type_id=3'+$(this).attr('data-id'));
    return false;
});

$('#$reloadDiv .tabHeaderLink').on('click',function(){
    var url = '" . Url::to(['/ezmodules/ezmodule/view?id=1522138126026776400']) . "';
    window.open(url, '_blank');
});


$('#$reloadDiv-view-grid .btnViewFile').on('click',function(){
    var url = $(this).prop('href');
    var target = $(this).prop('target');
    window.open(url, target);
});

$('#$reloadDiv-view-grid .btnViewAss').click(function(){
    var id = $(this).attr('data-id');
    var approve = $(this).attr('data-approve');
    var check = $(this).attr('data-check');
    var action = $(this).attr('data-action');
    var url = '/tmf/tmf/view-assign?data-id='+id+'&data-check='+check+'&data-approve='+approve+'&data-action='+action;
    viewAssign(url);
});

function viewAssign(url){
//    console.log($('#$subModal'));
    var hasMyModal = $( 'body' ).has( '#$subModal' ).length;
    if(!hasMyModal){
        $('#ezf-modal-box').append('$submodal');
    }
    $('#$subModal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#$subModal').modal('show')
    .find('.modal-content')
    .load(url);
}


function getUiAjax(url, divid) {
    $.ajax({
        method: 'GET',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+divid).html(result);
        }
    }).fail(function(err) {
                        err = JSON.parse(JSON.stringify(err))['responseText'];
                        $('#'+divid).html(`<div class='alert alert-danger'>`+err+`</div>`);
                   });
}

$('#$modal').on('hidden.bs.modal', function () {
//    $('#$subModal').remove();
//    var urlreload =  $('#$reloadDiv').attr('data-url');
//    $('#$reloadDiv').html('<center>Loading...</center>');        
//    getUiAjax(urlreload, '$reloadDiv');
//    window.location.reload();
});
//
//$('#$modal').on('shown.bs.modal', function () {
//   console.log(1);
//});

");




