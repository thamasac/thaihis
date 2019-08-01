<?php
use yii\helpers\Url;
$department = Yii::$app->user->identity->profile->department;
$target = Yii::$app->request->get('target', '');

echo \backend\modules\patient\classes\PatientHelper::btnCertificate($target, $target, 'btn-certificate', $department,$options);
?>

