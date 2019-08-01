<?php
   use yii\helpers\Url;
   use yii\bootstrap\ActiveForm;
   use yii\helpers\Html;
?>

<?php ActiveForm::begin([
    'options'=>[
        
    ]
]);?>
    <?php foreach($data as $key=>$value){ ?>
        <?php 
            if($delete == '1'){
                $delete = 0;
                echo $this->render('_form',["data"=>$value,'delete'=>'1']);
            }else{
                echo $this->render('_form',["data"=>$value,'delete'=>'0']);
            }
        ?>
    <?php }?>
<?php ActiveForm::end();?>

