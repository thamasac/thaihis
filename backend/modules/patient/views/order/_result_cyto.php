<?php
if ($dataPap) {
    ?>
    <div class="row">
        <?php
        foreach ($dataPap as $value) {
            if (!empty($value['report_status'])) {
                ?>
              <div class="col-md-12" style="margin-bottom: 5px">
                ผลตรวจ <?= $value['order_name']; ?> 
                <?=
                        backend\modules\ezforms2\classes\BtnBuilder::btn()
                        ->ezf_id($ezfCyto_id)
                        ->reloadDiv('view-order')
                        ->label('<i class="fa fa-wpforms"></i> คลิกเพื่อดูผลตรวจ')
                        ->options(['class' => 'btn btn-md btn-primary'])
                        ->buildBtnView($value['id']);
                ?>
              </div>
          <?php }
          ?>
        </div>
        <?php
    }
}
if ($dataEkg) {
    ?>
    <div class="row">
        <?php
        foreach ($dataEkg as $value) {
            ?>
          <div class="col-md-12" style="margin-bottom: 5px">
            ผลตรวจ <?= $value['order_name']; ?> 
            <?=
                    backend\modules\ezforms2\classes\BtnBuilder::btn()
                    ->ezf_id($ezfEkg_id)
                    ->reloadDiv('view-order')
                    ->label('<i class="fa fa-wpforms"></i> คลิกเพื่อดูผลตรวจ')
                    ->options(['class' => 'btn btn-md btn-primary'])
                    ->buildBtnView($value['id']);
            ?>
          </div>
          <?php
      }
      ?>
    </div>
    <?php
} else {
    ?>
    <h1 class = "text-center" style = "font-size: 35px; color: #ccc;">
      <?= Yii::t('patient', 'Find not found.') ?>
    </h1>
    <?php
}
?>
