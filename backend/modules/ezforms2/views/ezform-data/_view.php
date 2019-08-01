<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\SDUtility;

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
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/ezform-view',
                                        'ezf_id' => $ezform->ezf_id,
                                        'dataid' => $data['id'],
                                        'modal' => $modal,
                                        'reloadDiv' => $reloadDiv,
                                    ]), [
                                'data-action' => 'update',
                                'title' => Yii::t('yii', 'View'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => 'btn btn-default btn-xs btn-auth-view',
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
                                'class' => "btn btn-$color btn-xs btn-update btn-auth-update",
                                      
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
                                'class' => 'btn btn-danger btn-xs btn-auth-del',
                    ]);
                }
            },
        ],
    ];
}

if (isset($actions) && !empty($actions)) {
    $width_style = 'min-width:100px;width:100px;';
    if(isset($header['action']['width']) && $header['action']['width']!=''){
        $width_style = "min-width: {$header['action']['width']}px; width: {$header['action']['width']}px;";
    }
    $align_style = '';
    if(isset($header['action']['align']) && $header['action']['align']!=''){
        $align_style = "text-align: {$header['action']['align']};";
    }
            
    $columns[] = [
        'class' => 'appxq\sdii\widgets\ActionColumn',
        'contentOptions' => ['style' => "$width_style $align_style"],
        'template' => '{actions}',
        'buttons' => [
            'actions' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal, $db2, $actions) {
                    $html = '';
                    $path = backend\modules\ezforms2\classes\EzfFunc::array2PathTemplate($data);
                    $user = Yii::$app->user->identity->profile;
                    
                    $path['{ezf_id}'] = $ezform->ezf_id;
                    $path['{reloadDiv}'] = $reloadDiv;
                    $path['{modal}'] = $modal;
                    $path['{db2}'] = $db2;
                    $path['{sitecode}'] = $user->sitecode;
                    $path['{department}'] = $user->department;
                    $path['{user}'] = $user->user_id;
                    
                    foreach ($actions as $key_btn => $value_btn) {
                        if(isset($value_btn['action']) && !empty($value_btn['action'])){
                            $script = strtr($value_btn['cond'], $path);
                            $enable = true;
                            if(!empty($script)){
                                try {
                                    @eval("\$enable = ($script)?true:false;");
                                } catch (ParseError $e) {
                                    $enable = false;
                                }
                            }
                            
                            if($enable){
                                $html .= strtr($value_btn['action'], $path) . ' ';
                            }
                        }
                    }
                    return $html;
            },
        ],
    ];
}

if ($default_column) {
$m = 'moment()';    
$columns[] = [
    'attribute' => 'create_date',
    'label' => Yii::t('ezform', 'Created At'),
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
                        //'presetDropdown'=>TRUE,
                        'options'=>['id'=>'dr_'.$reloadDiv.'_'.$modal, 'class'=>'form-control'],
                        'pluginOptions'=>[
                            'locale'=>[
                                'format'=>'d-m-Y',
                                'separator'=>' to ',
                                //'language'=>'TH',
                            ],
                            'alwaysShowCalendars'=>true,
                            'autoUpdateInput'=>FALSE,
                            'ranges'=>[
                                Yii::t('kvdrp', 'Today') => ["{$m}.startOf('day')", $m],
                                Yii::t('kvdrp', 'Yesterday') => [
                                    "{$m}.startOf('day').subtract(1,'days')",
                                    "{$m}.endOf('day').subtract(1,'days')",
                                ],
                                Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["{$m}.startOf('day').subtract(6, 'days')", $m],
                                Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["{$m}.startOf('day').subtract(29, 'days')", $m],
                                Yii::t('kvdrp', 'This Month') => ["{$m}.startOf('month')", "{$m}.endOf('month')"],
                                Yii::t('kvdrp', 'Last Month') => [
                                    "{$m}.subtract(1, 'month').startOf('month')",
                                    "{$m}.subtract(1, 'month').endOf('month')",
                                ],
                            ],
                            'autoApply'=>true,                
                            //'opens'=>'left'
                        ]
                    ]),
];
}
$modelFieldsAll = EzfQuery::getFieldAllVersion($ezform->ezf_id);
$modelFields =  EzfQuery::getFieldAllVersion($ezform->ezf_id);

$fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFieldsAll, $ezform->ezf_version);

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
        if($varname==1){
            $label = $var;
        }
        
        if ($fieldName == $var) {
            $dataInput;
            $ezf_input;
            if (isset(Yii::$app->session['ezf_input'])) {
                $ezf_input = Yii::$app->session['ezf_input'];
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            
            $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
            
            $width_style = 'min-width:120px;';
            if(isset($header[$var]['width']) && $header[$var]['width']!=''){
                $width_style = "min-width: {$header[$var]['width']}px; width: {$header[$var]['width']}px;";
            }
            $align_style = '';
            if(isset($header[$var]['align']) && $header[$var]['align']!=''){
                $align_style = "text-align: {$header[$var]['align']};";
            }
            
            $colTmp = [
                'attribute' => $var ,
                'label' => isset($header[$var]['label']) && $header[$var]['label']!=''?$header[$var]['label']:$label,
                'encodeLabel'=>false,
                'format' => 'raw',
                'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input, $reloadDiv, $rawdata) {
                    if($rawdata==1){
                        return $data[$fieldName];
                    } else {
                        foreach ($modelFields as $key => $field) {
                            $var = $field['ezf_field_name'];
                            $version = $field['ezf_version'];
                            if($fieldName == $var && ($data['ezf_version'] == $version || $version=='all')){
                                if ($ezf_input) {
                                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                                }
                                return backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data, $reloadDiv);
                            }
                        }
                        return NULL;
                    }
                },
                'contentOptions' => ['style' => "$width_style $align_style"],        
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
    'attribute' => 'xsourcex',
    'label'=>Yii::t('ezform', 'Site Code'),
    'format' => 'raw',
    'value' => function ($data) {
        return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$data['sitename']}\">{$data['xsourcex']}</span>";
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
];
$columns[] = [
    'attribute' => 'userby',
    'label'=>Yii::t('ezform', 'Recorded By'),
    'contentOptions' => ['style' => 'width:200px;'],
    'filter' => '',
];
$columns[] = [
    'attribute' => 'rstat',
    'format' => 'raw',
    'value' => function ($data) {
        $alert = 'label-default';
        if(isset($data['rstat'])){
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
        }
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:120px;text-align: center;'],
    'filter' => Html::activeDropDownList($searchModel, 'rstat', backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class' => 'form-control', 'prompt' => 'All']),
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
        
//        $icon = '';
//        if(isset($ezform->ezf_icon) && !empty($ezform->ezf_icon)){
//            $icon = \backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($ezform);
//        }
        $title_label = $title;
        if($title=='{auto}'){
                $title_label = backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($ezform, 20).' '.$ezform->ezf_name;
        } 
        
        $grid_options = [
            'id' => "$reloadDiv-emr-grid",
            'dataProvider' => $dataProvider,
            'panelBtn' => $btnAdd,
            'title' => $title_label,
            'theme' => $theme,
            'columns' => $columns,
        ];
        
        if($filter==1){
            $grid_options['filterModel'] = $searchModel;
        }
        ?>
<?=

\appxq\sdii\widgets\EzGridView::widget($grid_options);
?>

<?php

//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';

$jsAddon = '';
$options = SDUtility::string2Array($ezform->ezf_options);
$enable_after_save = isset($options['after_save']['enable'])?$options['after_save']['enable']:0;
if($enable_after_save){
    if(isset($options['after_delete']['js']) && $options['after_delete']['js']!=''){
        $jsAddon .= $options['after_delete']['js'];
    }
}

$this->registerJs("
    
$('#$reloadDiv-emr-grid tbody tr').on('dblclick', function() {
    let id = $(this).attr('data-key');
    $(this).find('.btn-update').click();
});

$('#$reloadDiv-emr-grid .btn-action').on('click', function() {
    let url = $(this).attr('data-url');
    if(url){
        $.post(
            url
        ).done(function(result) {
            if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                let urlreload =  $('#$reloadDiv').attr('data-url');        
                getUiAjax(urlreload, '$reloadDiv');
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
            } 
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
            return false;
        });
    }
    return false;
});

$('#$reloadDiv-emr-grid tbody tr td a').on('click', function() {
    
    let url = $(this).attr('href');
    let action = $(this).attr('data-action');
    
    if(action === 'update' || action === 'create'){
        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$modal').modal('show')
        .find('.modal-content')
        .load(url);
        return false;
    } else if(action === 'delete') {
        yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
                $.post(
                        url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "'}
                ).done(function(result){
                        if(result.status == 'success'){
                                " . SDNoty::show('result.message', 'result.status') . "
                                    $jsAddon
                            let urlreload =  $('#$reloadDiv').attr('data-url');        
                            getUiAjax(urlreload, '$reloadDiv');        
                        } else {
                                " . SDNoty::show('result.message', 'result.status') . "
                        }
                }).fail(function(){
                        " . SDNoty::show("'" . "Server Error'", '"error"') . "
                        console.log('server error');
                });
        });
        return false;
    }
});

$('#$reloadDiv-emr-grid').on('beforeFilter', function(e) {
    let \$form = $(this).find('form');
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