<?php
use yii\grid\GridView;

$total = 0;
foreach ($dataProvider->allModels as $item) {
        $total += $item['count'];
}
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title pull-left">
            <h4><?=$text?> Project Activity </h4>
        </div>
        <div class="panel-title pull-right">
            <button type="button" class="close" >×</button>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <?php
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{pager}{summary}{items}{pager}',
            'id' => 'project-grid',
            'showFooter' => true,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'text-align: center;max-width:30px;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                ],
                [
                    'attribute' => 'projectname',
                    'label' => Yii::t('report', 'Project Name'),
                    'format' => ['raw'],
                    'footer' => '<b>Total</b>',
                ],
                [
                    'attribute' => 'count',
                    'label' => Yii::t('report', 'User Activity'),
                    'format' => ['raw'],
                    'headerOptions' => ['style' => 'text-align: center;width: 20%;'],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'footer' => "<b>".number_format($total)."</b>",
                    'value'=>function($model)  {
                        return number_format($model['count']);
                    }
                ],
            ],
        ]);
        ?>
    </div>
</div>
