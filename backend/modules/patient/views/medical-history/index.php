<?php

use backend\modules\patient\classes\PatientHelper;
use backend\modules\ezforms2\classes\EzfStarterWidget;

\backend\modules\patient\assets\PatientAsset::register($this);

EzfStarterWidget::begin();
?>
<div class="row" style="margin-top: 15px;">
    <div class="col-md-2">
        <?= PatientHelper::listVisitHospital($dataid, 'list-visithospital', 'list-visit'); ?>
    </div>
    <div class="col-md-2 sdbox-col" >
        <div id="list-visit">

        </div>
    </div>
    <div class="col-md-8 sdbox-col">
        <div id="view-detail">

        </div>
    </div>
</div>
<?php
EzfStarterWidget::end();
?>