<?php

/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 9/18/2018
 * Time: 11:45 AM
 */
/* @var $this \yii\web\View */
/* @var $visit_code string|mixed */
/* @var $visitTypeList array|mixed */

use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;

$id = SDUtility::getMillisecTime();
echo "<div data-temp-id='$id' class='row' style='margin-bottom: 6px'>";
?>

<?php
try {
    ?>
    <div class='col-md-6'>
        <?=
        kartik\select2\Select2::widget([
            'name' => "options[visit_type][]",
            'value' => "$visit_code",
            'options' => ['placeholder' => Yii::t('ezform', 'Visit Type'), 'id' => 'config_visit_' . $id],
            'data' => ArrayHelper::map($visitTypeList, 'visit_type_code', 'visit_type_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    
    <div class='col-md-3'>
      <?= yii\helpers\Html::button("Remove", ['class' => ['btn', 'btn-danger'], 'onclick' => "removeChoice('$id')"]); ?>
    </div>
    <?php
} catch (Exception $e) {
    echo "<p> Error in create choice Action!</p>";
    echo "<u>" . $e->getMessage() . "</u>";
}

$this->registerJs(<<<JS
function removeChoice(id) {
   console.log(id);
   var div =  $('div[data-temp-id='+id+']');
  div.remove()
}
JS
);
?>
