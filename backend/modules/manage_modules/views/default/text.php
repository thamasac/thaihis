<?php 
    echo cpn\chanpan\widgets\CNDrag::widget([
        'url'=> \yii\helpers\Url::to(['/manage_modules/default/sort']),
        'options'=>[
            'header'=>'col-md-2 col-sm-3 col-xs-4 text-center',
            'body'=>'col-md-10 col-sm-10 col-xs-10'
        ],
        'data'=>[
            [
                'data-id'=>'1',
                'img'=>'https://storage.work.ncrc.in.th/ezform/fileinput/projecticon_1529131549006585700.png',
                'options'=>['style'=>'width:70%']
            ],[
                'data-id'=>'2',
                'img'=>'https://backend.ncrc.in.th/img/health-icon.png',
                'options'=>['style'=>'width:70%']
            ]
        ]
    ]);
?>