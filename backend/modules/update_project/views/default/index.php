<?php 
use kartik\tabs\TabsX;
$this->title = Yii::t('chanpan','Update Project');
echo TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'encodeLabels'=>FALSE,
    'items' => [
        [
            'label' => "<i class='fa fa-cog'></i> ".Yii::t('chanpan','SQL Command'),
            'content' => $this->render("sql"),
            'active' => true
        ],
        [
            'label' => \Yii::t('chanpan', 'Error Log'),
            'content' => $this->render('log-not-update', ['dataProvider'=>$dataProvider]),
            'headerOptions' => ['style'=>'font-weight:bold'],
            'options' => ['id' => 'myveryownID'],
        ],
//        [
//            'label' => 'Dropdown',
//            'items' => [
//                 [
//                     'label' => 'DropdownA',
//                     'content' => 'DropdownA, Anim pariatur cliche...',
//                 ],
//                 [
//                     'label' => 'DropdownB',
//                     'content' => 'DropdownB, Anim pariatur cliche...',
//                 ],
//            ],
//        ],
    ],
]);
?>

 

