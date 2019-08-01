<?php
use yii\helpers\Html;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfFunc;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="print-<?= $modelEzf->ezf_id ?>" style="background-color: #fff; border-radius: 6px;">
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="itemModalLabel"> <?=$modelEzf->ezf_name?> 
         <?php if($modelEzf->enable_version==1){
                $model_version = backend\modules\ezforms2\classes\EzfQuery::getEzformVersionApprovList($ezf_id);
                ?>
        <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                 <?=($modelVersion['ver_active']==1?'<span class="glyphicon glyphicon-star" aria-hidden="true"></span>':'')?> <?=$modelVersion['ver_code']?>  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" >
                  <?php
                  if(isset($model_version) && !empty($model_version)){
                      foreach ($model_version as $key => $value) {
                          ?>
                  <li class="<?=($value['ver_code']==$modelVersion['ver_code'])?'active':''?>"><a class="ezform-main-open" data-modal="<?=$modal?>" data-url="<?= \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform-dictionary', 
                          'ezf_id'=>$ezf_id,
                          'v'=>$value['ver_code'],  
                          'modal'=>$modal,
                          'reloadDiv'=>$reloadDiv,
                          ])?>">
                                       <?=($value['ver_active']==1?'<span class="glyphicon glyphicon-star" aria-hidden="true"></span>':'')?> <?=$value['ver_code']?> 
                          </a></li>
                          <?php
                      }
                  }
                  ?>
                </ul>
              </div>
        <?php } else {
                echo '<span class="label label-default">'.$modelVersion['ver_code'].'</span>';
            }
            ?>
        </h3>
         
    </div>

<div class="modal-body">
  
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
                'header' => 'Field',
                'value' => function ($field) use($modelFields, $modelEzf) {
                    if (isset(Yii::$app->session['ezf_input']) && !empty(Yii::$app->session['ezf_input'])) {
                        $ezf_input = Yii::$app->session['ezf_input'];
                        $data = SDUtility::string2Array($field['ezf_field_data']);
                        $dataItems = [];
                        $value_label = '';
                        
                        $dataInput;
                        if (isset($ezf_input)) {
                            $dataInput = EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                        }
                        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, $ezf_input, 0);
                        
                        if (isset($dataInput) && !empty($data)) {
                            
                            if (isset($data['func'])) {
                                try {
                                    $params = [
                                        'field'=>$field,
                                        'data'=>$model
                                    ];
                                    eval("\$dataItems = {$data['func']};");
                                } catch (\yii\base\Exception $e) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                }
                            } else {
                                $dataItems = $data['items'];
                            }

                            if(isset($dataItems) && !empty($dataItems)){
                                if(isset($dataItems['data'])){
                                    $value_label = EzfFunc::export_obj($dataItems['data']);
                                } else {
                                    $value_label = EzfFunc::export_obj($dataItems);
                                }
                            }
                        }
                    }
                    return " <code>{$field['ezf_field_name']} {$value_label}</code>";
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:190px; '],
            ],
            [
                'header' => 'Label',
                'value' => function ($data) {
                    return $data['ezf_field_label'];
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:250px; '],
            ],
            [
                'header' => 'Hint',
                'value' => function ($data) {
                    return $data['ezf_field_hint'];
                },
                'format' => 'raw',
            ],
            [
                'header' => 'Required',
                'value' => function ($data) {
                    return $data['ezf_field_required']==1?'<code>Yes</code>':'No';
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:80px; '],
            ],            
            [
                'header' => 'Index',
                'value' => function ($data) {
                    return $data['table_index']==1?'<code>Yes</code>':'No';
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:80px; '],
            ],
            [
                'header' => 'Target',
                'value' => function ($data) {
                    return $data['ezf_target']==1?'<code>Yes</code>':'No';
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:80px; '],
            ],  
        ],
    ]);
    ?>
</div>
</div>
<div class="modal-footer">
    <?php
    echo Html::button('<i class="glyphicon glyphicon-print"></i>', [
        'id'=>'h2c',
        'class'=>'btn btn-default ', 
        'target'=>'_blank', 
    ]);     
    ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    $('#<?=$modal?> .modal-dialog').width('');
    
    $('#h2c').click(function(){
        let win=window.open();
        win.document.write('<html><head><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"></head><body>');
        win.document.write($('#print-<?= $modelEzf->ezf_id ?>').html());
        win.document.write('</body></html>');
        win.print();
        win.close();
    });
       
</script>
<?php \richardfan\widget\JSRegister::end(); ?>