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
if (!$disabled) {
    $columns[] = [
        'class' => 'appxq\sdii\widgets\ActionColumn',
        'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
        'template' => '{view} {update} {delete} ',
        'buttons' => [
            'view' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal) {
                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/item/ezform-view',
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
            'update' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal) {
                if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/item/ezform',
                                        'ezf_id' => $ezform->ezf_id,
                                        'dataid' => $data['id'],
                                        'modal' => $modal,
                                        'reloadDiv' => $reloadDiv,
                                    ]), [
                                'data-action' => 'update',
                                'title' => Yii::t('yii', 'Update'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => 'btn btn-primary btn-xs',
                    ]);
                }
            },
            'delete' => function ($url, $data, $key) use($ezform, $reloadDiv) {
                if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/item/delete',
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
$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id', [':ezf_id' => $ezform->ezf_id])
        ->orderBy(['ezf_field_order' => SORT_ASC])
        ->all();

if(!isset(Yii::$app->session['ezf_input'])){
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}

foreach ($data_column as $field) {

    $fieldName = $field;
    if (is_array($field) && isset($field['attribute'])) {
        $fieldName = $field['attribute'];
    }

    $changeField = TRUE;
    foreach ($modelFields as $key => $value) {
        $var = $value['ezf_field_name'];
        $label = $value['ezf_field_label'];

        if ($fieldName == $var) {
            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            
            $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
            
            $colTmp = [
                'attribute' => $var,
                'label' => $label,
                'value' => function ($data) use($dataInput, $value) {
                    return backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $data);
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