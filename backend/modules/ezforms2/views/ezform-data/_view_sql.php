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
            'actions' => function ($url, $data, $key) use($reloadDiv, $modal, $actions, $title) {
                    $html = '';
                    $path = backend\modules\ezforms2\classes\EzfFunc::array2PathTemplate($data);
                    $user = Yii::$app->user->identity->profile;
                    
                    $path['{reloadDiv}'] = $reloadDiv;
                    $path['{modal}'] = $modal;
                    $path['{sitecode}'] = $user->sitecode;
                    $path['{department}'] = $user->department;
                    $path['{user}'] = $user->user_id;
                    $path['{title}'] = $title;
                    
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

//if ($default_column) {
//$m = 'moment()';    
//$columns[] = [
//    'attribute' => 'create_date',
//    'label' => Yii::t('ezform', 'Created At'),
//    'value' => function ($data) {
//        return !empty($data['create_date']) ? \appxq\sdii\utils\SDdate::mysql2phpDateTime($data['create_date']) : '';
//    },
//    'headerOptions' => ['style' => 'text-align: center;'],
//    'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
//    'filter' => \kartik\daterange\DateRangePicker::widget([
//                        'model'=>$searchModel,
//                        'attribute'=>'create_date',
//                        'convertFormat'=>true,
//                        //'useWithAddon'=>true,
//                        //'presetDropdown'=>TRUE,
//                        'options'=>['id'=>'dr_'.$reloadDiv.'_'.$modal, 'class'=>'form-control'],
//                        'pluginOptions'=>[
//                            'locale'=>[
//                                'format'=>'d-m-Y',
//                                'separator'=>' to ',
//                                //'language'=>'TH',
//                            ],
//                            'alwaysShowCalendars'=>true,
//                            'autoUpdateInput'=>FALSE,
//                            'ranges'=>[
//                                Yii::t('kvdrp', 'Today') => ["{$m}.startOf('day')", $m],
//                                Yii::t('kvdrp', 'Yesterday') => [
//                                    "{$m}.startOf('day').subtract(1,'days')",
//                                    "{$m}.endOf('day').subtract(1,'days')",
//                                ],
//                                Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["{$m}.startOf('day').subtract(6, 'days')", $m],
//                                Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["{$m}.startOf('day').subtract(29, 'days')", $m],
//                                Yii::t('kvdrp', 'This Month') => ["{$m}.startOf('month')", "{$m}.endOf('month')"],
//                                Yii::t('kvdrp', 'Last Month') => [
//                                    "{$m}.subtract(1, 'month').startOf('month')",
//                                    "{$m}.subtract(1, 'month').endOf('month')",
//                                ],
//                            ],
//                            'autoApply'=>true,                
//                            //'opens'=>'left'
//                        ]
//                    ]),
//];
//}

if(isset($data_column) && !empty($data_column)){
    foreach ($data_column as $key_field => $value_field) {
        $var = $value_field;
        
        $width_style = '';
        if(isset($header[$var]['width']) && $header[$var]['width']!=''){
            $width_style = "min-width: {$header[$var]['width']}px; width: {$header[$var]['width']}px;";
        }
        $align_style = '';
        if(isset($header[$var]['align']) && $header[$var]['align']!=''){
            $align_style = "text-align: {$header[$var]['align']};";
        }
        $colTmp = [
                'attribute' => $value_field ,
                'label' => isset($header[$var]['label']) && $header[$var]['label']!=''?$header[$var]['label']:$value_field,
                'encodeLabel'=>false,
                'format' => 'raw',
                'value' => function ($data) use ($value_field) {
                    return $data[$value_field];
                },
                'contentOptions' => ['style' => "$width_style $align_style"],       
                //'filter' => Html::textInput($value_field, isset($get_params[$value_field])?$get_params[$value_field]:'', ['class'=>'form-control']),   
        ];
                
                if(isset($find_params["{{$value_field}}"])){
                    $colTmp['filter'] = Html::textInput($value_field, isset($get_params[$value_field])?$get_params[$value_field]:'', ['class'=>'form-control']);
                }
                
                $columns[] = $colTmp;
    }
}

?>

<?=

\appxq\sdii\widgets\EzGridView::widget([
    'id' => "$reloadDiv-sql-grid",
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'panelBtn' => '',
    'title' => $title,
    'theme' => $theme,
    'columns' => $columns,
]);
?>

<?php

//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
$jsAddon = '';
//$options = appxq\sdii\utils\SDUtility::string2Array($ezform->ezf_options);
//$enable_after_save = isset($options['after_save']['enable'])?$options['after_save']['enable']:0;
//if($enable_after_save){
//    if(isset($options['after_delete']['js']) && $options['after_delete']['js']!=''){
//        $jsAddon .= $options['after_delete']['js'];
//    }
//}

$this->registerJs("
    
$('#$reloadDiv-sql-grid tbody tr').on('dblclick', function() {
    let id = $(this).attr('data-key');
    $(this).find('.btn-update').click();
});

$('#$reloadDiv-sql-grid .btn-action').on('click', function() {
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

$('#$reloadDiv-sql-grid tbody tr td a').on('click', function() {
    
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

$('#$reloadDiv-sql-grid').on('beforeFilter', function(e) {
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

$('#$reloadDiv-sql-grid .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv-sql-grid thead tr th a').on('click', function() {
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