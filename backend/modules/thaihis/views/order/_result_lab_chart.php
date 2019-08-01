<?php

use miloschuman\highcharts\Highcharts;

/* echo backend\modules\ezforms2\classes\BtnBuilder::btn()
  ->ezf_id('1511490170071641200')
  ->target('1512561177067044000')
  ->initdata(['app_chk_pt_id' => $pt_id])
  ->label('<i class="glyphicon glyphicon-plus"></i>')->options(['class' => 'btn btn-sm pull-right btn-danger'])
  ->buildBtnAdd(); */
if ($arrResultLab) {
    ?>
    <div class="pull-right">
        <?php
        echo yii\helpers\Html::a('View all', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
            'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn,
                'visit_id' => $visit_id, 'view' => 'modal', 'secname' => '', 'date' => $date])]);

        $url = \yii\helpers\Url::to(['/patient/restful/print-report-lab', 'pt_hn' => $pt_hn, 'date' => $date, 'secname' => $secname,]);
        ?>
      <a class="btn btn-warning btn-sm print-report-order" target="_blank" href="<?= $url ?>" title="Print">
        <span class="fa fa-print"></span>
      </a>
    </div>
    <?php
    $btn = '';
    $seriesResult = [];
    foreach ($arrResultLab as $keySecName => $valueSecName) {
        $series = null;
        foreach ($valueSecName as $keyTestName => $valueTestName) {
            $seriesResult = null;
            foreach ($valueTestName as $key => $value) {
                if ($key !== 'show') {
                    $seriesResult[] = [date($key), $value];
                } else {
                    $visible = $value == 1 ? true : false;
                }
            }
            $series[] = ['name' => $keyTestName, 'data' => $seriesResult,
                'visible' => $visible,
                    //'dataLabels' => ['enabled' => TRUE]
            ];
        }

        if ($view == 'cpoe') {
            $btn = [
                'text' => 'View',
                'onclick' => new \yii\web\JsExpression("
                function(){
                var url = '" . yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
                            'reloadDiv' => $reloadDiv, 'view' => 'modal', 'secname' => $keySecName, 'date' => $date]) . "';
                modalResulOrder(url);
                }"),
            ];
        } elseif ($view == 'modal_chart') {
            $btn = [
                'text' => 'Table',
                'onclick' => new \yii\web\JsExpression("
          function(){
          var url = '" . yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
                            'reloadDiv' => $reloadDiv, 'view' => 'modal', 'secname' => $keySecName, 'date' => $date]) . "';
          modalResulOrder(url);
          }"),
            ];
        }

        echo Highcharts::widget([
            'id' => 'highchart-' . $view . '-' . $keySecName,
            'scripts' => [
                'modules/exporting',
            ],
            'options' => [
                'title' => ['text' => $keySecName],
                'xAxis' => [
                    //'categories' => array_keys($chkAppDate[$keySecName]),
                    'type' => 'datetime',
                    'labels' => ['enabled' => false]
                ],
                'series' => $series,
                'navigation' => [
                    'buttonOptions' => [
                        'theme' => [
                            'style' => [
                                'color' => '#039',
                                'textDecoration' => 'underline',
                            ],
                        ]
                    ]
                ],
                'exporting' => [
                    'buttons' => [
                        'contextButton' => [
                            'enabled' => FALSE,
                        ],
                        'viewButton' => $btn
                    ]
                ]
            ],
        ]);
    }
} else {
    ?>
    <h1 class = "text-center" style = "font-size: 35px; color: #ccc;">
      <?= Yii::t('patient', 'Find not found.') ?>
    </h1>
    <?php
}
