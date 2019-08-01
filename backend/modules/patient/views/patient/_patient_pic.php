<?php
$textStyle = '';
foreach ($style as $key => $value) {
    $textStyle .= $key . ':' . $value . ';';
}

$img = ($dataProfile['pt_pic'] ? Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataProfile['pt_pic'] : Yii::getAlias('@storageUrl/images') . '/nouser.png');
?>
<img class="img-responsive img-rounded" src=<?= $img ?> alt="patient-pic" style="<?=$textStyle?>">


