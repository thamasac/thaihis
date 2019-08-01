<?php

use miloschuman\highcharts\Highcharts;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo Highcharts::widget([
    'options' => [
        'title' => ['text' => $title['text']],
        'id' => "graph-line-financial",
        'chart' => [
            'renderTo' => 'container',
            'height' => $graphheight,
        ],
        'xAxis' => [
            'categories' => $categories,
            'labels' => [
                'rotation' => -45,
            ],
        ],
        'yAxis' => [
            'title' => ['text' => Yii::t('graphconfig', 'Values')]
        ],
        'colors'=>['#4db8ff','#ff9933'],
        'series' => $series
    ]
]);
?>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>