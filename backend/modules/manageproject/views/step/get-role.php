<?php

use appxq\sdii\helpers\SDNoty;
use backend\modules\ezforms2\classes\EzfAuthFunc;

$reloadDiv1 = "step3-grid";
$modal = "modal-ezform-main2";
?>
<?php
echo \appxq\sdii\widgets\ModalForm::widget([
    'id' => $modal,
    'size' => 'modal-lg',
    'tabindexEnable' => FALSE,
]);
?>
<div class="row">
    <div class="col-md-12 text-right">
        <?php \backend\modules\ezforms2\classes\EzfStarterWidget::begin(); ?>
            <?= backend\modules\ezforms2\classes\BtnBuilder::btn()
                                    ->ezf_id("1519706984056553600")
                                    //->options(['data-id'=>$_GET['id'], 'class'=>'btnManaAuth btn btn-success'])
                                    ->modal($modal)
                                    ->reloadDiv($reloadDiv1)
                                    ->buildBtnAdd();?>
        <?php \backend\modules\ezforms2\classes\EzfStarterWidget::end();?>
    </div>
</div>
    <?php
    $ezfReadWrite = backend\modules\ezforms2\classes\EzfHelper::ui('1519706984056553600')
            ->data_column(['role_name','role_detail'])
            ->default_column(0)
            ->reloadDiv($reloadDiv1)
            ->modal($modal)
            ->buildGrid();
    echo $ezfReadWrite;
    ?>

<?php
$this->registerJs("
     
        function getUiAjax(url, divid) {
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }
 
");
?>