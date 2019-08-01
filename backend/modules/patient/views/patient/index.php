<?php

use yii\helpers\Url;
use yii\bootstrap\Nav;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\patient\classes\PatientHelper;

\backend\modules\cpoe\assets\CpoeAsset::register($this);

$this->title = "Patient's Profile";

$items = [
    [
        'label' => 'PatientInfo',
        'url' => Url::to(['/patient/patient/', 'dataid' => $dataid, 'tab' => '1']),
        'active' => $tab == '1',
    ],
    [
        'label' => 'Medical History',
        'url' => Url::to(['/patient/patient/', 'dataid' => $dataid, 'tab' => '2']),
        'active' => $tab == '2',
    ],
    [
        'label' => 'Helath Plan',
        'url' => '#',
        'active' => $tab == '3',
    ],
    /*[
        'label' => 'EMR',
        'url' => Url::to(['/patient/patient/', 'dataid' => $dataid, 'tab' => '4']),
        'active' => $tab == '4',
    ],*/
];
EzfStarterWidget::begin();
?>
<?=

Nav::widget([
    'items' => $items,
    'options' => ['class' => 'nav nav-tabs',],
]);

if ($tab == 1) {
    echo PatientHelper::uiPatient($dataid, 'view-patient');
} elseif ($tab == 2) {
    echo PatientHelper::uiMedicalHistory($dataid, 'view-medical-history');
} elseif ($tab == 3) {
    
} elseif ($tab == 4) {
    echo PatientHelper::uiPatientEMR($dataid, 'view-patient');
}
?>

<?php EzfStarterWidget::end(); ?>