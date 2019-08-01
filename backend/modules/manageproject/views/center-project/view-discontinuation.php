<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div id="disconnect">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="h4"><?= Yii::t('project', 'Request for Discontinuation') ?></h4>
    </div>

    <div class="modal-body">
        <?=
        \appxq\sdii\widgets\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'format' => 'raw',
                    'attribute' => '',
                    'value' => function($model) {
                        return Html::img($model['icon']);
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'project_name',
                    'label' => Yii::t('project', 'Project Name'),
                    'value' => function($model) {
                        return $model['project_name'];
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'project_acronym',
                    'label' => Yii::t('project', 'Project Acronym'),
                    'value' => function($model) {
                        return $model['project_acronym'];
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'descriptions',
                    'label' => Yii::t('project', 'Descriptions'),
                    'value' => function($model) {
                        return $model['descriptions'];
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'status',
                    'label' => Yii::t('project', 'status'),
                    'value' => function($model) {
                        if ($model['status'] == '10') {
                            return "<div class='label label-warning' style='display:block;font-size:14pt;'>" . Yii::t('project', 'Discontinuation requested') . "</div>";
                        } else if ($model['status'] == '20') {
                            return "<div class='label label-success' style='display:block;font-size:14pt;'>" . Yii::t('project', 'Discontinuation Granted') . "</div>";
                        } else {
                            return "<div class='label label-default' style='display:block;font-size:14pt;'>" . Yii::t('project', 'Request for discontinuation') . "</div>";
                        }
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'project_type_text',
                    'label' => Yii::t('project', 'Project Type'),
                    'value' => function($model) {
                        return $model['project_type_text'];
                    }
                ],        
                        
            ]
        ])
        ?>
    </div>
</div>
<?php appxq\sdii\widgets\CSSRegister::begin(); ?>
<style>
    .h4{
        font-size:18pt;
    }
    .table thead tr th {
        font-size: 16pt;
        text-align: center;
    }
    .table tbody tr td {
        font-size: 14pt;
    }
    .grid-view .panel .summary {
        margin-bottom: 5px;
        text-align: left;
        font-size: 14pt;
        margin-top: 5px;
    }
</style>
<?php appxq\sdii\widgets\CSSRegister::end(); ?>