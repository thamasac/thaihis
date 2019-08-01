<?php
$visit_id = Yii::$app->request->get('visitid', '');
$target = Yii::$app->request->get('target', '');

if ($target != '') {
    echo backend\modules\thaihis\classes\ThaiHisHelper::btnAppointment(isset($options['ezf_id']) ? $options['ezf_id'] : '', $visit_id, $target, $options);
}
?>

