<?php

use yii\helpers\Html;

echo Html::button('<i class="fa ' . $btn_icon . '"></i> ' . $btn_text, ['class' => 'btn ' . $btn_color . ' ' . $btn_style, 'id' => 'btn-print']);

\richardfan\widget\JSRegister::begin(); ?>
    <script>
        $('#btn-print').click(function () {
            let url = $('#<?=$reloadDiv?>').attr('data-url');
            window.open(url + '&status=1');
        });
    </script>
<?php \richardfan\widget\JSRegister::end();
