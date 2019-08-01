<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezmodules\classes\ModuleQuery;

$userId = Yii::$app->user->id;
$ezm_builder = explode(',', $modelModule->ezm_builder);

$columns = [
    [
        'class' => 'yii\grid\CheckboxColumn',
        'checkboxOptions' => [
            'class' => 'selectionDataIds'
        ],
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:40px;text-align: center;'],
    ],
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:60px;text-align: center;'],
    ],
];

$columns[] = [
    'attribute' => 'create_date',
    'label' => Yii::t('ezmodule', 'Created At'),
    'value' => function ($data) {
        return !empty($data['create_date']) ? \appxq\sdii\utils\SDdate::mysql2phpDate($data['create_date'],'-') : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    'filter' => \kartik\daterange\DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'create_date',
                        'convertFormat'=>true,
                        //'useWithAddon'=>true,
                        'options'=>['id'=>'dr_'.$reloadDiv.'_m'.$module, 'class'=>'form-control'],
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

$modelFormFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id', [':ezf_id' => $ezform->ezf_id])
        ->orderBy(['ezf_field_order' => SORT_ASC])
        ->all();

$modelFormParentFields = NULL;
if(isset($ezformParent['ezf_id'])){
    $modelFormParentFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id', [':ezf_id' => $ezformParent['ezf_id']])
        ->orderBy(['ezf_field_order' => SORT_ASC])
        ->all();
}

if(!isset(Yii::$app->session['ezf_input'])){
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}

if(isset($modelFields) && !empty($modelFields)){ //fields
    foreach ($modelFields as $field) {
        $fieldOptions = SDUtility::string2Array($field['options']);
        $width = isset($fieldOptions['width'])?$fieldOptions['width']:110;
        $align = isset($fieldOptions['align'])?$fieldOptions['align']:'left';
        
        $currentForm = [$ezform['ezf_id']];
        if(isset($ezformParent['ezf_id'])){
            $currentForm[] = $ezformParent['ezf_id'];
        }

        $parentForm = 0;
        $field_name = $field['ezf_field_name'];
        if(in_array($field['ezf_id'], $currentForm)){
            if(isset($ezformParent['ezf_id']) && $ezformParent['ezf_id']==$field['ezf_id']){
                $field_name = 'fparent_'.$field['ezf_field_name'];
                $parentForm = 1;
            } else {
                $field_name = $field['ezf_field_name'];
                $parentForm = 0;
            }
        }
 
        $btn_manager = '';
        if(($field['field_default']==1 && ($userId == $modelModule->created_by || in_array($userId, $ezm_builder))) || $userId == $field['created_by'] ){
            $btn_manager = Html::button('<i class="glyphicon glyphicon-edit"></i>', [
			    'class' => 'btn btn-link btn-xs modal-updatebtn-fields',
                            'data-modal'=>'modal-ezform-main',
			    'data-url'=>Url::to(['/ezmodules/ezmodule-fields/update', 'id'=>$field['field_id'] ,
                                'ezf_id'=>$modelModule['ezf_id'],
                                'reloadDiv' => $reloadDiv, 
                                'module' => $module,
                                'addon'=>$addon,
                            ]),    
			]) . 
                        Html::button('<i class="glyphicon glyphicon-trash"></i>', [
			    'class' => 'btn btn-link btn-xs modal-delbtn-fields',
                            'data-modal'=>'modal-ezform-main',
			    'data-url'=>Url::to(['/ezmodules/ezmodule-fields/delete', 'id'=>$field['field_id'],
                                'ezf_id'=>$modelModule['ezf_id'],
                                'reloadDiv' => $reloadDiv, 
                                'module' => $module,
                                'addon'=>$addon,
                            ]), 
                            //'style'=>'color: #d9534f;',
			]);
        } 
            
        $itemsFormFields = $modelFormFields;
        if($parentForm){
            $itemsFormFields = $modelFormParentFields;
        }
        $fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($itemsFormFields, $ezform->ezf_version);
        
        $changeField = TRUE;
        foreach ($fieldsGroup as $key => $value) {
            
            if ($field['ezf_field_name'] == $value['ezf_field_name']) {
                
                if($parentForm){
                    $value['ezf_field_name'] = $field_name;
                }
                
                $dataInput;
                $ezf_input;
                if (isset(Yii::$app->session['ezf_input'])) {
                    $ezf_input = Yii::$app->session['ezf_input'];
                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                }

                $sort = Yii::$app->request->get('sort', '');
                $sortClass = '';
                if($sort!='' && $field_name == str_replace('-', '', $sort)){
                    if($sort!=$field_name){
                        $sort = $field_name;
                        $sortClass = 'desc';
                    } else {
                        $sort = '-'.$field_name;
                        $sortClass = 'asc';
                    }
                } else {
                    $sort = $field_name;
                }
                
                $currentUrl = Url::current([
                    'sort' => $sort,
                ]);
                
                $htmlFilter = ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $field_name);
                
                $colTmp = [
                    'attribute' => $field_name,
                    'label' => $field['field_name'],
                    'header'=> Html::a($field['field_name'], $currentUrl, ['class'=>$sortClass, 'data-sort'=>$sort]) .' '.
                        Html::button('<i class="glyphicon glyphicon-info-sign"></i>', [
			    'class' => 'btn btn-link btn-xs ezform-main-open',
                            'data-modal'=>'modal-ezform-main',
			    'data-url'=>Url::to(['/ezforms2/ezform-data/ezform-annotated', 'ezf_id'=>$field['ezf_id'],
                                'modal'=>'modal-ezform-main',
                                'reloadDiv'=>$reloadDiv,
                            ]),    
			]) . $btn_manager,
                    'format'=>'raw',
                    'value' => function ($data) use($dataInput, $value, $itemsFormFields, $field_name, $ezf_input) {
                        foreach ($itemsFormFields as $key => $field) {
                            $var = $field['ezf_field_name'];
                            $version = $field['ezf_version'];
                            if($field_name == $var && ($data['ezf_version'] == $version || $version=='all')){
                                if ($ezf_input) {
                                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                                }
                                return backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data);
                            }
                        }
                        return NULL;
                    },
                    'headerOptions' => ['style' => "text-align: {$align};"],
                    'contentOptions' => ['style' => "min-width:{$width}px; text-align: {$align};"],
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
            $columns[] = [
                'attribute' => $field_name,
                'label' => $field['field_name'],
                'headerOptions' => ['style' => "text-align: {$align};"],
                'contentOptions' => ['style' => "min-width:{$width}px; text-align: {$align};"],
            ];
        }
        
    }
}

//form fix
$parentForm = $ezform['ezf_id'];
if(isset($ezformParent['ezf_id'])){
    $parentForm = $ezformParent['ezf_id'];
}

$columns[] = [
    'header' => Yii::t('ezform', 'Parent Form').
                Html::button('<i class="glyphicon glyphicon-info-sign"></i>', [
                    'class' => 'btn btn-link btn-xs ezform-main-open',
                    'data-modal'=>'modal-ezform-main',
                    'data-url'=>Url::to(['/ezforms2/ezform-data/ezform-annotated', 'ezf_id'=>$parentForm,
                        'modal'=>'modal-ezform-main',
                        'reloadDiv'=>$reloadDiv,
                    ]),    
                ]),
    'value' => function ($data) use($ezform, $ezformParent, $reloadDiv) {
        $rstat = $data['rstat'];
        $ezf_id = $ezform['ezf_id'];
        $dataid = $data['id'];
        if(isset($data['fparent_rstat'])){
            $rstat = $data['fparent_rstat'];
            $ezf_id = $ezformParent['ezf_id'];
            $dataid = $data['fparent_id'];
        }
        $icon = ModuleFunc::getStatusIcon($rstat);
       
        return Html::a($icon, NULL, [
                        'class' => 'ezform-main-open',
                        'data-modal'=>'modal-ezform-main',
                        'data-url'=>Url::to(['/ezforms2/ezform-data/ezform',
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modal' => 'modal-ezform-main',
                            'reloadDiv' => $reloadDiv,
                        ]),
                        'style'=>'cursor: pointer;',
                    ]);
    },
    'format'=>'raw',        
    'headerOptions' => ['style' => "text-align: center;"],
    'contentOptions' => ['style' => "min-width:90px; text-align: center;"],
];
    //form
    $moduleId = $module;
    if($addon>0){
        $moduleId = $addon;
    }
    
    $modelForms = ModuleQuery::getFormsList($moduleId, $userId);
    if(isset($modelForms) && !empty($modelForms)){
        foreach ($modelForms as $key => $form) {
            $btn_manager_forms = '';
            if(($form['form_default']==1 && ($userId == $modelModule->created_by || in_array($userId, $ezm_builder))) || $userId == $form['created_by'] ){
                $btn_manager_forms = Html::button('<i class="glyphicon glyphicon-edit"></i>', [
                                'class' => 'btn btn-link btn-xs modal-updatebtn-forms',
                                'data-modal'=>'modal-ezform-main',
                                'data-url'=>Url::to(['/ezmodules/ezmodule-forms/update', 'id'=>$form['form_id'] ,
                                    'ezf_id'=>$modelModule['ezf_id'],
                                    'reloadDiv' => $reloadDiv, 
                                    'module' => $module,
                                    'addon'=>$addon,
                                ]),    
                            ]) . 
                            Html::button('<i class="glyphicon glyphicon-trash"></i>', [
                                'class' => 'btn btn-link btn-xs modal-delbtn-forms',
                                'data-modal'=>'modal-ezform-main',
                                'data-url'=>Url::to(['/ezmodules/ezmodule-forms/delete', 'id'=>$form['form_id'],
                                    'ezf_id'=>$modelModule['ezf_id'],
                                    'reloadDiv' => $reloadDiv, 
                                    'module' => $module,
                                    'addon'=>$addon,
                                ]), 
                                //'style'=>'color: #d9534f;',
                            ]);
            }
            
            $columns[] = [
                'header' => $form['form_name'].
                            Html::button('<i class="glyphicon glyphicon-info-sign"></i>', [
                                'class' => 'btn btn-link btn-xs ezform-main-open',
                                'data-modal'=>'modal-ezform-main',
                                'data-url'=>Url::to(['/ezforms2/ezform-data/ezform-annotated', 'ezf_id'=>$form['ezf_id'],
                                    'modal'=>'modal-ezform-main',
                                    'reloadDiv'=>$reloadDiv,
                                ]),    
                            ]) . $btn_manager_forms,
                'value' => function ($data) use($form, $ezformParent, $key, $specialField) {
                    $reloadMyDiv = "ezf{$form['ezf_id']}_C{$key}_R{$data['target']}";
                    $special = 0;
                    if(isset($specialField) && !empty($specialField)){
                        $special = 1;
                    }
                    $parent_ezf_id = 0;
                    if(isset($ezformParent['ezf_id'])){
                        $parent_ezf_id = $ezformParent['ezf_id'];
                    }
                    return ModuleFunc::formsItemWidget(
                            $form['ezf_id'],
                            $parent_ezf_id,
                            $special, 
                            $reloadMyDiv, 
                            backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($data->attributes), 
                            base64_encode($form['options']), 'modal-ezmodule-emr'
                            );
                },
                'format'=>'raw',        
                'contentOptions' => ['style' => "min-width:220px;"],
            ];
        }
    }
    
?>

    <?php
try {
    echo yii\grid\GridView::widget([
        'id' => "$reloadDiv-emr-grid",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}{pager}{items}{pager}',
        'columns' => $columns,
    ]);
} catch (\yii\db\Exception $e) {
    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
    echo $this->renderAjax('_error', [
                'msg' => $e->getMessage(),
    ]);
}
?>

<?php
        
echo Html::hiddenInput('total-module', Yii::t('ezmodule', '{count}', ['form'=>$ezform['ezf_name'], 'count'=>number_format($totalModule)]), ['id'=>'total-module']);
//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';

$this->registerJs("
$('.total-data-module').html($('#total-module').val());

$('#$reloadDiv-emr-grid tbody tr td a').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    
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
        return false;
    }
    
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