<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$ezf_input = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
?>
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Mismatched Result')?>  </h4>
    </div>

<div class="modal-body">
  <dl class="dl-horizontal">
  <dt>Form</dt>
  <dd><?=$ezform->ezf_name?></dd>
</dl>
  <dl class="dl-horizontal">
  <dt>Link ID</dt>
  <dd><code><?=$dataid?></code></dd>
</dl>
    <?php if(!empty($fieldList)){?>
  <?=
        \yii\grid\GridView::widget([
        'id' => 'compare-fields-grid',
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped '],    
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:60px;text-align: center;'],
            ],
            [
                'header' => 'Fields',
                'value' => function ($data) {
                    return $data['ezf_field_label']." <code>{$data['ezf_field_name']}</code>";
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:250px; '],
            ],
            [
                'header' => 'Key Operator 1',
                'value' => function ($data) use ($data1, $data2, $ezf_input, $modelFields) {
                    foreach ($modelFields as $key => $field) {
                        $var = $field['ezf_field_name'];
                        $version = $field['ezf_version'];
                        if($data['ezf_field_name'] == $var && ($data1['ezf_version'] == $version || $version=='all')){
                            if ($ezf_input) {
                                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                            }
                            return backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data1);
                        }
                    }
                    return NULL;
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:150px; text-align: center;'],
            ],
            [
                'header' => 'Key Operator 2',
                'value' => function ($data) use ($data1, $data2, $ezf_input, $modelFields) {
                    foreach ($modelFields as $key => $field) {
                        $var = $field['ezf_field_name'];
                        $version = $field['ezf_version'];
                        if($data['ezf_field_name'] == $var && ($data2['ezf_version'] == $version || $version=='all')){
                            if ($ezf_input) {
                                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                            }
                            return backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data2);
                        }
                    }
                    return NULL;
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:150px; text-align: center;'],
            ],
            [
                'header' => 'Query',
                'value' => function ($data) use($data1, $data2){
                    $html = Html::button('Key Operator 1', [
                        'class'=>'btn btn-warning btn-xs btn-querytool',
                        'data-url'=>Url::to(['/ezforms2/ezform-community/query-tool', 
                            'modal'=>'modal-ezform-community', 
                            'dataid'=>$data1->id, 
                            'object_id'=>$data['ezf_id'], 
                            'query_tool'=>1, 
                            'field'=>$data['ezf_field_name'], 
                            'type'=>'query_tool', 
                            'value_old'=>$data1[$data['ezf_field_name']]
                           ]),
                        ]).' ';
                    
                    $html .= Html::button('Key Operator 2', [
                        'class'=>'btn btn-warning btn-xs btn-querytool',
                        'data-url'=>Url::to(['/ezforms2/ezform-community/query-tool', 
                            'modal'=>'modal-ezform-community', 
                            'dataid'=>$data2->id, 
                            'object_id'=>$data['ezf_id'], 
                            'query_tool'=>1, 
                            'field'=>$data['ezf_field_name'], 
                            'type'=>'query_tool', 
                            'value_old'=>$data2[$data['ezf_field_name']]
                           ]),
                        ]);
                    
                        return $html;
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:250px; text-align: center;'],
            ],
        ],
    ]);
    ?>
    <?php } ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>