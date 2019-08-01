<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;

$targetField = EzfQuery::getTargetOne($modelEzf->ezf_id);
$list_ezf_id = $ezf_id;
if($targetField){
    $list_ezf_id = $targetField->parent_ezf_id;
}
$ezformAll = backend\modules\ezforms2\classes\EzfQuery::getEzformList($list_ezf_id);
//$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
//            ->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])
//            ->orderBy(['ezf_field_order' => SORT_ASC])
//            ->all();

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:60px;min-width:60px;text-align: center;'],
    ],
];
//if (!$disabled) {
//    $columns[] = [
//        'class' => 'appxq\sdii\widgets\ActionColumn',
//        'contentOptions' => ['style' => 'width:110px;min-width:110px;text-align: center;'],
//        'template' => '{view} {update} {delete} ',
//        'buttons' => [
//            'view' => function ($url, $data, $key) use($ezf_id, $reloadDiv, $modal, $modelEzf) {
//                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($modelEzf, Yii::$app->user->id, $data['user_create'])) {
//                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/ezform-view',
//                                        'ezf_id' => $ezf_id,
//                                        'dataid' => $data['data_id'],
//                                        'modal' => $modal,
//                                        'reloadDiv' => $reloadDiv,
//                                    ]), [
//                                'data-action' => 'update',
//                                'title' => Yii::t('yii', 'View'),
//                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                'class' => 'btn btn-default btn-xs btn-auth-view',
//                    ]);
//                }
//            },
//            'update' => function ($url, $data, $key) use($ezf_id, $reloadDiv, $modal, $modelEzf) {
//                if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($modelEzf, Yii::$app->user->id, $data['user_create'])) {
//                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/ezform-data/ezform',
//                                        'ezf_id' => $ezf_id,
//                                        'dataid' => $data['data_id'],
//                                        'modal' => $modal,
//                                        'reloadDiv' => $reloadDiv,
//                                    ]), [
//                                'data-action' => 'update',
//                                'title' => Yii::t('yii', 'Update'),
//                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                'class' => 'btn btn-primary btn-xs btn-auth-update',
//                    ]);
//                }
//            },
//            'delete' => function ($url, $data, $key) use($ezf_id, $reloadDiv, $modelEzf) {
//                if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($modelEzf, Yii::$app->user->id, $data['user_create'])) {
//                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/ezform-data/delete',
//                                        'ezf_id' => $ezf_id,
//                                        'dataid' => $data['data_id'],
//                                        'reloadDiv' => $reloadDiv,
//                                    ]), [
//                                'data-action' => 'delete',
//                                'title' => Yii::t('yii', 'Delete'),
//                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
//                                'data-method' => 'post',
//                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                'class' => 'btn btn-danger btn-xs btn-auth-del',
//                    ]);
//                }
//            },
//        ],
//    ];
//}
$columns[] = [
    'attribute' => 'create_date',
    'value' => function ($data) {
        return !empty($data['create_date']) ? \appxq\sdii\utils\SDdate::mysql2phpDate($data['create_date'],'-') : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;min-width:100px;text-align: center;'],
    'filter' => \kartik\daterange\DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'create_date',
                        'convertFormat'=>true,
                        //'useWithAddon'=>true,
                        'options'=>['id'=>'drmainemr_'.$reloadDiv.'_'.$modal, 'class'=>'form-control'],
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
//$columns[] = [
//    'attribute' => 'ezf_id',
//    'value' => function ($data) {
//        return $data['ezf_name'];
//    },
//    'contentOptions' => ['style' => 'width:200px;'],
//    'filter' => Html::activeDropDownList($searchModel, 'ezf_id', yii\helpers\ArrayHelper ::map($ezformAll, 'ezf_id', 'ezf_name'), ['class' => 'form-control', 'prompt' => 'All']),
//];
$modelFields = EzfQuery::getFieldAllVersion($modelEzf->ezf_id);
//$fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $modelEzf->ezf_version);

if(!isset(Yii::$app->session['ezf_input'])){
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}

$columns[] = [
    'attribute' => 'ezf_detail',
    //'label' => Yii::t('ezform', 'Results'),
    'format' => 'raw',
    'value' => function ($data) use($reloadDiv, $modal, $disabled) {
        
        $detail = appxq\sdii\utils\SDUtility::string2Array($data['ezf_detail']);
        if (is_array($detail)) {
            try {
                $query = new \yii\db\Query();
                $query->select(['*']);
                $query->from($data['ezf_table']);
                $query->where('id=:id', [':id' => $data['data_id']]);

                $zdata = $query->createCommand()->queryOne();
            } catch (\yii\db\Exception $e) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                return NULL;
            }
                    //\appxq\sdii\utils\VarDumper::dump($detail);

            if($zdata){
                $ezf_id = isset($data['ezf_id'])?$data['ezf_id']:0;
                $modelEzf = EzfQuery::getEzformOne($ezf_id);
                    
                $actions = '';
                if (!$disabled) {
                    

                    if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($modelEzf, Yii::$app->user->id, $data['user_create'])) {
                        $actions .= Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/ezform-view',
                                            'ezf_id' => $ezf_id,
                                            'dataid' => $data['data_id'],
                                            'modal' => $modal,
                                            'reloadDiv' => $reloadDiv,
                                        ]), [
                                    'data-action' => 'update',
                                    'title' => Yii::t('yii', 'View'),
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-default btn-xs btn-auth-view',
                        ]).' ';
                    }
                    if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($modelEzf, Yii::$app->user->id, $data['user_create'])) {
                        $actions .= Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/ezform-data/ezform',
                                            'ezf_id' => $ezf_id,
                                            'dataid' => $data['data_id'],
                                            'modal' => $modal,
                                            'reloadDiv' => $reloadDiv,
                                        ]), [
                                    'data-action' => 'update',
                                    'title' => Yii::t('yii', 'Update'),
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-primary btn-xs btn-update btn-auth-update',
                        ]).' ';
                    }
                    if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($modelEzf, Yii::$app->user->id, $data['user_create'])) {
                        $actions .= Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/ezform-data/delete',
                                            'ezf_id' => $ezf_id,
                                            'dataid' => $data['data_id'],
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
                }
                $options = appxq\sdii\utils\SDUtility::string2Array($modelEzf->ezf_options);
                $html = '<div class="pull-right text-right">'.$actions.'</div>  <h4 class="page-header" style="margin-top: 0px;margin-bottom: 10px;">'.$data['ezf_name'].' <small> By <i class="glyphicon glyphicon-user"></i> '.$data['userby'].' '.(!empty($data['update_date']) ? '<i class="glyphicon glyphicon-calendar"></i> '.\appxq\sdii\utils\SDdate::mysql2phpDateTime($data['update_date'],'-') : '').'</small></h4>';
                $comma = '';
                $column = isset($options['display_col'])?$options['display_col']:0;
                $template = isset($options['display_tmp'])?$options['display_tmp']:'<span class="content_box"><b class="content_label">{label}: </b><span class="content_value">{value}</span> </span>';
                $row_count = 0;
                $field_count = count($detail);
                foreach ($detail as $index_field => $field) {
                    $modelFields = EzfQuery::getFieldAllVersion($data['ezf_id']);
                    $col = $column>0?12/$column:0;
                    $sdbox = '';
                    $row_count++;
                    
                   
                    if($col>0 && $row_count>1){
                        $sdbox = 'sdbox-col';
                    }
                    
                    $template_content = $template;
                    if($col>0) {
                        $template_content = "<div class=\"col-md-$col $sdbox\">{$template}</div>";
                    }

                    if($modelFields){
                        foreach ($modelFields as $key => $value) {
                            $var = $value['ezf_field_name'];
                            $version = $value['ezf_version'];
                            if($field == $var && ($zdata['ezf_version'] == $version || $version=='all')){
                                $dataInput;
                                if (Yii::$app->session['ezf_input']) {
                                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                                }
                                if(isset($zdata[$var]) && $zdata[$var]!=''){
                                    if($col>0 && $row_count==1){
                                        $html .= '<div class="row" >';
                                    }
                                    
                                    $html .= strtr($template_content, [
                                        '{label}' => $value['ezf_field_label'],
                                        '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $zdata),
                                    ]);
                                            //$comma . "<b>{$value['ezf_field_label']}</b>" . backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $zdata);
                                } else {
                                    $row_count--;
                                }
                                break;
                            }
                            $comma = ' ';
                        }
                    }
                    
                    if($col>0 && ($row_count==$column || $field_count==$index_field+1)){
                        $html .= '</div>';
                        $row_count = 0;
                    }
                }
                return $html;
            }
        }
        
        return 'Please select the `Displayed fields` that setting the EzForm.';
    },
    'filter' => Html::activeDropDownList($searchModel, 'ezf_id', yii\helpers\ArrayHelper ::map($ezformAll, 'ezf_id', 'ezf_name'), ['class' => 'form-control', 'prompt' => 'All']),
];
$columns[] = [
    'attribute' => 'xsourcex',
    'format' => 'raw',
    'value' => function ($data) {
        return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$data['sitename']}\">{$data['xsourcex']}</span>";
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
];

//$columns[] = [
//    'attribute' => 'userby',
//    'contentOptions' => ['style' => 'width:200px;'],
//    'filter' => '',
//];
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
?>

<ul class="nav nav-tabs" style="margin: 10px 0;">
   <li role="presentation" class="<?=$popup==2?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target, 'view'=>2])?>">Data Entry</a></li>
    <li role="presentation" class="<?= $popup == 0 ? 'active' : '' ?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id' => $ezf_id, 'target' => $target, 'view' => 0]) ?>">Data of All Forms</a></li>
    <li role="presentation" class="<?= $popup == 1 ? 'active' : '' ?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id' => $ezf_id, 'target' => $target, 'view' => 1]) ?>">List of All Forms</a></li>
    
  <?php if(isset($modelEzf->ezf_db2) && $modelEzf->ezf_db2==1):?>
  <li role="presentation" class="<?=$popup==3?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target, 'view'=>3])?>">Key Operator 2</a></li>
  <?php endif;?>
</ul>
    
<?php

if ($targetField) {
    ?>
            <?php echo Html::label(Yii::t('ezform', 'Target')) ?>
        <?php
        echo $this->render('_emr_target', [
            'ezf_id' => $ezf_id,
            'modelEzf' => $modelEzf,
            'modelFields' =>$modelFields,
            'model' => $searchModel,
            'targetField' => $targetField,
            'modal' => $modal,
            'reloadDiv' => $reloadDiv,
            'target' => $target,
            'showall' => $showall,
        ]);
        ?>
        <?php } ?>  

<?php
$title = backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($modelEzf, 20).' '.$modelEzf->ezf_name;
$btnAdd = '';
            $btnAdd = \backend\modules\ezforms2\classes\EzfHelper::btn($modelEzf->ezf_id)
                    ->target($target)
                    ->reloadDiv('view-emr-lists')
                    //->modal($modal)
                    ->options([
                        'class'=>'btn btn-success btn-sm btn-auth-create',
                    ])->buildBtnAdd();
?>
      <br/>
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
    


<?php
//$sub_modal = '<div id="modal-'.$ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
$jsAddon = 'switch(result.ezf_id) {';
foreach ($ezformAll as $key_form => $ezform) {
    $options = appxq\sdii\utils\SDUtility::string2Array($ezform['ezf_options']);
    $enable_after_save = isset($options['after_save']['enable'])?$options['after_save']['enable']:0;
    if($enable_after_save){
        if(isset($options['after_delete']['js']) && $options['after_delete']['js']!=''){
            $jsAddon .= "case '{$ezform['ezf_id']}': ".$options['after_delete']['js'].' break;';
        }
    }
}
$jsAddon .= '}';


$this->registerJs("

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
                                    $jsAddon
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