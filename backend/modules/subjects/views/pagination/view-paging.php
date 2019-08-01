<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$amtSum=0;
$amt =0;
$begin_page = 1;
$end_page = 8;
?>
<nav aria-label="...">
    <ul class="pagination">
        <li class="page-item <?= $thisPage == '1' ? 'disabled' : '' ?>">
            <a class="page-link <?= $thisPage > '1' ? 'btn_paging' : '' ?>" data-page="<?= $thisPage - 1 ?>" href="javascript:void(0)" tabindex="-1"><strong><</strong></a>
        </li> 
        <?php
        $amtSum = $pageAmt / $pageLimit;
        $amt = ceil($amtSum);
        if($amt > 8 )
        {
            if($thisPage < $amt/2){
                $end_page = 8;
            }else{
                if($end_page+4 > $amt){
                    $begin_page = $begin_page-4;
                    $end_page=$amt;
                }else{
                    $end_page=$begin_page-4;
                    $end_page = $thisPage+4;
                }
            }
         
        }else{
            $end_page = $amt;
        }
        for ($i = $begin_page; $i <= $amt; $i++):
            ?>
            <?php if ($i == $thisPage): ?>
                <li class="page-item active">
                    <a class="page-link" href="javascript:void(0)"><?= $i ?><span class="sr-only">(current)</span></a>
                </li>
            <?php else: ?>
                <li class="page-item"><a class="page-link btn_paging" data-page="<?= $i ?>" href="javascript:void(0)"><?= $i ?></a></li>
            <?php endif; ?>
        <?php endfor; ?>

        <li class="page-item" <?= $thisPage == $amt ? 'disabled' : '' ?>>
            <a class="page-link <?= $thisPage != $amt ? 'btn_paging' : '' ?>" data-page="<?= $thisPage + 1 ?>" href="javascript:void(0)"><strong>></strong></a>
        </li>
    </ul>
</nav>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    
    $('.btn_paging').click(function () {
        var url = $('#<?=$reloadDiv?>').attr('data-url');
        var page = $(this).attr('data-page');
        getReloadDiv(url + '&thisPage=' + page, '<?=$reloadDiv?>');
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>