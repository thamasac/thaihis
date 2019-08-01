<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;

$this->title = Yii::t('app', 'Report print');
backend\modules\ezforms2\classes\EzfStarterWidget::begin();
?>
<div class="cashiercounter-index"> 
  <div class="sdbox-header">
    <h3><?= $this->title ?></h3>
  </div>
  <?php
  Pjax::begin(['id' => 'reportcounter-grid-pjax', 'timeout' => FALSE]);
  echo GridView::widget([
      'id' => 'reportcounter-grid',
      'panelBtn' => $this->render('_searchreport', ['model' => $searchModel]),
      //'filterModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'rowOptions' => function ($model) {
          return [
              'data' => ['key' => $model['report_id'], 'hn' => $model['pt_hn'], 'visit_id' => $model['visit_id']],
          ];
      },
      'columns' => [
          [
              'attribute' => 'pt_hn',
              'label' => 'HN'
          ],
          [
              'attribute' => 'fullname',
              'label' => Yii::t('patient', 'Name')
          ],
          [
              'attribute' => 'pt_email',
              'label' => Yii::t('patient', 'Mail')
          ],
          [
              'attribute' => 'doctor_name',
              'label' => Yii::t('patient', 'Doctor'),
              'value' => function ($model) {
                  if ($model['ckr_status'] == '1') {
                      $result = \appxq\sdii\utils\SDdate::mysql2phpDate($model['visit_date'], '-');
                  } else {
                      $result = $model['doctor_name'];
                  }
                  return $result;
              },
          ],
          [
              'attribute' => 'update_date',
              'format' => ['date', 'php:d/m/Y H:i'],
              'label' => Yii::t('patient', 'Date'),
              'contentOptions' => ['style' => 'width:150px;text-align: center;'],
          ],
          [
              'attribute' => 'ckr_status',
              'label' => Yii::t('patient', 'status'),
              'format' => 'html',
              'value' => function($model) {
                  return ($model['ckr_status'] == '4' ?
                          '<span class="btn btn-success btn-block btn-xs fa fa-check-square"></span> ' :
                          '<span class="btn btn-danger btn-block btn-xs fa fa-window-close"></span>');
              },
              'contentOptions' => ['style' => 'width:50px;text-align: center;'],
          ],
          [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{receive}',
              'buttons' => [
                  'receive' => function($url, $model) {
                      if ($model['ckr_status'] == '1') {
                          $html = '';
//                          $html = Html::a('<i class="fa fa-eye"></i> ' . Yii::t('patient', 'Order'), '#', [
//                                      'data-action' => 'print',
//                                      'data-key' => $model['report_id'],
//                                      'data-hn' => $model['pt_hn'],
//                                      'data-visit' => $model['visit_id'],
//                                      'data-report-status' => '1',
//                                      'class' => 'btn btn-primary btn-xs btn-block',
//                          ]);
                      } else {
                          $html = Html::a('<i class="fa fa-print"></i> ' . Yii::t('patient', 'Print'), '#', [
                                      'data-action' => 'print',
                                      'data-key' => $model['report_id'],
                                      'data-hn' => $model['pt_hn'],
                                      'data-visit' => $model['visit_id'],
                                      'data-report-status' => '3',
                                      'class' => 'btn btn-primary btn-xs btn-block',
                                  ]) . ' ' . $html = Html::a('<i class="fa fa-reply-all"></i> ' . 'ส่งกลับแพทย์', '#', [
                                      'data-action' => 're2doc',
                                      'data-key' => $model['report_id'],
                                      'data-hn' => $model['pt_hn'],
                                      'data-visit' => $model['visit_id'],
                                      'data-report-status' => '1',
                                      'class' => 'btn btn-warning btn-xs btn-block',
                          ]);
                      }


                      return $html;
                  },
              ],
              'contentOptions' => ['style' => 'width:120px;text-align: center;'],
          ],
      ],
  ]);
  ?>
  <?php
  Pjax::end();
  ?>
</div>
<?php
backend\modules\ezforms2\classes\EzfStarterWidget::end();
$url = Url::to(['/reports/report-checkup-send/print-report', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'dataid' => '']);
//if ($searchModel['ckr_status'] == '1') {
$urlOrder = Url::to(['/patient/order/grid-order', 'ezf_id' => $ezfOrder_id]);
$addon = "var url = '$urlOrder' +'&target='+ $(this).attr('data-visit_id');";
//} else {
//    $urlForm = Url::to(['/ezforms2/ezform-data/ezform-view', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'dataid' => '']);
//    $addon = "var url = '$urlForm' + dataid +'&pt_hn='+ $(this).attr('data-hn');";
//}
$urlStatus = Url::to(['/reports/report-checkup/report-save-approve']);
$this->registerJs("
$('#reportcounter-grid-pjax').on('dblclick', 'tbody tr', function() {
        var dataid = $(this).attr('data-key');
        $addon
        modalEzformMain(url,'modal-ezform-main');  
});	

$('#reportcounter-grid-pjax').on('click', 'tbody tr td a', function() {
        var report_status = $(this).attr('data-report-status');
        var report_id = $(this).attr('data-key');
        var report_action = $(this).attr('data-action');
        var pt_hn = $(this).attr('data-hn');
        var visit_id = $(this).attr('data-visit');
         $.get('$urlStatus',{report_status:report_status,report_id:report_id}).done(function(result) {
         if(report_action === 'print'){
            var url = '$url' + report_id+'&pt_hn='+ pt_hn+'&visit_id='+visit_id;
            myWindow = window.open(url, '_blank');
            myWindow.focus();
            myWindow.print();            
         }   
         window.location.reload();
        });        
    return false;
});

$('#modal-ezform-main .modal-content').on('click', 'tbody tr td a', function () {
      var url = $(this).attr('data-url');
      modalEzformMain(url,'modal-ezform-main');
      return false;
});

");
?>
