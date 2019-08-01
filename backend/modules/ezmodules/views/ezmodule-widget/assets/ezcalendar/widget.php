<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$tab = isset($_GET['tab'])?$_GET['tab']:'';
$module = isset($_GET['id'])?$_GET['id']:'';
$addon = isset($_GET['addon'])?$_GET['addon']:'';
$target = isset($_GET['target'])?$_GET['target']:'';
$now_date = isset($_GET['now_date']) ? $_GET['now_date'] : date('Y-m-d');
$forms = isset($options['forms'])?backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options['forms']):'';
$defaultView = isset($options['defaultView'])?$options['defaultView']:'month';
$view_menu = isset($options['view_menu'])?\backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options['view_menu']):\backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['month', 'agendaWeek', 'agendaDay']);


$reloadDiv = 'ezcalendar-'.$tab;
$modal = 'modal-ezform-main';

$url = Url::to(['/ezforms2/ezform-data/ezcalendar', 
    'modal' => $modal,
    'reloadDiv' => $reloadDiv,
    'target' => $target,
    'forms' => $forms,
    'now_date' => $now_date,
    'defaultView' => $defaultView,
    'view_menu' => $view_menu,
    ]);
$html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);

$this->registerJs("
    getUiAjax('$url', '$reloadDiv');
");
?>

<?php echo $html; ?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script

    $('#modal-ezform-main').on('hidden.bs.modal', function (e) {
      $('#calendar-<?= $reloadDiv ?>').fullCalendar('refetchEvents');
      $(document).find('#modal-ezform-calendar').modal('hide');
    });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>