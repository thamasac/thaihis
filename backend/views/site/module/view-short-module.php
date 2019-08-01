<?php
use yii\helpers\Url;
?> 
    <div id="module-select"></div>

<?php \richardfan\widget\JSRegister::begin(); ?>
<script> 
    function initModuleSelect() {
        let url = '/site/short-module-select';
        let module = '#module-select';
        $(module).html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $.get(url,{edit_mode:20}, function (data) {
            $(module).html(data);
        });
    }

    $('#modal-create-project').on('hidden.bs.modal', function () {
        //location.reload();
    })

    initModuleSelect();
</script>
<?php \richardfan\widget\JSRegister::end(); ?>


