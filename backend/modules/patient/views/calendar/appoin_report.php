<?php

use yii\helpers\Html;

backend\modules\ezforms2\classes\EzfStarterWidget::begin();
$this->title = Yii::t('app', 'นัดหมายจากระบบ nHIS');
?>
<div class="report-body"> 
  <div class="sdbox-header">
    <h3><?= Html::encode($this->title) ?></h3>
  </div>
  <?= $this->render('_appoin_s', ['model' => $searchModel, 'ezf_id' => $ezf_id]) ?>
  <div class="report-content">

  </div>
  <div class="report-export">

  </div>
  
</div>
