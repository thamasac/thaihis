
<?php
    $items = [
    [
        'label'=>'<i class="fa fa-envelope"></i> Verify E-mail Templates',
        'content'=>'xx1',
        'linkOptions'=>['data-url'=> \yii\helpers\Url::to(['/site/verify-email-templates'])],
        'active'=>true
        //verify-email
    ],
    [
        'label'=>'<i class="fa fa-address-card"></i> Invite Members Templates',
        'content'=>'',
        'linkOptions'=>['data-url'=> \yii\helpers\Url::to(['/site/invite-templates'])]
    ],
    [
        'label'=>'<i class="fa fa-refresh"></i> Recover Password Templates',
        'content'=>'',
        'linkOptions'=>['data-url'=> \yii\helpers\Url::to(['/site/recover-password-templates'])]
    ]     
     
];
    echo kartik\tabs\TabsX::widget([
        'items'=>$items,
        'position'=> kartik\tabs\TabsX::POS_ABOVE,
        'encodeLabels'=>false,
        'options'=>['id'=>'templates']
    ]);
?>
<?php richardfan\widget\JSRegister::begin();?>
<script>
    $('#templates .active a').trigger( "click" );
</script>
<?php richardfan\widget\JSRegister::end();?>