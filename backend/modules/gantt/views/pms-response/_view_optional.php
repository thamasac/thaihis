<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;

$item_ezform = ['1'=>'All'];
foreach ($dataEzForm as $key => $value){
    $item_ezform[$key] = $value['ezf_name'];
}

$filter_value = isset($filter_value)?$filter_value:'1';
$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:60px;min-width:60px;text-align: center;'],
    ],
];
$disabled = true;
if (!$disabled) {
    $columns[] = [
        'class' => 'appxq\sdii\widgets\ActionColumn',
        'contentOptions' => ['style' => 'width:110px;min-width:110px;text-align: center;'],
        'template' => '{view} {update} {delete} ',
        'buttons' => [
            'view' => function ($data) {
                
            },
            'update' => function ($data) {
                
            },
            'delete' => function ($data) {
                
            },
        ],
    ];
}
$columns[] = [
    'attribute' => 'create_date',
    'value' => function ($data) {
        return !empty($data['create_date']) ? \appxq\sdii\utils\SDdate::mysql2phpDate($data['create_date'], '-') : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;min-width:100px;text-align: center;'],
];
$columns[] = [
    'attribute' => 'ezf_name',
    'value' => function ($data) {
        return $data['ezf_name'];
    },
    'contentOptions' => ['style' => 'width:200px;'],
];

$columns[] = [
    'attribute' => 'ezf_detail',
    'header' => 'Ezform detail',
    'format' => 'raw',
    'value' => function ($data) {
        if (isset($data['field_detail'])) {
            $detail = appxq\sdii\utils\SDUtility::string2Array($data['field_detail']);
            if (is_array($detail)) {
                $zdata = $data;
                if ($zdata) {
                    $html = '';
                    $comma = '';
                    foreach ($detail as $field) {
                        $modelField = EzfQuery::getFieldByName($data['ezf_id'], $field);

                        $var = $modelField['ezf_field_name'];
                        $version = $modelField['ezf_version'];
                        if ($field == $var && ($zdata['ezf_version'] == $version || $version == 'all')) {
                            $dataInput;
                            if (Yii::$app->session['ezf_input']) {
                                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelField['ezf_field_type'], Yii::$app->session['ezf_input']);
                            }
                            $html .= $comma . backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelField, $zdata);
                            break;
                        }
                        $comma = ' ';
                    }
                    return $html;
                }
            }
        }

        return NULL;
    },
    'filter' => '',
];
$columns[] = [
    'attribute' => 'xsourcex',
    'format' => 'raw',
    'value' => function ($data) {
        if (isset($data['xsourcex']))
            return "<span class=\"label label-success\" data-toggle=\"tooltip\" >{$data['xsourcex']}</span>";
        else
            return null;
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
        if (isset($data['rstat'])) {
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
        } else {
            return null;
        }
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:120px;text-align: center;'],
        //'filter' => Html::activeDropDownList($searchModel, 'rstat', backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class' => 'form-control', 'prompt' => 'All']),
];
?>

<?php
$btnAdd = '';
$title = 'Optional form of task item';
if ($addbtn) {
    $btnAdd = Html::button("<i class='fa fa-plus'></i> Add new data", ['class' => 'btn btn-success', 'id' => 'add_optional_data']);
}
?>

<div id="modal-optional-list" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">Optional ezforms list</h5>
                <button type="button" class="close modal-optional-close pull-right" id="modal-optional-close" onclick="cloasModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <?php foreach ($dataEzForm as $key => $value): ?>
                    <div id="update_pmslink_optional<?= $value['ezf_id'] ?>" data-url="<?= yii\helpers\Url::to(['/gantt/pms-response/update-pmslink', 'taskid' => $task_dataid, 'ezf_id' => isset($value['ezf_id']) ? $value['ezf_id'] : null]) ?>"></div>
                    <?php
                    if (isset($value['ezf_id']))
                        echo "<div class='row form-group' style='margin-left:20px;'>";
                    echo EzfHelper::btn($value['ezf_id'])->label("<i class='fa fa-plus'></i> " . $value['ezf_name'])->reloadDiv('update_pmslink_optional' . (isset($value['ezf_id']) ? $value['ezf_id'] : null))->buildBtnAdd();
                    echo "</div>";
                endforeach;
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-optional-close" onclick="cloasModal()" >Close</button>
            </div>
        </div>
    </div>
</div>
<div  class="row form-group" style="margin-left: 10px;">
    <div  class="col-md-6">
        <?= Html::label('Ezform filter: ', $btnAdd)?>
        <?= Html::dropDownList('ezform_filter', $filter_value, $item_ezform, ['class' => 'form-control','id'=>'ezform_filter']) ?>
    </div>
    <div class="clearfix"></div>
</div>
<div id="content_optional_grid" style="margin: 10px;">
    <?=
    \appxq\sdii\widgets\EzGridView::widget([
        'id' => "$reloadDiv-emr-grid",
        'dataProvider' => $dataProvider,
        'panelBtn' => $btnAdd,
        'title' => $title,
        'columns' => $columns,
    ]);
    ?>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $(function () {
        var modal = $('#modal-optional-list');
        $(document).find('#ezf-main-app #modal-optional-list').remove();
        $(document).find('#ezf-main-app').append(modal);
    });

    function cloasModal() {
        $('#modal-optional-list').modal('hide');
        $('body').addClass('modal-open');
    }

    $('#add_optional_data').click(function () {
        $('#modal-optional-list').modal();
    });
    
    $('#ezform_filter').on('change',function(){
        var div = $(document).find('#content_optional_show');
        var url = div.attr('data-url');
        url=url+'&filter_value='+$(this).val();
        getUiAjax(url,'content_optional_show');
    });


</script>
<?php \richardfan\widget\JSRegister::end(); ?>

<?php
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