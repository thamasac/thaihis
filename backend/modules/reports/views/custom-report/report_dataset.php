<?php

use yii\helpers\Html;
$this->title = Yii::t('chanpan','Custom Print Dataset');
?>
<div class="row">
    <div class="panel">
        <div class="panel-body">
            <h3><?= $this->title;?></h3>
            <div class="clearfix"></div>
            <div class="col-md-4">
                <div>
                    <?= Html::label('Sql Command') ?>
                    <?= Html::textarea('sql_command', '', ['class' => 'form-control', 'rows'=>'5' , 'id'=>'sql_command']) ?>
                    <br/>
                    <div class="col-md-6 col-md-offset-3">
                        <?= Html::button(Yii::t('chanpan', 'Process'), ['class' => 'btn btn-primary btn-block btn-lg btnProcess']) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr/>
                <div id="var-output"></div>
            </div>
            <div class="col-md-8">
                <div class="" style="display: block;font-size:10pt;margin-bottom:15px;padding:10px;">* <?= Yii::t('report', 'Click ปุ่มสีส้มเพื่อเลือกตัวแปรลง Editor') ?></div>
                <div id="editor">
                    <?=
                    \appxq\sdii\widgets\FroalaEditorWidget::widget([
                        'name' => 'template_name',
                        'value' => isset($dataTemplate['template']) ? $dataTemplate['template'] : '',
                        //'id'=>'froala-editor',
                        'options' => ['id' => 'custom_template'],
                        'clientOptions' => [
                            'zIndex' => 1,
                            'height' => '300',
                            //'theme' => 'gray', //optional: dark, red, gray, royal
                            'language' => 'th',
                        ]
                    ]);
                    ?>
                </div>
                <br>
                <div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?= Yii::t('chanpan','Paper Size')?></label>
                                <input class="form-control" type="text" id="page_size" value="150,80">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?= Yii::t('chanpan','Layout')?></label>
                                <?php 
                                    $items=['P'=>'vertical','L'=>'Horizontal'];
                                    echo Html::dropDownList('layout', '', $items,['class'=>'form-control', 'id'=>'layout']);
                                ?>
                            </div>
                            
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-success btn-lg btnPrint btn-block"><i class="glyphicon glyphicon-print"></i> <?= Yii::t('report', 'Print')?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php richardfan\widget\JSRegister::begin();?>
<script>
    
    var paper_size = [
      "150,80",
      "A0",
      "A1",
      "A2",
      "A3",
      "A4",
      "A5","A6","A7","A8","A9","A10",
    ];
    $( "#page_size" ).autocomplete({
      source: paper_size
    });

    
    $('.btnProcess').on('click', function(){
        let sql_command = $('#sql_command').val();
        let url = '<?= yii\helpers\Url::to(['/reports/custom-report/process-command'])?>';
        $.post(url, {sql_command:sql_command} , function(res){
            if(res.status == 'success'){
                <?= \appxq\sdii\helpers\SDNoty::show('res.message','res.status')?>
                getData(res['data']);
            }
        })
        return false;
    });
    $('.btnPrint').on('click', function(){
        let template = $("#custom_template").val();
        let layout = $('#layout').val();
        let page_size = $('#page_size').val();
        let sql_command = $('#sql_command').val();
        
        let params = {sql_command:sql_command,page_size:page_size,layout:layout,template:template};
        let url = '<?= yii\helpers\Url::to(['/reports/custom-report/print-command'])?>';
        $.post(url, params, function(data){
            data = JSON.parse(data);
            if(data['success'] == true){
                let url = `${data['data']['path']}/${data['data']['fileName']}`
               // console.log(url);
                window.open(url , '_blank');
            }
        }).fail(function(err){
            console.log(err);
         });
        
         
        
       // window.open(url , '_blank');
        return false;
    });
    function getData(data){
       
        let key = Object.keys(data);
        for(let i of key){
            //console.log(data[i]);
            $('#var-output').append(`
                ${data[i]} <button  draggable="true"  data-value="${i}" class="btn btn-warning btn-sm  btnVar pull-right">${i}</button>
                <br>
                <br>
            `);
        }
        setVarToEditor();
        return false;
    }
    function setVarToEditor(data){
        $('.btnVar').on('click', function(){
           let val = $(this).attr('data-value');
           val = '{'+val+'}';
           $("#custom_template").froalaEditor('html.insert', val, true);
          // console.log(val); 
        });
        return false;
    }
    
    //SELECT * FROM dynamic_db WHERE id = 1
    
</script>
<?php richardfan\widget\JSRegister::end();?>

