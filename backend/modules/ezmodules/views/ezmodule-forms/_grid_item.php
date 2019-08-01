<?php

use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezforms2\classes\EzfQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<?php
//appxq\sdii\utils\VarDumper::dump($data);

$joinData = 'target';
if(isset($data['fparent_id'])){
    $joinData = 'fparent_id';
}

$pkJoin = 'target';
$groupField = 'target';
if ($special == 1) {
    $groupField = 'ptid';
    $pkJoin = 'ptid';
    $joinData = 'ptid';
    if(isset($data['fparent_ptid'])){
        $joinData = 'fparent_ptid';
    }
}

$ezformTable = [];
$progressCount = 0;
$progressTotal = 1;
$progressValue = 0;
$ezform = EzfQuery::getEzformById($ezf_id);
if (isset($ezform)) {
    $ezformTable[$ezf_id] = $ezform;

    $showItems = ModuleFunc::getCondition($data, $joinData, $options, $pkJoin, $ezformTable);
    $unique_record = $ezform['unique_record'];
    
    $btnItems = ['emrBtn' => '', 'itemsBtn' => '', 'hasData' => 0, 'hasSubmitData' => 0, 'groupItems'=>[], 'allItems'=>[]];
    $btnadd = '';
    if ($showItems) {
        $btnItems = ModuleFunc::getItemsBtn($data, $joinData, $groupField, $options,  $ezf_id, $pkJoin, $unique_record, $reloadDiv, $modal, $ezformTable);
        $progressCount = count($btnItems['groupItems']);
        $progressValue = count($btnItems['allItems']);
        
        $btnadd = ModuleFunc::btnAdd($data, $joinData, $pkJoin, $special, $parent_ezf_id, $ezf_id, $reloadDiv, $modal);
        if($unique_record==2 && $btnItems['hasData']==1){
            $btnadd = '';
        } elseif ($unique_record==3 && $btnItems['hasSubmitData']==1) {
            $btnadd = '';
        }
    }
    $progress = '';
    if (isset($options['forms']) && $showItems) {
        $progressItems = ModuleFunc::getItemsProgress($data, $joinData, $special, $options['forms'], $ezf_id, $pkJoin, $reloadDiv, $modal, $progressValue, $ezformTable);
        
        $progressTotal = $progressItems['total'];
        $progressCount = $progressItems['count'];
        
        $percent = 0;
        if($progressTotal>0){
            $percent = ($progressCount/$progressTotal)*100;
        }
        
        if($progressItems['html']!=''){
            $progress = Yii::$app->controller->renderPartial('_popover', [
                'content'=> yii\helpers\Html::encode($progressItems['html']),
                'percent'=>number_format($percent),
                'form_name'=> $ezform['ezf_name'],
            ]);
        }
    }


?>
<table style="width: 100%;">
    <tr>
        <td style="min-width: 110px;">
            <?= $btnItems['emrBtn'] ?>
            <?=$btnadd?>
        </td>
        <td style="padding: 0 3px;width: 100px;">
            <?= $progress ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 5px;">
            <?= $btnItems['itemsBtn'] ?>
        </td>

    </tr>
</table>


<?php
}

\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    popoverBoot();

    $('body').on('shown.bs.popover', '.popover-open', function () {
        popoverBoot();
    });

    function popoverBoot() {
        $('.popover-open').popover({
            html: true,
            placement: 'bottom',
            template: '<div class="popover" style="min-width: 400px;" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
