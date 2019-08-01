<?php

use yii\helpers\Html;


$code = preg_split("/\r\n|\n|\r/", $code);
//$html = '';
?>
<div class="modal-header">
    <button type="button" class="close closeModal" id="closeModal">&times;</button>
</div>
<div class="modal-body">
    <table class="table table-hover table-responsive">
        <?php
        if (is_array($code) && !empty($code)) {
            echo Html::beginTag('tbody');
            foreach ($code as $kCode => $vCode) {
//                if ($kCode >= $start_row - 1) {
                    echo Html::beginTag('tr');
                    $html = '';
                    $arrCode = explode(',', $vCode);
                    if (is_array($arrCode) && !empty($arrCode)) {
                        foreach ($arrCode as $kArrCode => $vArrCode) {
                            if (in_array($kArrCode + 1, $display_code)) {
                                echo Html::tag('td', $vArrCode);
//                        $html = $vArrCode.',';
                            }
                        }
//                    }
//            $html = substr($html,0,strlen($html) -1);
//            echo $html.'<br/>';
                    echo Html::endTag('tr');
                }
            }
            echo Html::endTag('tbody');
        }
        ?>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default closeModal">Close</button>
</div>

<?php
$this->registerJs("
    $('.closeModal').click(function(){
        $('#".$modalID."').modal('hide');
    });
");