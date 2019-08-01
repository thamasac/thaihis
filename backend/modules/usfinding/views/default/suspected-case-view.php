<?php

use kartik\grid\GridView;

$sitecode = \Yii::$app->user->identity->userProfile->sitecode;
$stateToCca = '';
if ($sitecode != $hospital) {
    $stateToCca = 'true';
}
?>
<style>

    .patinet:hover {
        color: blue;
        cursor:pointer;
    }
</style>
<div class="pull-right">
    <button id="btn-close<?= $case_type ?>" class="btn btn-danger">ซ่อน</button>
</div>
<div >
    <table  class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">ลำดับที่</th>
                <th>Site ID</th>
                <th>PID</th>
                <th>HN</th>
                <th>ชื่อ-สกุล</th>
                <th>วันที่ตรวจ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($dataProvider as $key => $val) {
                if ($sitecode == $hospital || \Yii::$app->user->can('administrator')) {
                    $urlToCca02 = "/inputdata/redirect-page?dataid=" . $val['id'] . "&ezf_id=1437619524091524800&rurl=" . base64_encode(Yii::$app->request->url);
                    
                }else{
                    $urlToCca02 = '';
                }
                
                ?>
                <?php if($urlToCca02 != ''){?>
                <tr class="patinet" onclick="window.open('<?= $urlToCca02 ?>', '_blank')">
                <?php }else{?>
                <tr class="">
                <?php }?>
                    <td align="center"><?= ($key + 1) ?></td>
                    <td><?= $val['hsitecode'] ?></td>
                    <td ><?= $val['ptcodefull'] ?></td>
                    <td ><?= $val['hncode'] ?></td>
                    <td ><?= $val['title'] . '' . $val['name'] . ' ' . $val['surname'] ?></td>
                    <td ><?= $val['f2v1'] ?></td>
                </tr>

<?php } ?>
        </tbody>
    </table>
</div>


<?php
$this->registerJs("
    var case_type = '$case_type';
    $('#btn-close'+case_type).on('click',function(){
        $(this).parent().parent().empty();
    });
");
?>