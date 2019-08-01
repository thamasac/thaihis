<?php
use yii\helpers\Url;

$current_url = strrpos(Url::current(),'&target') > 0 ? substr(Url::current(), 0,strrpos(Url::current(),'&target')) : Url::current();
$visit_tran_id = Yii::$app->request->get('visit_tran_id', '');
$visit_type = Yii::$app->request->get('visit_type', '');
$reloadDiv = isset($options['reloadDiv']) ? $options['reloadDiv'] : 'visit-'.\appxq\sdii\utils\SDUtility::getMillisecTime();

$content = backend\modules\thaihis\classes\ThaiHisHelper::btnCloseVisit($visit_tran_id, $visit_type, $reloadDiv,$current_url,$options);
//if ($visit_tran_id) {
    echo $content;
//}
?>

