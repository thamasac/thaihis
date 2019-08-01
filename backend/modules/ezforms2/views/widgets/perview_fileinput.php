<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (isset($fileArr) && is_array($fileArr)) {

    foreach ($fileArr as $key => $value) {
        $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        if ($ext == 'pdf') {
            echo '<embed  src="'.$path . $value.'" type="application/pdf" style="width: 100%;height: 640px;">';
        } elseif (in_array($ext, ['doc', 'docx'])) {
            echo '<iframe style="width: 100%;height: 640px;" src="https://view.officeapps.live.com/op/embed.aspx?src='.$path . $value.'"></iframe>';
        } elseif (in_array($ext, ['xls', 'xlsx'])) {
            echo '<iframe style="width: 100%;height: 640px;" src="https://view.officeapps.live.com/op/embed.aspx?src='.$path . $value.'"></iframe>';
        } elseif (in_array($ext, ['ppt', 'pptx'])) {
            echo '<iframe style="width: 100%;height: 640px;" src="https://view.officeapps.live.com/op/embed.aspx?src='.$path . $value.'"></iframe>';
        } elseif (in_array($ext, ['png','jpg','jpeg'])) {
            echo yii\helpers\Html::img($path . $value, ['class' => 'img-thumbnail']);
        } else {
            echo yii\helpers\Html::img(Yii::getAlias('@storageUrl') . '/ezform/img/unknow_icon.png', ['class' => 'img-thumbnail']);
        }
            
        
    }
}
?>
