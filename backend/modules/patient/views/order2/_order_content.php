<?php

use backend\modules\patient\classes\Order2Helper;
?>
<div class="col-md-9">
    <?= Order2Helper::uiOrderLists($visit_id, $visit_type, $dept, $order_status, 'order-data-lists'); ?>
</div>
<div class="col-md-3 sdbox-col">
  <?= Order2Helper::uiOrderHistoryVisit($pt_id, $dept, 'order-history-lists'); ?> 
</div>