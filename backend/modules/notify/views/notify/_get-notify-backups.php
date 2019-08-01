<?php

use appxq\sdii\helpers\SDNoty;
use yii\helpers\Url;
?>
<div class="list-group list-group-flush" style="word-wrap: break-word;">
    <?php
    foreach ($data as $value) {
        ;
        ?>
        <a  href="<?= Url::to(['/ezmodules/ezmodule/view?id=1520785643053421500']); ?>"
            class="btnViewNotify list-group-item <?= $value['status_view'] == 0 ? 'list-group-item-info' : '' ?>" >
            <p><strong><?= $value['notify'] ?></strong></p>
            <p><small><?= $value['detail'] ?></small></p>
            <p><small><?= $value['create_date'] ?></small></p>
        </a>
        <?php
    }
    ?>
</div>
