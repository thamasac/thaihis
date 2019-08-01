<?php

use yii\helpers\Html;
use appxq\sdii\widgets\GridView;

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title"></h3>
    </div>
    <div class="modal-body">
        <?=
        GridView::widget([
            'id' => 'ViewAss',
            'dataProvider' => $dataProvider,
            'columns' => [
                [
//                    'attribute' => 'firstname',
                    'label' => 'Name',
                    'format' => 'raw',
                    'value' => function($data)use($data_check) {

                        return $data['firstname'] . " " . $data['lastname'];
                    },
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center']
//                ], [
//                    'attribute' => 'lastname',
//                    'label' => 'Lastname',
//                    'headerOptions' => ['class' => 'text-center'],
//                    'contentOptions' => ['class' => 'text-center']
                ], [
                    'label' => 'Status',
                    'format' => 'raw',
                    'value' => function($data)use($data_check, $data_approve,$data_action) {
                        foreach ($data_check as $value) {
                            if ($data['user_id'] == $value) {
                                if (isset($data_approve[$value]) && $data_action == 'Approve') {
                                    if($data_approve[$value] == '1'){
                                        return Html::tag('label', 'Approved', ['class' => 'label label-success']);
                                    }else if($data_approve[$value] == '0'){
                                        return Html::tag('label', 'Not Approved', ['class' => 'label label-danger']);
                                    }
                                    
                                } else {
                                    return Html::tag('label', 'Completed', ['class' => 'label label-success']);
                                }
                            }
                        }
                        return Html::tag('label', 'Waiting', ['class' => 'label label-warning']);
                    },
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center']
//                ], [
//                    'attribute' => 'role_detail',
//                    'label' => 'Role',
//                    'format' => 'raw',
//                    'value' => function($data)use($data_check) {
//
//                        return Html::tag('label', $data['role_detail'], ['class' => 'label label-info']);
//                    },
//                    'headerOptions' => ['class' => 'text-center'],
//                    'contentOptions' => ['class' => 'text-center']
                ],
            ]
        ]);
        ?>
    </div>
    <div class="modal-footer">
        <?= Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
    </div>
</div>

<?php
$this->registerJs("
        $('#ViewAss .pagination a').on('click', function() {
            viewAssign($(this).attr('href'));
            return false;
        });
    ");
    

