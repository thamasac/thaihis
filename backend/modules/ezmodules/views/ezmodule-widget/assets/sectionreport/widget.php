<?php
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
?>
<?php
$tab = $_GET['tab'];
if (!isset($tab))
    $tab = 1;
if ($tab == '2') {
    ?>
    <div class="col-md-12">
        <h3>Monthly Team Status Report</h3>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Project Complete</h4>
            </div>
            <div class="panel-body">
                
            </div>
        </div>
        <div class="panel panel-success">
            <div class="panel-heading">
                <h4>In Progress</h4>
            </div>
            <div class="panel-body">

            </div>
        </div>
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h4>Assigned But Not Started</h4>
            </div>
            <div class="panel-body">

            </div>
        </div>
    </div>
<div class="clearfix"></div>
<?php } ?>