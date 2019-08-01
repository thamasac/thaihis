<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;

$this->title = Yii::t('app', 'Dashboard Admit');
backend\modules\ezforms2\classes\EzfStarterWidget::begin();
?>
<div class="cashiercounter-index"> 
  <div class="sdbox-header">
    <h3><?= $this->title ?></h3>
  </div>
  <?php
  Pjax::begin(['id' => 'das-admit-grid-pjax', 'timeout' => FALSE]);
  echo GridView::widget([
      'id' => 'das-admit-grid',
      'panelBtn' => $this->render('_searchreport', ['model' => $searchModel]),
      //'filterModel' => $searchModel,
      'dataProvider' => $dataProvider,
//      'rowOptions' => function ($model) {
//          return [
//              'data' => ['key' => $model['report_id'], 'hn' => $model['pt_hn'], 'visit_id' => $model['visit_id']],
//          ];
//      },
      'columns' => [
          [
              'attribute' => 'pt_hn',
              'label' => 'HN'
          ],
          [
              'attribute' => 'admit_an',
              'label' => 'AN'
          ],
          [
              'attribute' => 'fullname',
              'label' => Yii::t('patient', 'Name')
          ],
          [
              'attribute' => 'bed',
              'label' => Yii::t('patient', 'Bed')
          ],
          [
              'attribute' => 'sect_name',
              'label' => Yii::t('patient', 'Ward')
          ],          
          [
              'attribute' => 'admit_date',
              'format' => ['date', 'php:d/m/Y H:i'],
              'label' => Yii::t('patient', 'Date'),
              'contentOptions' => ['style' => 'width:150px;text-align: center;'],
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
?>
