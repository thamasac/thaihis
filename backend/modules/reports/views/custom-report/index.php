<?php

use yii\helpers\Url;
use yii\widgets\ListView;
use kartik\tabs\TabsX;
//phpinfo();

$this->title = Yii::t('report', 'Custom Report');
cpn\chanpan\assets\tour\BootstrapTourAssets::register($this);
// appxq\sdii\utils\VarDumper::dump($dataProvider);

$items = [
    [
        'label' => '<i class="fa fa-code"></i> ' . Yii::t('report', 'Valiable'),
        'content' =>$this->render("_items", ['data' => $data, "key" => ""]),
        'active' => true
    ],
    [
        'label' => '<i class="fa fa-cog"></i> ' . Yii::t('report', 'Options'),
        'content' => $this->render("options",['dataTemplate'=>$dataTemplate]),
    ],
];
?>
 
<div class="row">
    <div class="col-md-12">
        <h3><?= Yii::t('report', 'Custom Report Version 2.0')?></h3>
    </div>
    <div class="col-md-4 col-sm-4">
        <?php
            echo TabsX::widget([
                'items' => $items,
                'position' => TabsX::POS_ABOVE,
                'encodeLabels' => false
            ]);
        ?>

    </div>
    <div class="col-md-8 col-sm-8">
        <div class="" style="display: block;font-size:10pt;margin-bottom:15px;padding:10px;">* <?= Yii::t('report','Click ปุ่มสีส้มเพื่อเลือกตัวแปรลง Editor')?></div>
        <div id="editor">
            <?=
            \appxq\sdii\widgets\FroalaEditorWidget::widget([
                'name' => 'template_name',
                'value'=>$dataTemplate['template'],
                //'id'=>'froala-editor',
                'options'=>['id'=>'custom_template'],
                'clientOptions' => [
                    'zIndex' => 1,
                    'height' => '500',
                    //'theme' => 'gray', //optional: dark, red, gray, royal
                    'language' => 'th',
                ]
            ]);
            ?>
        </div>
        <br>
        <div class="text-center">
            <button class="btn btn-default btn-lg btnPreview"><i class="fa fa-eye"></i> <?= Yii::t('report', 'Preview')?></button>
            <button class="btn btn-primary btn-lg btnSave"><i class="fa fa-floppy-o"></i>  <?= Yii::t('report', 'Save')?></button>
        </div>
    </div>
</div>
<?php richardfan\widget\JSRegister::begin();?>
<script>
    $('.btnVar').on('click', function(){
        const val = $(this).attr('data-value');
        $("#custom_template").froalaEditor('html.insert', val, true);
    });
    
    $('.btnPreview').on('click', function(){
        //let id = $(this).attr('data-id');
        let template = $("#custom_template").val();
        let ezf_id = '<?= isset($ezf_id) ? $ezf_id : ""?>';
        let data_id = '<?= isset($data_id) ? $data_id : ""?>';
        
        let id              = $("#data_id").val();
        //console.log(id)
        
        let url = '/reports/custom-report/print';
        //console.log(url);
        
        url = url+"?ezf_id="+ezf_id+"&data_id=&template_id="+id+"&print=1&paper_size=150,80&layout=P";
        window.open(url , '_blank');
        return false;
    });
    
    $('.btnSave').on('click', function(){
        //let id = $(this).attr('data-id');
        let template        = $("#custom_template").val();
        let ezf_id          = '<?= isset($ezf_id) ? $ezf_id : ""?>'; 
        let options         = $('#frm-custom-report').serialize();
        let id              = $("#data_id").val();
        let template_id     = $('#template_name').val();
        let defaults         = $("input[name='default']:checked").val();

        console.log("id =>", id);
        console.log("template_name => ", template_id);
        console.log('default =>', defaults);
        if(template == ""){
            template = "Undefind";
        }
        
        
        let url = '/reports/custom-report/save';
        let params = {
            id:id,
            template:template,
            ezf_id:ezf_id,
            template_id:template_id,
            defaults:defaults
        };
        $.post(url, params, function(result){
            <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') ?>
        });
        
        
        return false;
    }); 
    
</script>
<?php richardfan\widget\JSRegister::end();?>


