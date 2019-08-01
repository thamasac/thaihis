<?php

$url = \yii\helpers\Url::to(['/patient/restful/print-report-cer', 'cer_id' => $dataid,'target' => $target,'options'=> \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options)]);
$this->registerJS("
  myWindow = window.open('$url', '_blank');
  ");
?>