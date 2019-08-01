<?php

use kartik\grid\GridView;

echo appxq\sdii\widgets\GridView::widget([
    'id' => 'grid-report',
//'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    //'autoXlFormat' => true,
    'columns' => [
        'pt_hn',
        'patientfullname',
        'app_date',
        'InspectName',
        'sect_name',
        'DoctorFullname',
        [ // รวมคอลัมน์
            'attribute' => 'DoctorFullname',
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) {
                return '<a target="_blank" class="btn btn-success" href="'.yii\helpers\Url::to(['/patient/calendar/appoin-pdf','appoint_id'=>$model['id']]).'">Print Order</a>';
            }
        ],
    ],
    
]);
?>