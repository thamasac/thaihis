<?php

use kartik\daterange\DateRangePicker;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
use miloschuman\highcharts\Highcharts;

//$this->title = 'Monitoring Panel';
try {

    $findUserInput = Select2::widget([
        'name' => 'user',
        'id' => 'user',
        'value' => isset($userId) ? $userId : "",
        'data' => \yii\helpers\ArrayHelper::map($dataUser, "id", "name"),
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => '/usfinding/default/finduser',
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {search:params.term}; }'),
                //                            'delay' => 50,
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(city) { return city.text; }'),
            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
        ],
        'options' => ['class' => "float-left mr-1", 'placeholder' => 'กรุณาเลือกผู้มีผู้ใช้งาน'],
    ]);
} catch (Exception $e) {
    echo " findUserInput has exception.";
}
try {
    if ($startDate != null && $endDate != null) {
        $currentValue = $startDate . ' to ' . $endDate;
    }
    $dateFilterWidget = DateRangePicker::widget([
        'name' => 'date_range_3',
        'id' => 'dateRange',
        'hideInput' => true,
        'value' => $currentValue,
        'convertFormat' => true,
        'useWithAddon' => true,
        'pluginEvents' => [
            'apply.daterangepicker' => 'function(ev, picker) { start_date = picker.startDate.format(\'YYYY-MM-DD HH:mm:ss\');end_date = picker.endDate.format(\'YYYY-MM-DD HH:mm:ss\');console.log(picker)}'
        ],
        'pluginOptions' => [
            'timePicker' => true,
            'timePickerIncrement' => 10,
            'locale' => ['format' => 'Y-m-d H:i',
                'separator' => ' to ',
            ]
        ]
    ]);
    $dateFilter = HTML::tag('div', "$dateFilterWidget", ['class' => 'input-group drp-container']);
} catch (Exception $e) {
    echo "DATERANGE NOT WORKING.";
}
$inputTrack = HTML::input("checkbox", "track", 1, ['id'=>'track','checked' => $type["track"] == '1' ? true : false ]);
$inputApi = HTML::input("checkbox", "api", 1, ['id'=>'api','checked' => $type["api"] == '1' ? true : false ]);
$inputLog = HTML::input("checkbox", "log", 1, ['id'=>'log','checked' => $type["log"]== '1' ? true : false ]);
$inputError = HTML::input("checkbox", "error", 1, ['id'=>'error','checked' =>  $type["error"]== '1' ? true : false]);
$inputInfo = HTML::input("checkbox", "info", 1, ['id'=>'info','checked' =>  $type["info"] == '1' ? true : false]);
$findUserActivityPanel = Html::panel([
    'heading' => "Find User Activity",
    'body' => "<form class='form-horizontal'>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <div class='form-group'>
                                        <label class=' col-md-3 control-label'>เลือกช่วงเวลา</label>
                                        <div class='col-md-9'>
                                            $dateFilterWidget
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class='col-md-3 control-label'>เลือก Type</label>
                                        <div class='col-md-9'>
                                                 $inputLog <label>LOG</label> 
                                                 $inputInfo <label>INFO</label> 
                                                 $inputError <label>ERROR</label>            
                                                 $inputTrack <label>TRACK</label>            
                                                 $inputApi <label>API</label>            
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class=' col-md-3 control-label'>เลือก User</label>
                                        <div class='col-md-9'>
                                            $findUserInput
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <div class='col-md-12'>
                                            " . Html::button('Search', ['id' => 'searchButton', 'class' => 'btn btn-primary btn-block']) . "     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        ",
], "primary");

$totalUsePanel = Html::panel(["heading" => "จำนวนผู้ใช้งาน",
    "body" => "<h3>ผู้ใช้งานขณะนี้ <span>$totalOnline คน.</span></h3>
             <h4>ผู้ใช้งานทั้งหมด <span>$totalUsers คน.</span></h4>
        "], "primary");
$logChart = "LogChart";
try {
    $logChart = Highcharts::widget([
        'setupOptions' => [
            'lang' => [
                'thousandsSep' => ','
            ],
        ],
        'options' => [
            'title' => [
                'text' => ''
            ],
            'chart' => [
                'renderTo' => 'show-hightchart',
            ],
            'xAxis' => [
                'categories' => $dataName,
            ],
            'yAxis' => [
                'title' => ['text' => 'จำนวน(ครั้ง)']
            ],
            'plotOptions' => [
                'series' => [
                    'cursor' => 'pointer',
                    'events' => [
                        'click' =>  new JsExpression('function(event){}')
                    ]
                ]
            ],
            'colors' => ['#28a745', '#ff0000', '#ff9900' , '#343a40' , '#007bff'],
            'series' => [
                [
                    'type' => 'column',
                    "name"=>"Request Chart",
                    'data' => $dataChart,
                    'colorByPoint' => TRUE,
                    'dataLabels' => [
                        'enabled' => true,
                        'color' => 'red',
                        'align' => 'center',
                        'format' => '{point.y:,.0f}',
                        'style' => [
                            'fontSize' => '12px',
                            'fontFamily' => 'Verdana, sans-serif',
                        ],
                    ],
                ]
            ]
        ]
    ]);
} catch (Exception $e) {
    echo "Highchart has Exception";
}

try {

    $versionDataArr = [];

    foreach ($versionUsage as $value){
        array_push($versionDataArr,[ "name" =>$value["version"] , "y" => (int)$value["count_user"]]);
    }
    $versionChart = Highcharts::widget([
        'options' => [
            'chart' => [
                'plotBackgroundColor' => null,
                'plotBorderWidth' => null,
                'plotShadow' => false,
                'type' => 'pie'
            ],

            'tooltip' => [
                'pointFormat'=> '{series.name}: <b>{point.y}</b>'
            ],
            'plotOptions' => [
                'pie' => [
                    'allowPointSelect' => true,
                    'cursor' => 'pointer',
                    'dataLabels' => [
                        'enabled' => true,
                        'format' => '<b>{point.name}</b>: {point.percentage:.1f} %',
                    ]
                ]
            ],
            'title' => [
                'text' => 'สัดส่วนเวอร์ชัน'
            ],
            'series' => [
                [
                    "name"=>"Version",
                    "data"=> $versionDataArr
                ]
            ]
        ]
    ]);
} catch (Exception $e) {
    $versionChart = "Highchart has Exception";
}

$iosUsage = $platformUsage['IOS'];
$androidUsage = $platformUsage['ANDROID'];
$desktopUsage = $platformUsage['DESKTOP'];

$versionPanel = Html::panel([
    'heading' => "เวอร์ชันและแพรตฟอร์ม",
    'body' => "  <div class='row'>
                         <div class='col-md-10'>
                            $versionChart
                        </div>
                        <div class='col-md-2 text-center'>
                          <div id='iosUsage1' class='h2 mb-0 text-primary'>$iosUsage</div>
                          <div class='h4 text-muted'>IOS Devices</div>
                          <hr>
                          <div id='androidUsage1' class='h2 mb-0 text-success'>$androidUsage</div>
                          <div class='h4 text-muted'>ANDROID Devices</div>
                                                    <hr>

                              <div class='h2 text-warning'>$desktopUsage</div>
                          <div class='h4 text-muted'>DESKTOP Devices</div>
                        </div>
                        </div>
                        ",
],'primary');


try {
    foreach ($userPerDay as &$value) {
        $value = (int)$value;
    }
    $userActiveChart = Highcharts::widget([
        'options' => [
            'title' => [
                'text' => 'ผู้ใช้ต่อวัน'
            ],
            'plotOptions' => [
                'series' => [
                    'cursor' => 'pointer',
                    'events' => [
                        'click' =>  new JsExpression('function(event){ var category = event.point.category; var url = \''
                            .Url::toRoute('monitoring/users-application-list?command=userActive&param1=').
                            '\'; window.location.href = url+category+"&application=usmobile"; }')
                    ]
                ]
            ],
            'xAxis' => [
                'categories' => array_reverse(array_keys($userPerDay)),
            ],

            'yAxis' => [
                'title' => ['text' => 'จำนวนผู้ใช้']
            ],
            'series' => [
                [
                    "name"=>"จำนวนผู้ใช้",
                    "data"=> array_reverse(array_values($userPerDay))
                ]
            ]
        ]
    ]);
} catch (Exception $e) {
    $userActiveChart = "Highchart has Exception";
}

$userActivePanel = Html::panel([
    'heading' => "ผู้ใช้งานแอพลิเคชั่น",
    'body' => "  <div class='row'>
                         <div class='col-md-10'>
                            $userActiveChart
                        </div>
                        <div class='col-md-2 text-center'>
                          <div id='totalUsage' class='h2 mb-0 text-primary'>$totalUsers</div>
                          <div class='h4 text-muted'>Total User</div>
                          <hr>
                          <div id='onlineUsage'  class='h2 mb-0 text-success'>$totalOnline</div>
                          <div class='h4 text-muted'>Online User</div>
                        </div>
                        </div>
                        ",
    ],'primary');


try {
    foreach ($totalRequest as &$value) {
        $value = (int)$value;
    }

    $requestChart = Highcharts::widget([
        'options' => [
            'chart' => [
                'type' => 'column'
            ],
            'tooltip' => [
                'headerFormat' => '<span style="font-size:10px">{point.key}</span><table>',
                'pointFormat'=> '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' .
            '<td style="padding:0"><b>{point.y:.1f} req.</b></td></tr>',
                'footerFormat'=> '</table>',
                'shared'=> true,
                'useHTML'=> true
            ],
            'plotOptions' => [
                'column'=> [
                        'pointPadding'=> 0.2,
                        'borderWidth'=> 0
                    ]
            ],
            'xAxis' => [
                'categories' => array_reverse(array_keys($totalRequest)),
                'crosshair' => true
            ],
            'yAxis' => [
                'title' => ['text' => 'จำนวน Request.']
            ],
            'title' => [
                'text' => 'อัตรารีเควส ต่อวัน'
            ],
            'series' => [
                [
                    "name"=>"Request API",
                    "data"=> array_reverse(array_values($totalRequest))
                ]
            ]
        ]
    ]);
} catch (Exception $e) {
    $requestChart = "Highchart has Exception";
}
$requestPanel = Html::panel([
    'heading' => "Request API",
    'body' => "  <div class='row'>
                         <div class='col-md-12'>
                            $requestChart
                        </div>
                </div>
                        ",
],'primary');

try{

$logListGrid = GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'create_date',
            'label' => 'Log Date',
            'headerOptions' => ['class' => 'text-center'],
        ],
        [
            'attribute' => 'user_id',
            'label' => 'User',
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                try {
                    $res = \common\models\UserProfile::find()->where(["user_id" => $model->user_id])->one();
                    $platform = $model->platform;
                    $version = $model->version;
                    $platformTag = $platform == "ios" ? "<span class='label label-info'>" : $platform == "android" ? "<span class='label label-success'>" :  "<span class='label label-default'>";
                    $fullname = "<span class='label label-primary'>$res->user_id</span><br> $res->firstname $res->lastname 
                                    <p>$res->sitecode</p>
                                    $platformTag $platform</span>
                                    <p>version. $version</p>";
                    return $fullname;
                } catch (Exception $e) {
                    return $model->user_id;
                }
            }
        ],
        [
            'attribute' => 'name',
            'label' => 'Log Name',
            'format' => 'html',
            'headerOptions' => ['class' => 'col-md-6 text-center'],
            'value' => function ($model) {
                if($model->type == "track" || $model->type == "TRACK"){
                    $input = json_decode( $model->input,true);
                    if($model->name == "EzForm Save" || $model->name == "EzFormWorking"){
                        $ezform = \backend\models\Ezform::find()->where(["ezf_id"=> $input["input1"]])->one();
                        if($ezform != null){
                            $input["label"] = str_replace("@input1",  $ezform->ezf_name, $input["label"]);
                            $input["label"] = str_replace("@input2", $input["input2"], $input["label"]);
                            $label = $input["label"];
                            return "<div> 
                                <h4>$model->name</h4>
                                <h3> $label </h3>
                            </div>";
                        }
                    }
                    $input["label"] = str_replace("@input1", $input["input1"], $input["label"]);
                    $input["label"] = str_replace("@input2", $input["input2"], $input["label"]);
                    $label = $input["label"];
                    return "<div> 
                                <h4>$model->name</h4>
                                <h3> $label </h3>
                            </div>";
                }

                $logInput = $model->input;
//                    $logInput = str_replace("\n","",$logInput);
                    $logInput = str_replace('\"','"',$logInput);
                    $logInput = str_replace('\/','/',$logInput);
                    return "<h4>$model->name</h4><pre>$logInput</pre>";
            }],

        [
            'attribute' => 'result',
            'label' => 'Response',
            'filter' => '',
            'headerOptions' => ['class' => 'col-md-1 text-center'],
        ],

        [
            'attribute' => 'type',
            'label' => 'Type',
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                $type = $model->type;
                if ($type == 'log' || $type == 'LOG') {
                    return "<span class='label label-success'>$type</span>";
                } elseif ($type == 'error' || $type == 'ERROR') {
                    return "<span class='label label-danger'>$type</span>";
                } elseif ($type == 'info'  || $type == 'INFO') {
                    return "<span class='label label-info'>$type</span>";
                }elseif ($type == 'track' || $type == 'TRACK') {
                    return "<span class='label label-default'>$type</span>";
                }elseif ($type == 'api' || $type == 'API') {
                    return "<span class='label label-primary'>$type</span>";
                }
                return "<span class='label label-muted'>$type</span>";
            }
        ], [
            'attribute' => 'application',
            'label' => 'Application',
            'headerOptions' => ['class' => 'text-center'],
        ],
    ],
]);
} catch (Exception $e) {
    echo $e;
}

$lastSyncLabel =  Html::tag('h1', $lastSync);
$monitorPanel = Html::tag("div",
    "
            <div class='panel panel-primary'>
                <div class='panel-heading'>
                    <h4>Monitoring Control</h4>
                 </div>
                <div class='panel-body'>
                    <div class='row'>
                        <div class='col-md-6'>
                            $findUserActivityPanel
                        </div>
                        
                        <div class='col-md-4' id='show-hightchart'>
                            $logChart
                        </div>
                          <div class='col-md-2'>
                            $totalUsePanel
                        </div>
                    </div>
                     <div class='row' >
                        <div class='col-md-12'>
                            $lastSyncLabel
                            $logListGrid
                        </div>
                     </div>
                </div>
            </div>
                ");

$statisticPanel = Html::tag("div",
    "
                    <h4>Statistic Control</h4>
                    <div class='row'>
                         <div class='col-md-6'>
                            $userActivePanel
                         </div>
                        <div class='col-md-6'>
                            $versionPanel
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6'>
                            $requestPanel
                        </div>
                    </div>
         
                ");

use kartik\tabs\TabsX;
$items = [
    [
        'label'=>'<i class="glyphicon glyphicon-tasks"></i> Statistic',
        'content'=>$statisticPanel,
        'active'=> $tab != 2
    ],
    [
        'label'=>'<i class="glyphicon glyphicon-list-alt"></i> Monitor',
        'content'=>$monitorPanel,
        'active'=> $tab == 2
    ]
];
// Above
//echo Html::tag("div", var_export(array_values($userPerDay)));
//echo Html::tag("div", var_export($versionUsage));
//echo Html::tag("div", var_export($platformUsage));

echo TabsX::widget([
    'items'=>$items,
    'position'=>TabsX::POS_ABOVE,
    'encodeLabels'=>false
]);

$this->registerJs(<<< JS
var start_date = '$start_date';
var end_date = '$end_date';

function datacheckbox(data){
    if(data == true){
      status = 1;
    }else{
      status = 0;
    }
    return status ;
}

function openUserList(command,param1){ 
    var url = '/usfinding/monitoring/users-application-list?command='+command+'&param1='+param1+'&application=usmobile';
    window.location.href = url; 
}
     
            
$('#onlineUsage').click( function(){
    openUserList('online','none');
});
            
$('#totalUsage').click( function(){
    openUserList('all','none');
});

$('#iosUsage1').click( function(){
    openUserList('platform','ios');
});
               
$('#androidUsage1').click( function(){
    openUserList('platform','android');
});

$('#searchButton').click( function(){
    var dateRange = $('#dateRange').val();
    var res = dateRange.split(" to ");
        console.log(res);

    var log = datacheckbox($('#log').is(':checked'));
    var error = datacheckbox($('#error').is(':checked'));
    var info = datacheckbox($('#info').is(':checked'));
    var track = datacheckbox($('#track').is(':checked'));
    var api = datacheckbox($('#api').is(':checked'));
    var url ='/usfinding/monitoring/log-stash?user_id='+$('#user').val()+'&log='+log+'&error='+error+'&info='+info+'&track='+track+'&api='+api+'&start_date='+res[0]+'&end_date='+res[1]+'&tab=2';   
    window.location.href=url;
});
JS
);
