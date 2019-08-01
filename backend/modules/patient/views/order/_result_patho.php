<?php
if ($data) {
    foreach ($data['result'] as $value) {
        ?>
        <div class="row">
          <div class="col-md-12">
            <div class="text-center h4"><?= $value['xray_item_des'] ?> </div>
          </div>
          <div class="col-md-12">
              <?= nl2br($value['result']); ?>
          </div>
          <div class="col-md-12">
            <div class="text-right"><?= Yii::t('patient', 'Doctor') . ' ' . $value['doc_fullname'] ?></div>
          </div>
        </div>
        <?php
    }
} else {
    ?>
    <h1 class = "text-center" style = "font-size: 35px; color: #ccc;">
      <?= Yii::t('patient', 'Find not found.') ?>
    </h1>
    <?php
}
?>