<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="ezmodule-filter-list">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Filter') ?></h4>
    </div>

    <div class="modal-body">
        <div class="list-group">
        <?php
            if (isset($dataFilter) && !empty($dataFilter)) {
                foreach ($dataFilter as $key => $value) {
                    ?>
                    <a style="cursor: pointer;" data-dismiss="modal" class="list-group-item add-filter" data-filter="<?= $value['filter_id'] ?>"><?= $value['filter_name'] ?></a>
                    <?php
                }
            }
            ?>

        </div>
    </div>
</div>