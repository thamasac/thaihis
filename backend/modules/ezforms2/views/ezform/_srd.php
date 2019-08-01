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
$staple_fields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_field_type in(84, 92, 91) AND ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all();
$sub_staple = [];
if($staple_fields){
    foreach ($staple_fields as $key => $value) {
        $staple_id = 0;
        $options = \appxq\sdii\utils\SDUtility::string2Array($value['ezf_field_options']);
        if(isset($options['data-id'])){
            $staple_id = $options['data-id'];
        } elseif (isset($options['options']['data-id'])) {
            $staple_id = $options['options']['data-id'];
        }
        
        
        $model_auto = \backend\modules\ezforms2\models\EzformAutonum::findOne($staple_id);
        if($model_auto){
            $parent = $model_auto['ezf_id'];
            $field_main = \backend\modules\ezforms2\classes\EzfQuery::getFieldById($model_auto['ezf_field_id']);
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($parent);
            $img = Yii::getAlias('@storageUrl/ezform/img/icon_empty_ezform.png');
            if(isset($ezform['ezf_icon']) && !empty($ezform['ezf_icon'])){
                $img = $ezform['ezf_icon'];
            }
            
            if(!in_array($ezform['ezf_id'], $sub_staple)){
                $node[] = ['id'=>"{$ezform['ezf_id']}", 'shape'=>'circularImage', 'image'=> $img, 'label'=>$ezform['ezf_name']];
                $sub_staple[] = $ezform['ezf_id'];
            }
            
            
            $modelNode = backend\modules\ezforms2\classes\EzfQuery::getEzformStapleList($staple_id);
            if(isset($modelNode)){
                foreach ($modelNode as $key_sub => $value_sub) {//shape=>circularImage, image
                    $img = Yii::getAlias('@storageUrl/ezform/img/icon_empty_ezform.png');
                    if(isset($value_sub['ezf_icon']) && !empty($value_sub['ezf_icon'])){
                        $img = $value_sub['ezf_icon'];
                    }
                    if(!in_array($value_sub['ezf_id'], $sub_staple)){
                        $node[] = ['id'=>"{$value_sub['ezf_id']}", 'shape'=>'circularImage', 'image'=> $img, 'label'=>$value_sub['ezf_name']];
                        $sub_staple[] = $value_sub['ezf_id'];
                    }
                    
                    $edges[] = ['from'=>"{$ezform['ezf_id']}", 'to'=>"{$value_sub['ezf_id']}", 'label'=>$field_main['ezf_field_name'].' => '.$value['ezf_field_name'], 'font'=>['align'=>'middle', 'color'=>'#FF0000']];
                }
            }
            
        }
        
    }
    //data-id -> create
    //data-auto ->referent
}

$dataSet = [
    'nodes'=> $node,
    'edges'=> $edges,
];

//appxq\sdii\utils\VarDumper::dump(yii\helpers\ArrayHelper::map($modelJoin, 'ezf_id', 'ref_ezf_id'));
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel"><?=Yii::t('ezform', 'Staple Relation Diagram')?></h4>
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
//                sortMethod: "directed",
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