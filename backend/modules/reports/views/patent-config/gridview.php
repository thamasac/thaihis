<?php

use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\helpers\Url;

Pjax::begin(['id' => 'generic-grid-pjax', 'timeout' => FALSE]);

echo appxq\sdii\widgets\GridView::widget([
    'id' => 'generic-grid',
    'panelBtn' => $initdata ? '<button class="btn btn-success ezform-main-open" data-modal="modal-ezform-main" data-url="/ezforms2/ezform-data/ezform?ezf_id=' . $ezf_id . '&amp;modal=modal-ezform-main&amp;reloadDiv=&amp;initdata=' . $initdata . '&amp;target=' . $target . '&amp;dataid=&amp;targetField=' . $target . '"><i class="glyphicon glyphicon-plus"></i></button>' : '',
    'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
        return [
            'data' => ['key' => $model['id']],
        ];
    },
    'columns' => [
        [
            'attribute' => 'cid_project',
            'label' => 'รหัสบัตรประชาชน',
        ],
        [
            'attribute' => 'fullname_project',
            'label' => 'ชื่อ-สกุล',
        ],
        [
            'attribute' => 'project_name',
            'label' => 'หน่วยงาน',
        ],
        [
            'attribute' => 'receipt_id',
            'label' => 'สถานะ',
            'format' => 'html',
            'value' => function($model) {
                return isset($model['receipt_id']) ?
                        '<span class="btn btn-success btn-block btn-xs fa fa-check-square"></span> ' :
                        '<span class="btn btn-danger btn-block btn-xs fa fa-window-close"></span>';
            },
            'contentOptions' => ['style' => 'width:60px;text-align: center;'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function($url, $model) {
                    $html = Html::a('<i class="glyphicon glyphicon-trash"></i> ', 'javascript:deletes("' . $model['id'] . '")', [
                                    // 'data-url' => '/ezforms2/ezform-data/delete?ezf_id=1517227483007856300&dataid=' . $model['id'] . '',
                                    // 'class' => 'btn btn-danger',
                    ]);
                    return $html;
                },
            ],
            'contentOptions' => ['style' => 'width:60px;text-align: center;'],
        ],
    ],
]);
?>
<?php
Pjax::end();
?>
</div>
<?php
$url_change = Url::to(['get-gridview']);
$url_delete = Url::to(['delete']);
$url = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => 'modal-appoint']);
$this->registerJs("
$('#generic-grid-pjax').on('dblclick', 'tbody tr', function() {
        var url = '$url' + '&dataid=' + $(this).attr('data-key'); 
        modalEzformMain2(url,'#modal-ezform-main');  
});	

function modalEzformMain2(url,modal) {
    $(modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $(modal).modal('show')
    .find('.modal-content')
    .load(url);
}
function deletes(id){
 yii.confirm('คุณแน่ใจหรือว่าต้องการลบรายการนี้?', function(){
    $.get('$url_delete?id='+id, function(data, status){
        $.pjax.reload({container:'#generic-grid-pjax',timeout: false});
     });
   });
}
 $('#modal-ezform-main').on('hidden.bs.modal', function (e) {
   reload();
 });
 function reload(){
  var project_id = $('#project_id').val();
    var date_start_project = $('#date_start_project').val();
    var date_end_project = $('#date_end_project').val();
    if(project_id != '' && date_start_project != '' && date_end_project != ''){
        $('#gridview').load('$url_change?target='+project_id+'&date_start_project='+date_start_project+'&date_end_project='+date_end_project);
    }else{
    
    }
}
");
?>