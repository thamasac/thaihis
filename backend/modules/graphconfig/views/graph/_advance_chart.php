<?php

use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
?>
<div class="col-md-12">
<?php
if ($error == 1) {
    ?>
    <div class="panel-warning">
        <div class="panel-heading" style="text-indent: 2.5em;"><?= Yii::t('graphconfig', 'SQL Not complete or error'); ?></div>
    </div>
   <?php
  
} else {
    $graphtarget = $modal == 1 ? 'chart-modal-'.$parentid : 'chart'.$parentid ;
    $padding = '0%';
    $graphsize = 400;
    $piesize = '60%';
    if($modal == 1){
        $head = '<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 class="modal-title" id="itemModalLabel">'.$title.'</h3>
</div>';
        $b01 = '<div class="modal-body">';
        $b02 = '</div>';
        $foot = '<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> '.Yii::t('graphconfig', 'Close').'</button>    
</div>';
        
    }else{
        $head = '';
        $b01 = '';
        $b02 = '';
        $foot = '';
    }
    if($modal == 1 || $newpage == 1 ){
        $piesize = '100%';
        $graphsize = 700;
        $padding = '0%';
    }
    if($newpage == 1 ){
        
    $this->registerCss("             
            .table{
                width:100%;
                border-collapse: collapse;
            }   
            #$graphtarget{
                padding-top: 0%;
            }
            
            table, th, td {
                border: 1px solid black;
            }

        ");
        
    }

    //$graph_title = Yii::t('graphconfig', 'Data');
    echo $head.$b01;
    ?>
    <div id="<?= $graphtarget ?>" style="padding-top: 0%; ">

        <?php
        if ($dataQuery) {
            if ($report_type == 0) {//ตาราง
                ?>

                <div style="padding: 1.5em;">
                    <table class="table table-bordered"> 
                        <thead class="thead-dark">
                            <tr> 
                                <?php foreach ($dataQuery[0] as $key => $val) { ?>
                                    <th style="text-align: center;"><?= $key ?></th> 
                                <?php } ?> 
                            </tr> 
                        </thead>
                        <tbody>
                            <?php foreach ($dataQuery as $key => $value) {
                                ?>
                                <tr> 
                                    <?php
                                    foreach ($value as $val) {

                                        if (is_numeric($val)) {
                                            ?>
                                            <td style="text-align: center;"><?= $val ?></td> 
                                        <?php } else { ?>
                                            <td style="text-align: center;"><?= $val == '' || $val == null ?  Yii::t('graphconfig', 'Null') : $val ?></td> 
                                        <?php
                                        }
                                    }
                                    ?>

                                </tr> 
            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php
            } else if ($report_type == 1) { //กราฟวงกลม
                
                echo Highcharts::widget([
                    'setupOptions' => [
                        'lang' => [
                            'thousandsSep' => ','
                        ],'id'=>"graph$parentid",
                    ],
                    'scripts' => [
                        'modules/exporting',
                    ],
                    'options' => [
                        'title' => ['text' => $title
                        ],
                        
                        'chart' => [
                            'renderTo' => $graphtarget,
                        ],
                        'plotOptions' => [
                            'pie' => [
                                'size' => $piesize,
                                'cursor' => 'pointer',
                                'dataLabels' => [
                                    'enabled' => true,
                                    'format' => new JsExpression("'<b>{point.name}</b>: {point.y:,.0f}/{point.percentage:.1f} %'"),
                                    'style' => [
                                        'left' => '50px',
                                        'top' => '18px',
                                        'color' => new JsExpression("(Highcharts.theme && Highcharts.theme.contrastTextColor) || '#4a7ae1'")
                                    ]
                                ],
                            ],
                        ],
                        'series' => [
                            [// new opening bracket
                                'type' => 'pie',
                                'name' => $valueRow,
                                'data' => $datachart
                            ] // new closing bracket
                        ],
                        'credits'=>false
                    ],
                ]);
            } else if ($report_type == 3) { //กราฟแท่ง
                echo Highcharts::widget([
                    'setupOptions' => [
                        'lang' => [
                            'thousandsSep' => ','
                        ],
                    ],
                    'scripts' => [
                        'modules/exporting',
                    ],
                    'options' => [
                        'title' => [
                            'text' => $title
                        ],
                        'id'=>"graph$parentid",
                        'chart' => [
                            'renderTo' => $graphtarget,
                            'height'=>$graphsize,
                        ],
                        'xAxis' => [
                            'categories' => $datattype,
                            'labels'=>[
                                'rotation'=>-45,
                            ],
                        ],
                        'yAxis' => [
                            'title' => ['text' => Yii::t('graphconfig', 'Values')]
                        ],
                        'plotOptions' => [
                            'size' => '20%',
                            'series' => ['shadow' => true],
                            'candlestick' => ['lineColor' => '#404048'],
                        ],
                        'series' => $dataline,
                        'credits'=>false
                    ]
                ]);
            } else if ($report_type == 2) {//กราฟเส้น
                echo Highcharts::widget([
                    'scripts' => [
                        'modules/exporting',
                    ],
                    'options' => [
                        'title' => ['text' => $title],
                        'id'=>"graph$parentid",
                        'chart' => [
                            'renderTo' => $graphtarget,
                            'height'=>$graphsize,
                        ],
                        'xAxis' => [
                            'categories' => $datattype,
                            'labels'=>[
                                'rotation'=>-45,
                            ],
                        ],
                        'yAxis' => [
                            'title' => ['text' => Yii::t('graphconfig', 'Values')]
                        ],
                        'series' => $dataline,
                        'credits'=>false
                    ]
                ]);
            }
            ?>

        </div>
        <?php if ($selectbox != '') { ?>

            <div class="panel-warning">
                <div class="panel-heading" style="text-indent: 2.5em;"><?= $selectbox; ?></div>
            </div>

            <?php
        }
    } else {
        ?>
        <div class="panel-warning" style="margin-top: 15px;">
            <div class="panel-heading" style="text-indent: 2.5em;"><?= Yii::t('graphconfig', 'No data'); ?></div>
        </div>

    <?php
    }
    echo $b02.$foot;
}
?>
</div>


