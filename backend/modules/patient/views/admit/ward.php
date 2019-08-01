<?php

use backend\modules\patient\classes\PatientHelper;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezforms2\classes\EzfStarterWidget;

\backend\modules\patient\assets\PatientAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);
$this->title = Yii::t('app', 'Ward');

EzfStarterWidget::begin();
?>
<div class="sdbox-header">
  <h3><?= $dept['unit_name']; ?> </h3>
</div>
<div class="ward-dashboard" style="margin-top: 15px">
    <?= PatientHelper::uiWardDash($ezfAdmit_id, $dept['unit_code'], 'ward-dash'); ?>
</div>
<div class="ward-bed" style="margin-top: 15px">
  <?= PatientHelper::uiWardBed($ezfBed_id, $dept['unit_code'], 'ward-bed'); ?>
</div>
<?php
echo ModalForm::widget([
    'id' => 'modal-ezform-md',
    'size' => 'modal-md',
    'tabindexEnable' => false,
]);

echo ModalForm::widget([
    'id' => 'modal-ward-xxl',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);

$this->registerJS("
    $(document).on('show.bs.modal', '.modal', function (event) {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    $(document).on('hidden.bs.modal', '.modal', function (e) {
        var hasmodal = $('body .modal').hasClass('in');
        if (hasmodal) {
            $('body').addClass('modal-open');
        }
    }); 
    ");


EzfStarterWidget::end();

