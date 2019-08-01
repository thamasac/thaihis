<?php

$url = \yii\helpers\Url::to(['/patient/restful/print-discharge', 'visit_id' => $visit_id]);
$this->registerJS("
  myWindow = window.open('$url', '_blank');
  ");
?>