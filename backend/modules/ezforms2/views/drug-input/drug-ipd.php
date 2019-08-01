
<?php 

use yii\helpers\Html;


echo Html::beginTag('ul', ['class'=>'list-group']);
foreach ($date as $value) {
     
    echo Html::tag('a',$value['DATETIME_ADMIT'],['class'=>'list-group-item text-center listDrugIpd','data-val'=>$value['DATETIME_ADMIT']]);
     
}

echo Html::endTag('ul');

\richardfan\widget\JSRegister::begin(['position'=> yii\web\View::POS_READY]);
?>
<script>

    $('.listDrugIpd').on('click',function(){
        getData($(this).attr('data-val'),'ipd');
    });


</script>

<?php\richardfan\widget\JSRegister::end();


