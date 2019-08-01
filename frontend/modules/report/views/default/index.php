<?php
$this->title ="Report";
?>
<div class="site-index">
    <br>
    <div class="clearfix"></div>
    <div class="col-lg-12">
        <a class="btn btn-success" href="https://portal.ncrc.in.th">Sign in/ Sign up&nbsp;</a>
        <a class="btn btn-danger" href="/report">Report</a>
    </div>
    <hr>
</div>
<div class="tabs" style="margin-bottom: 20px">
    <?=
    \yii\bootstrap\Nav::widget([
        'items' => $item,
        'options' => ['class' => 'nav nav-tabs'],
    ]);
    ?>
</div>
<div id="main-tab">
    <div class="text-center">
        <h4>Loading...</h4>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // $('#main-tab').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
    var url = '<?= $url ?>' ;
    if(url != '#'){
        $.ajax({
            method: 'GET',
            url: url,
            success: function (result) {
                $('#main-tab').html(result);
            }
        });
    }else{
        $('#main-tab').empty();
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
