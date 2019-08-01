<?php

$url = \yii\helpers\Url::to(['/patient/restful/print-appoint', 'app_id' => $dataApp['id']]);
$this->registerJS("
  myWindow = window.open('$url', '_blank');
  ");
?>