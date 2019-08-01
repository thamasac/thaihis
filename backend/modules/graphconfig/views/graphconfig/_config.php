<?php
use \backend\modules\ezforms2\classes\EzfAuthFunc;
$this->title = Yii::t('graphconfig', 'Graph config');
?>
<div class="inv-main-create">
    <?php if(EzfAuthFunc::canManage($proj_id) || EzfAuthFunc::canReadWrite($proj_id)){ ?>
    <?= $this->render('_config_form', [
	    'data' => $data,
            'proj_id'=>$proj_id,
    ]) ?>
    <?php }  ?>
</div>
