<?php
// start widget builder
use sjaakp\timeline\Timeline;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\models\TbdataAll;

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
$modelEzf = EzfQuery::getEzformOne($options['ezf_id']);
$model = new TbdataAll();
$model->setTableName($modelEzf->ezf_table);
$dataProvider = new \yii\data\ActiveDataProvider([
    'query' => $model->find(),
    'pagination' => false
]);
?>

<?php
//'latestStart' => 'post_start',
//'earliestEnd' => 'pre_stop',
//'description' => 'title',
//'text' => 'name',
//'caption' => 'title',
$attributes = [
    'id' => 'id',
    'start' => $options['sdate'],
];

if(isset($options['edate']) && !empty($options['edate'])){
    $attributes['edate'] = $options['edate'];
}
if(isset($options['text']) && !empty($options['text'])){
    $attributes['text'] = $options['text'];
}
if(isset($options['caption']) && !empty($options['caption'])){
    $attributes['caption'] = $options['caption'];
}
if(isset($options['description']) && !empty($options['description'])){
    $attributes['description'] = $options['description'];
}
	// define Timeline
	$t = Timeline::begin([
		'dataProvider' => $dataProvider,
		'attributes' => $attributes,
                'height'=> (isset($options['height']) && !empty($options['height']))?(int)$options['height']:400,
	]);

	// define main Band
	$t->band([
		'width' => '80%',
		'intervalUnit' => (isset($options['mUnit']) && !empty($options['mUnit']))?(int)$options['mUnit']:Timeline::WEEK,
		'intervalPixels' => 200,
		// layout not set, use default
	]);

	// define secundary Band
	$t->band([
		'width' => '20%',
		'intervalUnit' => (isset($options['sUnit']) && !empty($options['sUnit']))?(int)$options['sUnit']:Timeline::MONTH,
		'intervalPixels' => 200,
		'layout' => 'overview',
                'overview' => true,
	]);

	// complete definition
	Timeline::end();
?>
