<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

\appxq\sdii\assets\VisAsset::register($this);

$node = [];
$edges = [];

$parent = $ezf_id;
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOneEzform($ezf_id);
if($target){
    $parent = $target['parent_ezf_id'];
}

$modelNode = backend\modules\ezforms2\classes\EzfQuery::getEzformList($parent);
if(isset($modelNode)){
    foreach ($modelNode as $key => $value) {//shape=>circularImage, image
        $img = Yii::getAlias('@storageUrl/ezform/img/icon_empty_ezform.png');
        if(isset($value['ezf_icon']) && !empty($value['ezf_icon'])){
            $img = $value['ezf_icon'];
        }
        $node[] = ['id'=>"{$value['ezf_id']}", 'shape'=>'circularImage', 'image'=> $img, 'label'=>$value['ezf_name']];
    }
}

$modelEdges = backend\modules\ezforms2\classes\EzfQuery::getJoinFieldsAll($parent);
if(isset($modelEdges)){
    foreach ($modelEdges as $key => $value) {
        $edges[] = ['from'=>"{$value['ref_ezf_id']}", 'to'=>"{$value['ezf_id']}", 'label'=>$value['ezf_field_name'], 'font'=>['align'=>'middle']];
    }
}

$dataSet = [
    'nodes'=> $node,
    'edges'=> $edges,
];

//appxq\sdii\utils\VarDumper::dump(yii\helpers\ArrayHelper::map($modelJoin, 'ezf_id', 'ref_ezf_id'));
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel"><?=Yii::t('ezform', 'Entity Relation Diagram')?></h4>
</div>
  <div class="modal-body">
    <div id="network-er" style="width: 100%; height: 650px;"></div>
</div>


<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    
    var data = <?= yii\helpers\Json::encode($dataSet)?>;
    // create a network
    var container = document.getElementById('network-er');
    var options = {
       nodes: {
          borderWidth:4,
          size:20,
//          color: {
//            border: '#444',
//            background: '#eee'
//          },
        },
        layout: {
            hierarchical: {
                direction: "UD",
                sortMethod: "directed",
                nodeSpacing: 250,
                levelSeparation: 250,
            }
        },
        physics: {
            enabled: false
        },
      };
    var network = new vis.Network(container, data, options);
</script>
<?php \richardfan\widget\JSRegister::end(); ?>