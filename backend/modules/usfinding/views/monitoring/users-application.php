<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 3/21/2018
 * Time: 2:38 PM
 */

use yii\grid\GridView;
use kartik\helpers\Html;

//var_dump($dataProvider);
//var_dump($dataQuery->all());

try{

    $userListGrid = GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            [
                'attribute' => 'user_id',
                'label' => 'UserId',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'firstname',
                'label' => 'Firstname',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Lastname',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'email',
                'label' => 'Email.',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'telephone',
                'label' => 'Tel.',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'device_id',
                'label' => 'DeviceID.',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'platform',
                'label' => 'Platform.',
                'headerOptions' => ['class' => 'col-md-1 text-center'],
                'format' => 'raw',
            ]
        ],
    ]);
} catch (Exception $e) {
    echo $e;
}

echo Html::panel([
    'heading' => "Device Users List (".$heading.")",
    'body' => "$userListGrid",
], "primary");

