

<?php


$items = [
    [
        'label'=>'<i class="glyphicon glyphicon-user"></i> Profile',
        'linkOptions'=>['data-url'=>\yii\helpers\Url::to(['/notify/notify/view',$get[0]])]
    ],
    
];

echo kartik\tabs\TabsX::widget([
    'items'=>$items,
    'position'=> kartik\tabs\TabsX::POS_ABOVE,
    'encodeLabels'=>false
]);




richardfan\widget\JSRegister::begin()
?>

<script>
    
    $('.radioSreach').click(function () {
        var param = '';
        if ($(this).prop('checked')) {
            if ($(this).val() == 1) {
                param = '&status_view=0';
            } else if ($(this).val() == 2) {
                param = '&status_view=1';
            } else if ($(this).val() == 3) {
                param = '&complete_date=1';
            }

        }
        getUiAjax($('#notify-mem').attr('data-url') + param, 'notify-mem');
    });


    function getUiAjax(url, divid) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function (result, textStatus) {
                $('#' + divid).html(result);
            }
        }).fail(function (err) {
            err = JSON.parse(JSON.stringify(err))['responseText'];
            $('#' + divid).html(`<div class='alert alert-danger'>` + err + `</div>`);
        });
    }

</script>
<?php
richardfan\widget\JSRegister::end()?>