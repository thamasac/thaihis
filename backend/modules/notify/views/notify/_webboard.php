
<?php

//appxq\sdii\utils\VarDumper::dump($reloadDiv);
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Url;

$class = '';
if ($status == 3) {
    $class = "btn-success "; //disabled";
} else if ($status == 2) {
    $class = "btn-primary";
    $status = 3;
} else if ($status == 1) {
    $class = "btn-primary";
    $status = 2;
};
if ($txt == '1') {
    $txt = 'Review';
} else if ($txt == '2') {
    $txt = 'Approve';
} else if ($txt == '3') {
    $txt = 'Acknowledge';
}
?>


<div class="panel" style="padding-left: 5px; padding-right: 5px;">
    <div class="pull-right">
        <?php
//        echo \yii\bootstrap\Html::button($txt, [
////            'id'=>'btnAction',
//            'class' => 'btn ' . $class . ' btn-md btnUrl',
//            'data-status' => $status,
//            'data-id' => $id
//        ]);
        echo \yii\bootstrap\Html::a('<i class="fa fa-link" aria-hidden="true"></i> View', "#", [
            'target' => '_blank',
            'class' => 'btn ' . $class . ' btn-md btnUrl',
//                                            'data-status' => '2',
//                                            'data-id' => $data->id,
            'data-pjax' => 0,
        ]);
        ?>
    </div>
    <div class="clearfix"></div>
    <hr>
    <?php
//        appxq\sdii\utils\VarDumper::dump($dataProvider,0);
//    echo \yii\widgets\ListView::widget([
//        'dataProvider' => $dataProvider,
//        'itemView' => '_list',
//    ]);
    ?>

<!--    <div class="panel panel-default">
        <div class="panel-heading">
<?php // echo 'แสดงความคิดเห็น'; ?>
        </div>
        <div class="panel-body">
            <div class="col-md-4 text-center">
                <?php
//                echo \yii\helpers\Html::img(
//                        Yii::$app->user->identity->profile->avatar_base_url . "/" .
//                        Yii::$app->user->identity->profile->avatar_path, [
//                    'width' => '100', 'height' => '100'
//                ]);
                ?>
                <br>
                <i class="fa fa-user-circle-o"></i> <?php // echo Yii::$app->user->identity->profile->firstname . " " . Yii::$app->user->identity->profile->lastname ?>
                <br>
                <i class="fa fa-envelope"></i> <?php // echo Yii::$app->user->identity->profile->public_email ?> 

            </div>
            <div class="col-md-8">
                </i> <?php // echo '' ?> 
            </div>
        </div>
    </div>-->


</div>

<?php $this->registerJs("
        $('#divForum .pagination a').on('click', function(e) {
//            e.preventDefault();
            getForum($('#divForum').attr('data-id'),$(this).attr('href'));
            return false;
        });
        
        $('#btnForum').on('click', function(e) {
//          e.preventDefault();
            getForum($('#divForum').attr('data-id'),$(this).attr('href'));
            return false;
        });

        $('#divForum').on('click','.btnAction',function(){
            var url = $('#$reloadDiv').attr('data-url');
            var divid =  '$reloadDiv';
            if(!$(this).hasClass('btn-success')){
                $.ajax({
                    method: 'POST',
                    url: '" . Url::to(['update-action']) . "',
                    data:{
                        id:$(this).attr('data-id'),
                        ezf_id:'" . $ezf_id . "',
                        status:$(this).attr('data-status')
                    },
                    dataType: 'JSON',
                    success: function(result, textStatus) {
                          if(result.status == 'success'){
                            setTimeout(()=>{
                                " . SDNoty::show('result.status', 'result.status') . ";
                                $('#divForum .btnAction').removeClass('btn-primary');
                                $('#divForum .btnAction').addClass('btn-success');
//                                getUiAjax(url, divid);
                             },500);
                           
                          }else if(result.status == 'error'){
                            " . SDNoty::show('result.status', 'result.status') . ";
                          }
            //            
                    },error:function(error){
                        " . SDNoty::show('"Error"', '"error"') . ";
                    }
                });
            }
        });

    "); ?>