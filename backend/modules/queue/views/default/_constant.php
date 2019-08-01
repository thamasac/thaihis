<?php
use yii\helpers\Html;
?>
<div class="modal-header">
     <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <?php 
    echo '<div class="row">';
    $defaultconstant ='';
    $defaultconstant .= Html::button('วัน เดือน ปี แบบเต็ม', ['class' => 'btn btn-warning btn-sm '.$name, 'data-constant' => '{full_date}', 'style' => 'margin-top:2%']).' ';
    $defaultconstant .= Html::button('วันที่', ['class' => 'btn btn-warning btn-sm '.$name, 'data-constant' => '{date}', 'style' => 'margin-top:2%']).' ';
    $defaultconstant .= Html::button('เดือน', ['class' => 'btn btn-warning btn-sm '.$name, 'data-constant' => '{month}', 'style' => 'margin-top:2%']).' ';
    $defaultconstant .= Html::button('ปี', ['class' => 'btn btn-warning btn-sm '.$name, 'data-constant' => '{year}', 'style' => 'margin-top:2%']).' ';
    $defaultconstant .= Html::button('ชื่อโรงพยาบาล', ['class' => 'btn btn-warning btn-sm '.$name, 'data-constant' => '{hospital}', 'style' => 'margin-top:2%']).' ';
    $defaultconstant .= Html::button('รหัสโรงพยาบาล', ['class' => 'btn btn-warning btn-sm '.$name, 'data-constant' => '{hospital}', 'style' => 'margin-top:2%']).' ';
    echo Html::tag('div', 'Default constant',['class' => 'col-md-12']);
    echo Html::tag('div', $defaultconstant,['class' => 'col-md-12','style' => 'margin-bottom:2%']);
    if (is_array($dataForm) && !empty($dataForm)) {
        foreach ($dataForm as $key => $vData) {
            echo "<div class='col-md-12' style='margin-bottom:2%'>";
            echo Html::tag('div', 'Form name : <b>' . $key . '</b>');
            if (is_array($vData) && !empty($vData)) {
                foreach ($vData as $k => $v) {
                    echo Html::button($v, ['class' => 'btn btn-warning btn-sm '.$name, 'data-constant' => '{' . $k . '}', 'style' => 'margin-top:2%']) . '<br/>';
                }
            }
            echo '</div>';
        }
    }
    echo "</div>";
    ?>
</div>
<div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>

