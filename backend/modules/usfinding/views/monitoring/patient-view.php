<?php
use kartik\grid\GridView;
?>
<div class="panel-success2" >
    <div class="panel-heading2 " style="border-radius: 10px 10px 0 0;">
        <table>
            <tr>
                <td rowspan="2">
                    <i class="fa fa-users" style="font-size:70px;padding-right:15px;"></i>
                </td>
                <td>
                    <label style="font-size:32px"><strong>Patients</strong></label><br/>
                    <label style="font-size:22px">ผู้ป่วยที่เข้ามารับบริการ และดูแลอยู่</label>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
echo GridView::widget([
    'dataProvider' => $provider,
    'id' => 'sites-report',
    'panel' => [
        'type' => \Yii::$app->request->get('action') ? Gridview::TYPE_SUCCESS : Gridview::TYPE_SUCCESS,
    ],
    'columns' => [
//                            [
//                            'class' => 'yii\grid\SerialColumn',
//                            'headerOptions' => ['style' => 'text-align: center;'],
//                            'contentOptions' => ['style' => 'width:5px;text-align: center;'],
//                        ],
            [
            'header' => 'ลำดับที่',
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:4%;text-align: center;'],
        ],
            [
            'header' => 'HOSPCODE',
            'attribute' => 'hospcode',
            'value' => function ($model) {
                return ($model['hospcode']);
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:10%;text-align: center;'],
        ],
            [
            'header' => 'ชื่อ-สกุล',
            'value' => function ($model) {
                return $model['title'] . $model['name'] . ' ' . $model['surname'];
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:20%;text-align: left;'],
        ], [
            'header' => 'เพศ',
            'value' => function ($model) {
                return $model['gender']==1?'ชาย':'หญิง';
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:7%;text-align: left;'],
        ],[
            'header' => 'อายุ',
            'value' => function ($model) {
                return $model['age'];
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:7%;text-align: left;'],
        ],
            [
            'format'=>'raw',
            'header' => 'ICF',
            'attribute' => 'icf',
            'value' => function ($model) {
                return $model['icf']=='0'||$model['icf']==''?"<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;font-size:16px;'></i></center>":"<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;font-size:16px;'></i></center>";
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:30px;text-align: center;'],
        ], [
            'format'=>'raw',
            'header' => 'register',
            'attribute' => 'register',
            'value' => function ($model) {
                return $model['register']=='0'||$model['register']==''?"<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;font-size:16px;'></i></center>":"<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;font-size:16px;'></i></center>";;
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ],[
            'format'=>'raw',
            'header' => 'CCA-01',
            'attribute' => 'cca01',
            'value' => function ($model) {
                return $model['cca01']=='0'||$model['cca01']==''?"<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;font-size:16px;'></i></center>":"<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;font-size:16px;'></i></center>";;
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ],[
            'format'=>'raw',
            'header' => 'CCA-02',
            'attribute' => 'cca02',
            'value' => function ($model) {
                return $model['cca02']=='0'||$model['cca02']==''?"<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;font-size:16px;'></i></center>":"<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;font-size:16px;'></i></center>";;
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ],
    ],
    'pjax' => true
]);
?>