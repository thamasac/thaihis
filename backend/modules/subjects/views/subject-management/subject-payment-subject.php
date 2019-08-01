<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use kartik\tabs\TabsX;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="list-group">
    <li class="list-group-item active" style="text-align: center;">Subject Number.</li>
    <li class="list-group-item" style="text-align: center;">
        <input type="text" name="subject-number-search" id="subject-number-search" class="form-control subject-number-search" placeholder="Subject Search..."> 
    </li>
    <li class="list-group-item" style="text-align: center;">
        <select type="text" name="subject-number-group" id="subject-number-group" class="form-control subject-number-group" placeholder="Subject Search..."> 
            <option value='0' <?= isset($group_id) && $group_id==null?'selected':''?> >All subject</option>
            <?php
            if ($groupData) {
                foreach ($groupData as $val) {
                    echo "<option value='{$val['id']}' ".(isset($group_id)&&$group_id==$val['id']?'selected':'') .">" . $val['group_name'] . "</option>";
                }
            }
            ?>
        </select>   
    </li>
    <?php
    foreach ($data as $key => $value):
        $group = SubjectManagementQuery::getGroupByTarget($ezform_main, $value['target'])
        ?>
        <a href="javascript:void(0)" class="list-group-item subject-item" data-id="<?= $value['target'] ?>" data-subject="<?= $value[$subDisplay] ?>" 
           data-group_name="<?= $value['group_name'] ?>" data-group_id="<?= $group['group_name'] ?>" style="text-align:right;font-size:16px;"><i class="fa fa-address-card "></i> <?= $value[$subDisplay] ?></a>
       <?php endforeach; ?>
</div>
<div class="clearfix"></div>
<div id="pagination">
    <?php
    echo $this->renderAjax('../pagination/view-paging', [
        'thisPage' => $thisPage,
        'pageLimit' => $pageLimit,
        'pageAmt' => $pageAmt,
        'reloadDiv' => 'display_subject_payment',
    ]);
    ?>
</div>

<div class="clearfix"></div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
</div>




