<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfQuery;

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];

$modelFields = EzfQuery::getFieldAllVersion($ezform->ezf_id);
$fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform->ezf_version);

if(!isset(Yii::$app->session['ezf_input'])){
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}

$columns[] = [
    'attribute' => 'id',
    'label' => 'Link ID',
    'contentOptions' => ['style' => 'width:100px;'],
];
    
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
            
            $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
            
            $colTmp = [
                'attribute' => $var,
                'label' => $label,
                'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
                    foreach ($modelFields as $key => $field) {
                        $var = $field['ezf_field_name'];
                        $version = $field['ezf_version'];
                        if($fieldName == $var && ($data['ezf_version'] == $version || $version=='all')){
                            if ($ezf_input) {
                                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                            }
                            return backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data);
                        }
                    }
                    return NULL;
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
if ($default_column) {

$columns[] = [
    'attribute' => 'userby',
    'label' => 'Key Operator 1',
    'value' => function ($data) use($ezform, $reloadDiv, $modal) {
        return $data['userby'].' '.backend\modules\ezforms2\classes\EzfHelper::btn($ezform->ezf_id)->modal($modal)->reloadDiv($reloadDiv)->options(['class'=>'btn btn-warning btn-xs'])->label('<i class="glyphicon glyphicon-eye-open"></i>')->buildBtnView($data['id']);
    },
     'format'=>"raw",
    'contentOptions' => ['style' => 'width:200px;'],
    'filter' => '',
];
$columns[] = [
    'attribute' => 'create_date',
    'label' => 'Enty Date 1',
    'value' => function ($data) {
        return !empty($data['create_date']) ? \appxq\sdii\utils\SDdate::mysql2phpDateTime($data['create_date']) : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
    'filter' => \kartik\daterange\DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'create_date',
                        'convertFormat'=>true,
                        //'useWithAddon'=>true,
                        'options'=>['id'=>'dr_create_date_'.$reloadDiv.'_'.$modal, 'class'=>'form-control'],
                        'pluginOptions'=>[
                            'locale'=>[
                                'format'=>'d-m-Y',
                                'separator'=>' to ',
                                //'language'=>'TH',
                            ],
                            //'opens'=>'left'
                        ]
                    ]),
];
    $columns[] = [
    'label' => 'Key Operator 2',
    'value' => function ($data) use($ezform, $reloadDiv, $modal) {
        return $data['userby2'].' '.backend\modules\ezforms2\classes\EzfHelper::btn($ezform->ezf_id)->modal($modal)->reloadDiv($reloadDiv)->db2()->options(['class'=>'btn btn-warning btn-xs'])->label('<i class="glyphicon glyphicon-eye-open"></i>')->buildBtnView($data['id2']);
    },  
     'format'=>"raw",       
    'contentOptions' => ['style' => 'width:200px;'],
    'filter' => '',
];
$columns[] = [
    'attribute' => 'create_date2',
    'label' => 'Enty Date 2',
    'value' => function ($data) {
        return !empty($data['create_date2']) ? \appxq\sdii\utils\SDdate::mysql2phpDateTime($data['create_date2']) : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
    'filter' => \kartik\daterange\DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'create_date2',
                        'convertFormat'=>true,
                        //'useWithAddon'=>true,
                        'options'=>['id'=>'dr_create_date2_'.$reloadDiv.'_'.$modal, 'class'=>'form-control'],
                        'pluginOptions'=>[
                            'locale'=>[
                                'format'=>'d-m-Y',
                                'separator'=>' to ',
                                //'language'=>'TH',
                            ],
                            //'opens'=>'left'
                        ]
                    ]),
];
   
$columns[] = [
    'header' => 'Comparison Results',
    'value' => function ($data) use($ezform, $reloadDiv, $modal) {
        if($data['rstat']==2 && $data['rstat2']==2){
            //if($data['user_update'] != $data['user_update2']){
                $fieldsList = EzfQuery::getFieldsList($ezform->ezf_id);
                if(isset($fieldsList) && !empty($fieldsList)){
                    foreach ($fieldsList as $keyF => $valueF) {
                        if($data[$valueF['ezf_field_name']] != $data[$valueF['ezf_field_name'].'2']){
                            return Html::button('Mismatched', ['class'=>'btn btn-primary btn-xs btn-compare', 'data-url' => Url::to(['/ezforms2/ezform-data/compare-fields', 'ezf_id'=>$ezform->ezf_id, 'dataid'=>$data['id']]), 'data-id' => $data['id'], 'data-ezf_id' => $ezform->ezf_id]);
                        }
                    }
                    return 'Matched';
                }
//            } else {
//                return 'Same Operator';
//            }
        } else {
            return 'Waiting';
        }
        
    },  
    'format'=>"raw",       
    'contentOptions' => ['style' => 'width:200px;'],
    'filter' => '',
];
}
?>
<?php
        $btnAdd = '';
        if($addbtn && $db2==0){
            $btnAdd = \backend\modules\ezforms2\classes\EzfHelper::btn($ezform->ezf_id)
                    ->target($target)
                    ->reloadDiv($reloadDiv)
                    ->modal($modal)
                    ->options([
                        'class'=>'btn btn-success btn-sm btn-auth-create',
                    ])->buildBtnAdd();
        }
        ?>
<?=
\appxq\sdii\widgets\EzGridView::widget([
    'id' => "$reloadDiv-emr-grid",
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'panelBtn' => $btnAdd,
    'title' => $title,
    'columns' => $columns,
]);
?>
<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-compare',
    'size' => 'modal-lg',
    'tabindexEnable' => FALSE,
]);
?>
<?php

//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';

$this->registerJs("
$('.btn-compare').click(function(){
    var url = $(this).attr('data-url');
   
    modalCompare(url);
});

function modalCompare(url) {
    $('#modal-compare .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-compare').modal('show')
    .find('.modal-content')
    .load(url);
}

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

");
?>