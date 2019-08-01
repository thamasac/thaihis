<?php

use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];

$disabled = false;
if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
    $columns[] = [
        'class' => 'appxq\sdii\widgets\ActionColumn',
        'contentOptions' => ['style' => 'width:110px;min-width:110px;text-align: center;'],
        'template' => '{view} {update} {delete} ',
        'buttons' => [
            'view' => function ($url, $data, $key) use($options, $reloadDiv, $module_id) {
                //if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($options['budget_ezf_id'])
                                    ->reloadDiv($reloadDiv)
                                    ->modal('modal-budget-form')
                                    ->label('<i class="fa fa-eye"></i>')
                                    ->options(['class' => 'btn btn-default btn-xs'])
                                    ->buildBtnView($data['id']);
                    //}
                }
            },
            'update' => function ($url, $data, $key) use($options, $reloadDiv, $module_id) {
                //if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($options['budget_ezf_id'])
                                    ->reloadDiv($reloadDiv)
                                    ->modal('modal-budget-form')
                                    ->label('<i class="fa fa-pencil"></i>')
                                    ->options(['class' => 'btn btn-primary btn-xs'])
                                    ->buildBtnEdit($data['id']);
                    //}
                }
            },
            'delete' => function ($url, $data, $key) use($options, $reloadDiv, $module_id) {
                //if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($options['budget_ezf_id'])
                                    ->reloadDiv($reloadDiv)
                                    ->label('<i class="fa fa-trash"></i>')
                                    ->options(['class' => 'btn btn-danger btn-xs'])
                                    ->buildBtnDelete($data['id']);
                    //}
                }
            },
        ],
    ];
}

foreach ($options['budget_fields'] as $value){
    $field = EzfQuery::getFieldByName($options['budget_ezf_id'], $value);
    $columns[] = [
        'attribute' => $value,
        'header' => $field['ezf_field_label'],
        'format' => 'raw',
        'value' => function ($data) use($value,$field,$options) {
            if($field['ezf_field_type']=='80'){
                $ezf_ref=$field['ref_ezf_id'];
                $field_ref = \appxq\sdii\utils\SDUtility::string2Array($field['ref_field_desc']);
                $ref_form = EzfQuery::getEzformOne($ezf_ref);
                $dat = backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($ref_form,['id'=>$data[$value]],'one');

                return $dat[$field_ref[0]];
            }elseif($field['ezf_field_type']=='907'){
                $dataItems = [];
                $field_data = \appxq\sdii\utils\SDUtility::string2Array($field['ezf_field_data']);
                $dataItems = backend\modules\subjects\classes\SubjectManagementQuery::getGroupScheduleByWidget($field_data['widget']);
                $group=[];
                foreach ($dataItems as $val){
                    $group[$val['id']]=$val['group_name'];
                }
                
                return empty($group[$data[$value]])?'':$group[$data[$value]];
            }else{
                $result = "";
                if(!empty($data[$value])){
                    if(is_numeric($data[$value]))
                        $result = number_format ($data[$value]);
                    else
                        $result = $data[$value];
                }
                return $result;
            }
            
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: right;'],
    ];
}
\yii\widgets\Pjax::begin();
echo yii\grid\GridView::widget([
    'id' => "$reloadDiv-subject-grid",
    'dataProvider' => $dataProvider,
    'filterModel' => isset($searchModel) ? $searchModel : null,
    'columns' => $columns,
]);
\yii\widgets\Pjax::end();