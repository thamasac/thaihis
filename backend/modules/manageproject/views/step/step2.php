<?php
    $this->title = Yii::t('chanpan','Project Information');
?>


<?= $this->render('_menu',['defaultActive'=>1])?>

<div style='color: #eb622c;'><b>Optional but perfect if completed.</b></div>


<?php 
    use appxq\sdii\helpers\SDNoty;
    use backend\modules\ezforms2\classes\EzfAuthFunc;
    $reloadDiv1 = "step1-grid2"; 
    $modal = "modal-ezform-main";
    $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
    $dataDynamic = backend\modules\manageproject\classes\CNCloneDb::checkDynamicDb($domain);
    
    
    $dataId=isset($dataDynamic['data_id']) ? $dataDynamic['data_id'] : '';
    if(!empty($dataId)){
        $dataCreate = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project", ['id'=>$dataId], 'ncrc');
        $ezfId='1520776142078903600';
        $url =  \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform?ezf_id='.$ezfId.'&dataid='.$dataDynamic['tctr_id'].'&modal=modal-ezform-main&reloadDiv='.$reloadDiv1.'&db2=0']);
    }
    $url = isset($url) ? $url : '';
    //\appxq\sdii\utils\VarDumper::dump($url);
?>
<?php $this->registerJsFile(\yii\helpers\Url::to('@web/js/jquery.min.js'))?>
<?php $this->registerJsFile(\yii\helpers\Url::to('@web/wizard/js/chanpanJs.js'))?>
<div id="showTCTR"></div>
<?php
$this->registerCss("
      
");
$this->registerJs("
        initFunc = function(){
            let url = '".$url."';
            $('#showTCTR').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');    
            $.get(url,function(data){
                $('#showTCTR').html(data);
                $('#showTCTR .nav-tabs').hide();
                $('#showTCTR .btn-info').hide();
                $('#showTCTR .close').hide();
                $('#showTCTR .close').hide();
               
            });     
        }
        initFunc();
        function getUiAjax(url, divid) {
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }
        showProjectAll=function(){}
");
?>