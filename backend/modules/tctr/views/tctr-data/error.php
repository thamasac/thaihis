<?php

use appxq\sdii\helpers\SDHtml;
use yii\helpers\Html;
?>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin(); ?>

<?php backend\modules\ezforms2\classes\EzfStarterWidget::end(); ?>
<div class="container-fluid">
    <div class="modal-header">
        <h3 class="modal-title" id="itemModalLabel"><?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small></h3>
    </div>
    <div class="modal-body">
        <div class="alert alert-warning" role="alert"> <?= SDHtml::getMsgWarning() ?> <?= $msg ?></div>
    </div>
</div>

