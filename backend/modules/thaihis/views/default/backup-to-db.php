<?php
use backend\modules\thaihis\classes\BackupFunc;
use yii\helpers\ArrayHelper;
use appxq\sdii\utils\SDUtility;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!isset(Yii::$app->session['dept_all'])){
    $dept = BackupFunc::getDeptAll();
    if($dept){
        Yii::$app->session['dept_all'] = $dept;
    } else {
        Yii::$app->session['dept_all'] = [];
    }
}

//dept
$dept_all = [];
if(empty(Yii::$app->session['dept_all'])){
    echo '<a class="btn btn-default" href="'.yii\helpers\Url::to(['/thaihis/default/configbku']).'"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('dept_all = empty');
} else {
    $dept_all = ArrayHelper::map(Yii::$app->session['dept_all'], 'unit_code', 'id');
}
//
//if(!(isset(Yii::$app->session['func']) && !empty(Yii::$app->session['func']))){
//    echo '<a class="btn btn-default" href="'.yii\helpers\Url::to(['/thaihis/default/configbku']).'"><-- Back</a><br><br>';
//    \appxq\sdii\utils\VarDumper::dump('func = empty');
//}
//
if(!(isset(Yii::$app->session['table_name']) && !empty(Yii::$app->session['table_name']))){
    echo '<a class="btn btn-default" href="'.yii\helpers\Url::to(['/thaihis/default/configbku']).'"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('table_name = empty');
}
//
if(!(isset(Yii::$app->session['bku_step']) && !empty(Yii::$app->session['bku_step']))){
    echo '<a class="btn btn-default" href="'.yii\helpers\Url::to(['/thaihis/default/configbku']).'"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('bku_step = empty');
}
//
////version
//$ezform = \backend\modules\thaihis\classes\BackupFunc::getVersionByTb(Yii::$app->session['table_name']);
//if($ezform && isset($ezform['ezf_version'])){
//    Yii::$app->session['bku_version'] = $ezform['ezf_version'];
//} else {
//    echo '<a class="btn btn-default" href="'.yii\helpers\Url::to(['/thaihis/default/configbku']).'"><-- Back</a><br><br>';
//    \appxq\sdii\utils\VarDumper::dump('ezf_version = empty');
//}
//
//if(!(isset(Yii::$app->session['bku_version']) && !empty(Yii::$app->session['bku_version']))){
//    echo '<a class="btn btn-default" href="'.yii\helpers\Url::to(['/thaihis/default/configbku']).'"><-- Back</a><br><br>';
//    \appxq\sdii\utils\VarDumper::dump('bku_version  = empty');
//}

$tbname = Yii::$app->session['table_name'];
$next = Yii::$app->session['bku_step'];

$s = isset($_GET['s'])?$_GET['s']:0;
$e = isset($_GET['e'])?$_GET['e']:$next;

$total = 0;
$active = 0;
$success = 0;
$error = 0;

$data = [];
try {
//    eval("\$data = {$func_name};");
    $data = BackupFunc::getLog($s, $e, $tbname);

} catch (\yii\base\Exception $ex) {
    echo '<a class="btn btn-default" href="'.yii\helpers\Url::to(['/thaihis/default/log-to-db']).'"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump($ex);
}
$total = count($data);
$text_error='';
if($total>0){
    foreach ($data as $key => $value) {
        $active++;
        try {
            //save data
            $dataset = SDUtility::string2Array($value['data_json']);
            if(!empty($dataset)){
                $save = BackupFunc::insertData($tbname, $dataset);
                if($save){
                    $success++;
                } else {
                    $error++;
                }
            } else {
                $error++;
            }


        } catch (\yii\base\Exception $ex) {
//            \appxq\sdii\utils\VarDumper::dump($ex);
            $error++;
            $text_error = ' <strong>Message</strong> '.$ex->getMessage();
        }

    }
}

if($s==0){
    $s = $s+1;
}

$s = $s+$e;
?>

<ul class="nav nav-tabs">
  <li role="presentation" ><a href="<?= yii\helpers\Url::to(['/thaihis/default/configbku']) ?>">Backup</a></li>
  <li role="presentation" class="active" ><a href="<?= yii\helpers\Url::to(['/thaihis/default/log-to-db']) ?>">Logs2DB</a></li>
</ul>

<br>

<div class="alert alert-info" role="alert"> <strong>Total</strong> <?= number_format($total)?> </div>
<div class="alert alert-warning" role="alert"> <strong>Active</strong> <?= number_format($active)?> </div>
<div class="alert alert-success" role="alert"> <strong>Success</strong> <?= number_format($success)?> </div>
<div class="alert alert-danger" role="alert"> <strong>Error</strong> <?= number_format($error)?> <?=$text_error?></div>

<a class="btn btn-default" href="<?= yii\helpers\Url::to(['/thaihis/default/log-to-db'])?>">Back</a> 
<a class="btn btn-success" href="<?= yii\helpers\Url::to(['/thaihis/default/backup-to-db', 's'=>$s, 'e'=>$e])?>">Next</a>
<br><br>
