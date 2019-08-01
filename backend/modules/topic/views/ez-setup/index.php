<div class="container">
    <div id="ez-setup"></div>
</div>
<?php \richardfan\widget\JSRegister::begin(['position' => \yii\web\View::POS_READY]);?>
<script>
    function loadEzSetup(){
        let url = '/topic/ez-setup/get-ez-setup';
        $.get(url, function(data){
            $('#ez-setup').html(data);
        });
    }
    loadEzSetup();
</script>
<?php \richardfan\widget\JSRegister::end();?>