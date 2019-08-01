<?php
// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
'widget_config' => $widget_config,
'model' => $model,
'modelOrigin'=>$modelOrigin,
'menu' => $menu,
'module' => $module,
'addon' => $addon,
'filter' => $filter,
'reloadDiv' => $reloadDiv,
'dataFilter' => $dataFilter,
'modelFilter' => $modelFilter,
'target' => $target,
    */

use backend\modules\ezforms2\classes\EzfQuery;
use yii\db\Query;

$target = Yii::$app->request->get("target",'');
$ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : '0';
$ezf_field = isset($options['fields']) ? $options['fields'] : '';
$table =  EzfQuery::getEzformOne($ezf_id);
$imgUrl = '';
if( $ezf_field != '' && $target != ''){
    $filePath = (new Query())->select($ezf_field)->from($table["ezf_table"])->where(['ptid' => $target])->scalar();
    $imgPath = \Yii::getAlias('@storageUrl');
    $imgUrl = "{$imgPath}/ezform/fileinput/{$filePath}";
}
?>
<style>
    .image-view-img{
        width: 100% !important;
        max-width: 256px !important;
        margin: 0 auto;
        box-shadow:0 10px 16px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19) !important;
    }

    .image-view-box{
        text-align:center;
    }
</style>
<div class="image-view-box" >
    <img class="media-object image-view-img"
         src="<?= $imgUrl ?>"
    >
</div>
