<?php

use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$datachart = [
    [$dataChart[0][0], (float)$dataChart[0][1]],
    [$dataChart[1][0], (float)$dataChart[1][1]],

];

?>
<br/>
<div id="container2" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>

<?php
echo Highcharts::widget([
    'setupOptions' => [
        'lang' => [
            'thousandsSep' => ','
        ], 'id' => "graph_financial_budget",
    ],
    'options' => [
        'title' => $title,
        'chart' => [
            'renderTo' => 'container2',
        ],
        'plotOptions' => [
            'pie' => [
                'size' => '90%',
                'name'=>'Financial Type',
                'cursor' => 'pointer',
                'innerSize' => '50%',
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
        'colors'=>['#4db8ff','#ff9933'],
        'series' => [
            [// new opening bracket
                'type' => 'pie',
                'data' => $datachart,
                'name'=> 'Amount',
            ] // new closing bracket
        ],
    ],
]);
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>

</script>
<?php \richardfan\widget\JSRegister::end(); ?>

