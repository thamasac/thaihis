<?php
use yii\helpers\Html;
$this->title = Yii::t('app', 'Report Admin');
?>
<div class="report-body"> 
  <div class="sdbox-header">
    <h3><?= Html::encode($this->title) ?></h3>
  </div>
  <?= $this->render('_search', ['model' => $searchModel]) ?>
  <div class="report-content">

  </div>
  <div class="report-export">

  </div>
</div>