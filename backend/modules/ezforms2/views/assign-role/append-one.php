<?php
   use yii\helpers\Url;
   use yii\bootstrap\ActiveForm;
   use yii\helpers\Html;
?>

<?php ActiveForm::begin([
     
]);?>
 
        <?php 
            echo $this->render('_form',["data"=>$data,'delete'=>'0']);
        ?>
 
<?php ActiveForm::end();?>

