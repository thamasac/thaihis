<?php

use appxq\sdii\widgets\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$columns[] = [
    'attribute' => 'pt_hn',
    'label' => 'HN'
];
$columns[] = [
    'attribute' => 'pt_cid',
    'label' => Yii::t('patient', 'Citizen ID')
];
$columns[] = [
    'attribute' => 'fullname',
    'label' => Yii::t('patient', 'Name')
];
$columns[] = [
    'attribute' => 'right_name',
    'label' => Yii::t('patient', 'Right')
];

//$columns[] = [
//    'class' => 'yii\grid\ActionColumn',
//    'template' => '{receive}',
//    'buttons' => [
//        'receive' => function($url, $model)use($ezfRight_id) {
//            /* $html = Html::a('<i class="fa fa-edit"></i> ' . Yii::t('patient', 'Edit Right'), '#', [
//              'data-key' => $model['right_id'],
//              'data-ptid' => $model['visit_pt_id'],
//              'data-status' => $model['right_status'],
//              'class' => 'btn btn-warning btn-xs btn-block',
//              ]); */
//            $initdata = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['right_status' => '1']);
//            $html = Html::a('<i class="fa fa-plus-square"></i> ' . Yii::t('patient', 'Add Right'), '#', [
//                        'data-key' => $model['visit_id'],
//                        'data-url' => Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezfRight_id,
//                            'target' => $model['visit_id'], 'modal' => 'modal-ezform-main', 'reloadDiv' => ''
//                            , 'initdata' => $initdata]),
//                        'class' => 'btn btn-success btn-xs btn-block',
//            ]);
//            return $html;
//        },
//    ],
//    'contentOptions' => ['style' => 'width:120px;text-align: center;'],
//];
        


echo GridView::widget([
    'id' => 'rightcounter-grid',
    'panelBtn' => $this->render('_searchrightcounter', ['model' => $searchModel, 'reloadDiv' => $reloadDiv, 'date'=>$date]),
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model)use ($ezfRight_id, $reloadDiv) {
        $class;
        if ($model['right_status'] == '8') {
            $class = 'bg-warning';
        } else {
            $class = '';
        }
        $data = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['right_status' => '2']);
        return ['data' => ['key' => $model['right_id'], 'status' => $model['right_status'],
                'ptid' => $model['visit_pt_id']], 'class' => $class,
            'data-url' => Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezfRight_id,
                'dataid' => $model['right_id'], 'modal' => 'modal-ezform-main', 'reloadDiv' => $reloadDiv
                , 'initdata' => $data])];
    },
    'columns' => $columns,
]);
?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('#date_filter').on('dp.change' ,function(){
        
    });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>