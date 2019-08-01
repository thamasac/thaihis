
<?php

$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
$options['fix_grid_ipd'] = TRUE;
$options['fix_grid_ipd_date'] = $model['order_date'];
echo backend\modules\thaihis\classes\ThaiHisHelper::uiGridOrder(
        'ezf_id', $visit_id, 'grid-order-tran' . $id, FALSE, $options)
?>   
