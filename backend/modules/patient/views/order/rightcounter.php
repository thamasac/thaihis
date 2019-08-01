<?php

use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;

backend\modules\ezforms2\classes\EzfStarterWidget::begin();
$this->title = Yii::t('app', 'Patient Validate');
?>
<div class="ordercounter-index">
  <div class="sdbox-header">
    <h3><?= Html::encode($this->title) ?></h3>
  </div>
  <?php
  $url = Url::to(['/patient/order/right-counter', 'right' => 'approve', 'reloadDiv' => 'view-rightcounter']);
  ?>
  <div id="view-rightcounter" data-url="<?= $url ?>">
    <?php
    echo $this->render('_gridright', [
        'ezfVisit_id' => $ezfVisit_id,
        'ezfRight_id' => $ezfRight_id,
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel, 
        'reloadDiv' => 'view-rightcounter',
        'date'=>$date
        ]);
    ?>
  </div>
</div>

<?=
ModalForm::widget([
    'id' => 'modal-rightcounter',
    'size' => 'modal-xxl',
]);
?>

<?php
$this->registerJs("       
    $('#view-rightcounter').on('dblclick', 'tbody tr', function() {    
        var url = $(this).attr('data-url');
        modalEzformMain(url,'modal-ezform-main');   
    });	

    $('#view-rightcounter').on('click', 'tbody tr td a', function() {
        var url = $(this).attr('data-url');
        modalEzformMain(url,'modal-ezform-main'); 

        return false;
    });

");

backend\modules\ezforms2\classes\EzfStarterWidget::end();
?>