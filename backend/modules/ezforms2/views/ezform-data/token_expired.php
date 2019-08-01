<?php

use appxq\sdii\helpers\SDHtml;
use yii\helpers\Html;

$msg = \backend\modules\core\classes\CoreQuery::getOptions('token_expired');
?>

<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin(); ?>

<?php backend\modules\ezforms2\classes\EzfStarterWidget::end(); ?>

<div class="container-fluid" style="margin-top: 15px;margin-bottom: 15px;">
    <?=$msg->option_value?>
</div>
