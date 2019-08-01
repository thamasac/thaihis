<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use appxq\sdii\widgets\ModalForm;
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
$module_id = isset($_GET['id'])?$_GET['id']:'';
$url = Url::to(['/gantt/pms-calendar/index', 
    'modal' => $modal,
    'reloadDiv' => $reloadDiv,
    'target' => $target,
    'module_id'=>$module_id,
    'forms' => $forms,
    'now_date' => $now_date,
    'defaultView' => $defaultView,
    'view_menu' => $view_menu,
    'maintask_ezf_id'=>$options['maintask_ezf_id'],
    'subtask_ezf_id'=>$options['subtask_ezf_id'],
    'task_ezf_id'=> isset($options['task_ezf_id'])?$options['task_ezf_id']:null,
    'response_ezf_id'=>$options['response_ezf_id'],
    'response_actual_field'=>$options['response_actual_field'],
    ]);
$html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);

$this->registerJs("
    getUiAjax('$url', '$reloadDiv');
");
?>

<?php echo $html; ?>

<?=
ModalForm::widget([
    'id' => 'modal-pms-calendar',
    'size' => 'modal-lg',
    'tabindexEnable'=>false,
]);
?>

<?=

ModalForm::widget([
    'id' => 'modal-ezform-task',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
?>

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