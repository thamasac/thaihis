<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use yii\web\JsExpression;

?>

    <div class="ezform-fields-form">


        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('notify', 'Add Notify') ?></h4>
        </div>

        <div class="modal-body">
            <?php
            //            Html::label(Yii::t('notify', 'Please select form'))
            //        kartik\select2\Select2::widget([
            //            'id' => 'selectEzf',
            //            'name' => 'selectEzf',
            //            'options' => ['placeholder' => Yii::t('notify', 'Please select form')],
            ////            'data' => \yii\helpers\ArrayHelper::map($dataEzf, 'ezf_id', 'ezf_name'),
            //            'pluginOptions' => [
            //                'ajax' => [
            //                    'url' => '/notify/default/get-ezf',
            //                    'dataType' => 'json',
            //                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
            //                ],
            //                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            //                'templateResult' => new JsExpression('function(data) { return data.text; }'),
            //                'templateSelection' => new JsExpression('function (data) { return data.text; }'),
            //            ]
            //        ]);
            echo GridView::widget([
                    'id' => $reloadDiv.'-grid-ezf-notify',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'text-align: center;'],
                        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
                    ],
                    'ezf_name', [
                        'class' => 'appxq\sdii\widgets\ActionColumn',
                        'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
                        'template' => '{view}', //'{view} {update} {delete} ',
                        'buttons' => [
                            'view' => function ($url, $data, $key) use ($reloadDiv) {
//                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {

                                return Html::button('<span class="glyphicon glyphicon-plus"></span> ADD', [
                                    'title' => Yii::t('yii', 'Add'),
                                    'data-ezf-id' => $data['ezf_id'],
                                    'data-action' => 'add',
                                    'class' => 'btn btn-success btn-xs btn-add-notify',
                                ]);
                            },
                        ]
                    ]
                ]
            ]);
            ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"
                    aria-hidden="true"><?= Yii::t('app', 'Close') ?></button>
        </div>


    </div>

<?php $this->registerJs("
    
    $('.btn-add-notify').click(function(){
        var url = '/notify/default/create?ezf_id='+$(this).attr('data-ezf-id')+'&v=&modal=$reloadDiv-add-notify&reloadDiv=$reloadDiv';
        $('#$reloadDiv-add-notify .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$reloadDiv-add-notify').modal('show')
        .find('.modal-content')
        .load(url);
        return false;
    });
    
    $('#$reloadDiv-grid-ezf-notify').on('beforeFilter', function(e) {
        var \$form = $(this).find('form');
        $.ajax({
            method: 'GET',
            url: \$form.attr('action'),
                data: \$form.serialize(),
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#$reloadDiv-modal .modal-content').html(result);
            }
        });
        return false;
    });
    
    $('#$reloadDiv-grid-ezf-notify .pagination a').on('click', function() {
        var url = $(this).attr('href');
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#$reloadDiv-modal .modal-content').html(result);
            }
        });
        return false;
    });
    
    $('#$reloadDiv-grid-ezf-notify thead tr th a').on('click', function() {
        var url = $(this).attr('href');
       $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#$reloadDiv-modal .modal-content').html(result);
            }
        });
        return false;
    });
    
    
    
");
?>