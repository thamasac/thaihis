<?php

use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;

?>
<div class="col-md-12" style="background-color: #fff">
    <?php

        $graphtarget = 'graph-display-'.$visit  ;
        $graphsize = 500;

        $head = '<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 class="modal-title" id="itemModalLabel">'.$title.'</h3>
</div>';
            $b01 = '<div class="modal-body">';
            $b02 = '</div>';

        echo $head.$b01;
        ?>
    <div id="<?= $graphtarget ?>" style="padding-top: 0%; ">

        <?php

                echo Highcharts::widget([
                    'scripts' => [
                        'modules/exporting',
                    ],
                    'options' => [
                        'title' => '',
                        'id'=>"graph$visit",
                        'chart' => [
                            'renderTo' => $graphtarget,
                            'height'=>$graphsize,
                        ],
                        'xAxis' => [
                            'categories' => $category,
                            'labels'=>[
                                'rotation'=>-45,
                            ],
                        ],
                        'yAxis' => [
                            'title' => ['text' => Yii::t('graphconfig', 'Values')]
                        ],
                        'series' => $series,
                        'credits'=>false
                    ]
                ]);
        echo $b02;
?>
</div>



