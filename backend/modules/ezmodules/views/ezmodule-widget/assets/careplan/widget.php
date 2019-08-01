<?php
use yii\helpers\Url;
use yii\helpers\Html;

// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
  'options' => $options,
  'widget_config' => $widget_config,
  'model' => $model,
  'modelOrigin'=>$modelOrigin,
  'menu' => $menu,
  'module' => $module,
  'addon' => $addon,
  'filter' => $filter,
  'reloadDiv' => $reloadDiv,
  'dataFilter' => $dataFilter,
  'modelFilter' => $modelFilter,
  'target' => $target,
 */

$this->registerJsFile(
        '@web/js/svg.js?1', ['depends' => [\yii\web\JqueryAsset::className()]]
);

$url = Url::to([
    '/gantt/care-plan/main-care-plan',
]);
?>
<div id="display-care-plan" data-url="<?= $url ?>">

</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $(function () {
        var reloadDiv = 'display-care-plan';
        var url = $('#display-care-plan').attr('data-url');
        getReload(url, reloadDiv);
    });

    function getReload(url, div) {
        $.get(url, function (result) {
            div.empty(result);
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>   