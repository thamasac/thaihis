<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

\appxq\sdii\assets\VisAsset::register($this);

$node = [];
$edges = [];

$company_name = isset(Yii::$app->params['company_name'])?Yii::$app->params['company_name']:'Organize Chart';

$modelNode = backend\modules\ezforms2\classes\EzfQuery::getUnitParent();
$node[] = ['id'=>"00", 'shape'=>'box', 'margin'=>10, 'label'=>$company_name];

if(isset($modelNode)){
    foreach ($modelNode as $key => $value) {//shape=>circularImage, image
        $unit_parent = isset($value['unit_parent']) && !empty($value['unit_parent'])?$value['unit_parent']:'00';
        $node[] = ['id'=>"{$value['id']}", 'shape'=>'box', 'group'=>$unit_parent, 'margin'=>10, 'font'=>['multi'=> true, 'align'=>'left' ], 'label'=>"<b>Unit:</b> {$value['text']}\n<b>Type:</b> {$value['unit_type']}"];
        $edges[] = ['from'=>"{$unit_parent}", 'to'=>"{$value['id']}"];
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
    <h4 class="modal-title" id="itemModalLabel"><?=Yii::t('ezform', 'Organization Chart')?></h4>
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
          borderWidth:1,
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
                nodeSpacing: 200,
                levelSeparation: 200,
            }
        },
        physics: {
            enabled: false
        },
      };
    var network = new vis.Network(container, data, options);
</script>
<?php \richardfan\widget\JSRegister::end(); ?>