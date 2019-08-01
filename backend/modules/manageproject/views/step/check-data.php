<?php 
    $domain = isset(Yii::$app->params['current_url']) ? Yii::$app->params['current_url'] : \cpn\chanpan\classes\CNServerConfig::getDomainName();
    $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
    $dataDynamic = backend\modules\manageproject\classes\CNCloneDb::checkDynamicDb($domain);
    $dataId=$dataDynamic['data_id'];
    
$this->registerJs("
$(function(){
    checkData = function(){
            let dataId = '".$dataId."';
            let url = '".\yii\helpers\Url::to(['/manageproject/step/get-status'])."';
            let params={data_id:dataId};
            $.get(url, params, function(data){
                console.warn(data);
                if(data == 2){
                    ApiService();
                }else{
                    let url2 = '".\yii\helpers\Url::to(['/manageproject/step/index'])."';
                    location.href = url2;
                }
            });
             
        }
        ApiService=function(){
            let dataId = '".$dataId."';
            let url = 'https://".$main_url."/api/ncrc-project/get-data-byid?data_id='+dataId;
            $.get(url, function(data){
                console.warn(data); 
                SaveData(data);
            });
        }
        SaveData=function(dataStr){
          dataStr = JSON.parse(dataStr);
          
          let url = '".\yii\helpers\Url::to(['/manageproject/step/save-data'])."';
           $.post(url,{data:dataStr[0]}, function(data){
                console.warn(data);
                if(data == 1){
                    let url2 = '".\yii\helpers\Url::to(['/manageproject/step/index'])."';
                    location.href = url2;
                }else{
                    location.reload();
                }
           });    
        }
        
       checkData();

});
 
");
?>
