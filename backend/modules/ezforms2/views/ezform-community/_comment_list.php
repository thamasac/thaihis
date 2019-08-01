<?php

use backend\modules\ezmodules\classes\ModuleFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<?php
if ($moreitem == 1) {
    ?>
    <li class="media text-center more-item">
        <button id="more_comment-<?=$object_id?>-<?=$query_tool?>" data-start="<?= $start ?>" class="btn btn-info btn-sm"><?= Yii::t('ezmodule', 'More Comment') ?></button>
    </li>
<?php } ?>
    
<?php
if (isset($model) && !empty($model)) {
    $count_model = count($model);
    for($i=$count_model-1; $i>=0; $i--){
        $obj = $model[$i];

        echo $this->renderAjax('_comment_item', [
                'value' => $obj,
                'modal' => $modal,
                'dataid' => $dataid,
            ]);
    }
}?>


<?php $this->registerJs("
$('#count-$object_id-$query_tool').html('$count');


"); ?>

