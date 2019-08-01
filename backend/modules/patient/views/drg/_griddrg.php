<?php

use appxq\sdii\widgets\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

echo GridView::widget([
    'id' => 'drgcounter-grid',
    'panelBtn' => $this->render('_search_drgcounter', ['model' => $searchModel, 'reloadDiv' => $reloadDiv]),
    'dataProvider' => $dataProvider,
//    'rowOptions' => function ($model)use ($ezfRight_id, $reloadDiv) {
//        $class = '';
//        if ($model['right_status'] == '8') {
//            $class = 'bg-warning';
//        } else {
//            $class = '';
//        }
//        $data = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['right_status' => '2']);
//        return ['data' => ['key' => $model['right_id'], 'status' => $model['right_status'],
//                'ptid' => $model['visit_pt_id']], 'class' => $class,
//            'data-url' => Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezfRight_id,
//                'dataid' => $model['right_id'], 'modal' => 'modal-ezform-main', 'reloadDiv' => $reloadDiv
//                , 'initdata' => $data])];
//    },
    'columns' => [
        [
            'attribute' => 'pt_hn',
            'label' => 'HN'
        ], [
            'attribute' => 'fullname',
            'label' => Yii::t('patient', 'Name')
        ], [
            'attribute' => 'visit_type_name',
            'label' => Yii::t('patient', 'Type')
        ], [
            'attribute' => 'di_txt',
            'label' => Yii::t('patient', 'Diagnosis'),
            'value' => function($model) {
                return strip_tags($model['di_txt']);
            },
        ], [
            'attribute' => 'diag_icd10',
            'label' => Yii::t('patient', 'ICD10')
        ], [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{receive}',
            'buttons' => [
                'receive' => function($url, $model)use($reloadDiv) {

                    $html = Html::a('<i class="fa fa-user"></i> ' . Yii::t('patient', 'Edit'), '#', [
                                'data-url' => Url::to(['/cpoe/cpoe/cpoe-view', 'ptid' => $model['ptid'], 'visitid' => $model['visit_id']
                                    , 'visit_tran_id' => '', 'visit_type' => $model['visit_type']
                                    , 'action' => 'que', 'modal' => 'modal-drgcounter', 'reloadDiv' => $reloadDiv]),
                                'class' => 'btn btn-danger btn-xs btn-block',
                    ]);
                    return $html;
                },
            ],
            'contentOptions' => ['style' => 'width:120px;text-align: center;'],
        ]
    ],
]);
?>