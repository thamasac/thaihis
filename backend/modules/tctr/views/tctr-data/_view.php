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
if (!$disabled) {
    $columns[] = [
        'class' => 'appxq\sdii\widgets\ActionColumn',
        'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
        'template' => '{view} {update} {delete} ',
        'buttons' => [
            'view' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal, $db2) {
                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['user_create']) && $db2==0) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/tctr/tctr-data/ezform-view',
                                        'ezf_id' => $ezform->ezf_id,
                                        'dataid' => $data['id'],
                                        'modal' => $modal,
                                        'reloadDiv' => $reloadDiv,
                                    ]), [
                                'data-action' => 'update',
                                'title' => Yii::t('yii', 'View'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => 'btn btn-default btn-xs',
                    ]);
                }
            },
            'update' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal, $db2) {
                if (!isset($data['user_create']) || backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
                    $color = ($db2==1 && !isset($data['id']))?'success':'primary';
                    $icon = $db2==1?'duplicate':'pencil';
                    return Html::a('<span class="glyphicon glyphicon-'.$icon.'"></span>', Url::to(['/ezforms2/ezform-data/ezform',
                                        'ezf_id' => $ezform->ezf_id,
                                        'dataid' => $db2==1?$data['id_ref']:$data['id'],
                                        'modal' => $modal,
                                        'reloadDiv' => $reloadDiv,
                                        'db2' => $db2,
                                    ]), [
                                'data-action' => 'update',
                                'title' => Yii::t('yii', 'Update'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => "btn btn-$color btn-xs",
                    ]);
                }
            },
            'delete' => function ($url, $data, $key) use($ezform, $reloadDiv, $db2) {
                if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($ezform, Yii::$app->user->id, $data['user_create']) && $db2==0) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/ezform-data/delete',
                                        'ezf_id' => $ezform->ezf_id,
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

$modelFields = EzfQuery::getFieldAllVersion($ezform->ezf_id);
$fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform->ezf_version);

if(!isset(Yii::$app->session['ezf_input'])){
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
    'attribute' => 'update_date',
    'label' => Yii::t('ezform', 'Updated'),
    'value' => function ($data) {
        return !empty($data['update_date']) ? \appxq\sdii\utils\SDdate::mysql2phpDateTime($data['update_date']) : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
    'filter' => \kartik\daterange\DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'create_date',
                        'convertFormat'=>true,
                        //'useWithAddon'=>true,
                        'options'=>['id'=>'dr_'.$reloadDiv.'_'.$modal, 'class'=>'form-control'],
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
}

?>

<?=

yii\grid\GridView::widget([
    'id' => "$reloadDiv-emr-grid",
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
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

");
?>