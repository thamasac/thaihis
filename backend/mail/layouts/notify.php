<h1><?= $notify ?></h1>

<p><?= $detail ?></p>
<?php
//$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
$reg_exUrl = "/(http|https)\:\/\/?/";
$textUrl = '';
if (preg_match($reg_exUrl, $url)) {
    $textUrl = $url;
} else {
    $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $textUrl = $http . $_SERVER['SERVER_NAME'] . $url;
}

if($disableDetailFooter == false){
    echo Yii::t('notify', 'See detail')." : ". \yii\helpers\Html::a(Yii::t('notify', 'Click here'), $textUrl, isset($options) ? $options : []);
}
?>