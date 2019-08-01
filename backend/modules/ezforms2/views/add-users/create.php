<?php

use yii\helpers\Html;
$title = "";
if($db_type == "tcc"){
    $title =  Yii::t('chanpan', 'Import Members from Thai Care Cloud');
}else if($db_type == "ncrc"){
    $title = Yii::t('chanpan', 'Import Members from nCRC');
}else{
    $title = Yii::t('chanpan', 'Import Members from Thai Care Cloud');
} 
 
$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="modal-header">
    <?= Html::encode($this->title)?>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <?=
    $this->render('_form', [
        'model' => $model,
        'status' => '1',
        'db_type' => $db_type,
        'dataUser' => $dataUser
    ])
    ?>
</div>
