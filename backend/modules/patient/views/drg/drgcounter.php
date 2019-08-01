<?php

use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;

backend\modules\ezforms2\classes\EzfStarterWidget::begin();
$this->title = Yii::t('app', 'Patient Lists');
?>
<div class="ordercounter-index">
  <div class="sdbox-header">
    <h3><?= Html::encode($this->title) ?></h3>
  </div>
  <?php
  yii\widgets\Pjax::begin(['id' => 'drgcounter-grid-pjax', 'timeout' => FALSE]);
  echo appxq\sdii\widgets\GridView::widget([
      'id' => 'drgcounter-grid',
      'panelBtn' => $this->render('_search_drgcounter', ['model' => $searchModel, 'reloadDiv' => '']),
      'dataProvider' => $dataProvider,
      'rowOptions' => function ($model) {
          return ['data-url' => Url::to(['/cpoe/cpoe/cpoe-view', 'ptid' => $model['ptid'], 'visitid' => $model['visit_id']
                  , 'visit_tran_id' => '', 'visit_type' => $model['visit_type']
                  , 'action' => 'que', 'modal' => 'modal-drgcounter'])];
      },
      'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          [
              'attribute' => 'pt_hn',
              'label' => 'HN'
          ], [
              'attribute' => 'fullname',
              'label' => Yii::t('patient', 'Name')
          ], [
              'attribute' => 'visit_type_name',
              'label' => Yii::t('patient', 'Type')
          ], [
              'attribute' => 'di_txt',
              'label' => Yii::t('patient', 'Diagnosis'),
              'value' => function($model) {
                  return strip_tags($model['di_txt']);
              },
          ], [
              'attribute' => 'diag_icd10',
              'label' => Yii::t('patient', 'ICD10')
          ], [
              'attribute' => 'icd9',
              'label' => Yii::t('patient', 'ICD9')
          ], [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{receive}',
              'buttons' => [
                  'receive' => function($url, $model) {

                      $html = Html::a('<i class="fa fa-user"></i> ' . Yii::t('patient', 'Edit'), '#', [
                                  'data-url' => Url::to(['/cpoe/cpoe/cpoe-view', 'ptid' => $model['ptid'], 'visitid' => $model['visit_id']
                                      , 'visit_tran_id' => '', 'visit_type' => $model['visit_type']
                                      , 'action' => 'que', 'modal' => 'modal-drgcounter']),
                                  'class' => 'btn btn-warning btn-xs btn-block',
                      ]);
                      return $html;
                  },
              ],
              'contentOptions' => ['style' => 'width:120px;text-align: center;'],
          ]
      ],
  ]);
  yii\widgets\Pjax::end();
  ?>
</div>
</div>

<?php
echo ModalForm::widget([
    'id' => 'modal-drgcounter',
    'size' => 'modal-xxl',
]);

$this->registerJs("       
    $('#drgcounter-grid-pjax').on('dblclick', 'tbody tr', function() {    
        var url = $(this).attr('data-url');
        modalEzformMain(url,'modal-drgcounter');   
    });	

    $('#drgcounter-grid-pjax').on('click', 'tbody tr td a', function() {
        var url = $(this).attr('data-url');
        modalEzformMain(url,'modal-drgcounter'); 

        return false;
    });
    
    $('#modal-drgcounter').on('hidden.bs.modal', function (e) {
        $('#modal-drgcounter .modal-content').html('');
        $.pjax.reload({container:'#drgcounter-grid-pjax',timeout: false});
    });

    $(document).on('show.bs.modal', '.modal', function (event) {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    $(document).on('hidden.bs.modal', '.modal', function (e) {
        var hasmodal = $('body .modal').hasClass('in');
        if (hasmodal) {
            $('body').addClass('modal-open');
        }
    }); 
");

backend\modules\ezforms2\classes\EzfStarterWidget::end();
?>