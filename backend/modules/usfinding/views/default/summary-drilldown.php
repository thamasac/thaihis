<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;

if ($state == 'doctor') {
    echo GridView::widget([
            'dataProvider' => $provider,
            'toolbar' => [],
            'panel' => [
                'type' => GridView::TYPE_PRIMARY,
                'heading' => $doctorname,
            ],
            'pjax' => true,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => 'No.',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;']
                 ],
                [
                    'attribute' => 'hsitecode',
                    'label' => 'Site ID',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'hptcode',
                    'label' => 'Participant ID',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'age',
                    'label' => 'Age',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'f1v3',
                    'label' => 'Gender',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'value' => function($model){
                                    if ($model['f1v3'] == 1)
                                        return "ชาย";
                                    else if ($model['f1v3'] == 2)
                                        return "หญิง";
                    }
                 ],
                [
                    'attribute' => 'f2v1',
                    'label' => 'Exam Date',
                    'format' => ['raw'],
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'value' => function($model) {
                                    $f2v1 = $model['f2v1'];
                                    $dataid = $model['id_cca02'];
                                    $target = base64_encode($model['ptid']);
                                    $ptid = $model['ptid'];
                                    return "<a  id = 'ccaModal'
                                                ezf_id = '1437619524091524800'
                                                dataid = '$dataid'
                                                comp_target = '1437725343023632100'
                                                target = '$target'
                                                comp_id_target = '1437725343023632100'
                                                read = '1'
                                                title='เปิดอ่านเท่านั้น'
                                                data-target='#cca-modal'>$f2v1</a>&nbsp;&nbsp;&nbsp;"
                                            ."<a  id = 'editcca'
                                                ezf_id = '1437619524091524800'
                                                dataid = '$dataid'
                                                ptid = '$ptid'
                                                data_url = '/teleradio/suspected/open-form?dataid=$dataid&ezf_id=1437377239070461301&ptid=$ptid'
                                                title='เปิดเพื่อแก้ไข และดูภาพ'><span class='glyphicon glyphicon-edit'></span></a>"
                                                ;                       
                                   }
                 ],
                [
                    'label' => 'Value',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'value'=>function($model) use($doctorname){
                                    return $doctorname;
                            }
                 ],
            ],
        ]);
}
if ($state == 'gender') {
    echo GridView::widget([
            'dataProvider' => $provider,
            'toolbar' => [],
            'panel' => [
                'type' => GridView::TYPE_PRIMARY,
            ],
            'pjax' => true,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => 'No.',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'hsitecode',
                    'label' => 'Site ID',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'hptcode',
                    'label' => 'Participant ID',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'f2v1',
                    'label' => 'DVISIT',
                    'format' => ['raw'],
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'value' => function($model) {
                                    $f2v1 = $model['f2v1'];
                                    $dataid = $model['id_regis'];
                                    $target = base64_encode($model['ptid']);
                                    $ptid = $model['ptid'];
                                    return "<a  id = 'ccaModal'
                                                ezf_id = '1437377239070461301'
                                                dataid = '$dataid'
                                                comp_target = '1437725343023632100'
                                                target = '$target'
                                                comp_id_target = '1437725343023632100'
                                                read = '1'
                                                title='เปิดอ่านเท่านั้น'
                                                data-target='#cca-modal'>$f2v1</a>&nbsp;&nbsp;&nbsp;"
                                            ."<a  id = 'editcca'
                                                ezf_id = '1437377239070461301'
                                                dataid = '$dataid'
                                                ptid = '$ptid'
                                                data_url = '/teleradio/suspected/open-form?dataid=$dataid&ezf_id=1437377239070461301&ptid=$ptid'
                                                title='เปิดเพื่อแก้ไข และดูภาพ'><span class='glyphicon glyphicon-edit'></span></a>"
                                                ;                       
                                   }
                 ],
                [
                    'attribute' => 'f1v3',
                    'label' => 'Value',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
            ],
        ]);
 } 
 if ($state == 'cca02') {
   echo GridView::widget([
            'dataProvider' => $provider,
            'toolbar' => [],
            'panel' => [
                'type' => GridView::TYPE_PRIMARY,
            ],
            'pjax' => true,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => 'No.',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'hsitecode',
                    'label' => 'Site ID',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'hptcode',
                    'label' => 'Participant ID',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'f2v1',
                    'label' => 'Exam Date',
                    'format' => ['raw'],
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'value' => function($model) {
                                    $f2v1 = $model['f2v1'];
                                    $dataid = $model['id_cca02'];
                                    $target = base64_encode($model['ptid']);
                                    $ptid = $model['ptid'];
                                    return "<a  id = 'ccaModal'
                                                ezf_id = '1437619524091524800'
                                                dataid = '$dataid'
                                                comp_target = '1437725343023632100'
                                                target = '$target'
                                                comp_id_target = '1437725343023632100'
                                                read = '1'
                                                title='เปิดอ่านเท่านั้น'
                                                data-target='#cca-modal'>$f2v1</a>&nbsp;&nbsp;&nbsp;"
                                            ."<a  id = 'editcca'
                                                ezf_id = '1437619524091524800'
                                                dataid = '$dataid'
                                                ptid = '$ptid'
                                                data_url = '/teleradio/suspected/open-form?dataid=$dataid&ezf_id=1437377239070461301&ptid=$ptid'
                                                title='เปิดเพื่อแก้ไข และดูภาพ'><span class='glyphicon glyphicon-edit'></span></a>"
                                                ;                       
                                   }
                 ],
                [
                    'attribute' => 'data',
                    'label' => 'Value',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
            ],
        ]);
 } 
 if ($state == 'cca01') {
echo GridView::widget([
            'dataProvider' => $provider,
            'toolbar' => [],
            'panel' => [
                'type' => GridView::TYPE_PRIMARY,
                'heading' => $doctorname,
            ],
            'pjax' => true,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => 'No.',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'hsitecode',
                    'label' => 'Site ID',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'hptcode',
                    'label' => 'Participant ID',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                [
                    'attribute' => 'f2v1',
                    'label' => 'วันที่ Form Completed CCA-01 (Date)',
                    'format' => ['raw'],
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'value' => function($model) {
                                    $f2v1 = $model['f2v1'];
                                    $dataid = $model['id_cca01'];
                                    $target = base64_encode($model['ptid']);
                                    $ptid = $model['ptid'];
                                    return "<a  id = 'ccaModal'
                                                ezf_id = '1437377239070461302'
                                                dataid = '$dataid'
                                                comp_target = '1437725343023632100'
                                                target = '$target'
                                                comp_id_target = '1437725343023632100'
                                                read = '1'
                                                title='เปิดอ่านเท่านั้น'
                                                data-target='#cca-modal'>$f2v1</a>&nbsp;&nbsp;&nbsp;"
                                            ."<a  id = 'editcca'
                                                ezf_id = '1437377239070461302'
                                                dataid = '$dataid'
                                                ptid = '$ptid'
                                                data_url = '/teleradio/suspected/open-form?dataid=$dataid&ezf_id=1437377239070461302&ptid=$ptid'
                                                title='เปิดเพื่อแก้ไข และดูภาพ'><span class='glyphicon glyphicon-edit'></span></a>"
                                                ;                       
                                   }
                 ],
                [
                    'attribute' => 'data',
                    'label' => 'Value',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                 ],
                 [
                    'attribute' => 'confirm',
                    'label' => 'ผลการตรวจสอบ',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'value' => function($model){
                                    if ($model['data'] == 0)
                                        return "ไม่ผ่านการตรวจสอบ";
                                    else if ($model['data'] > 0)
                                        return "ผ่านการตรวจสอบ";
                                    else
                                        return "ข้อมูลยังไม่สมบูรณ์";
                    }
                 ],
            ],
        ]);
 } ?>
<div id="cca-modal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-body" id="body-cca">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
