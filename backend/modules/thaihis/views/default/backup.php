<?php

use backend\modules\thaihis\classes\BackupFunc;
use yii\helpers\ArrayHelper;
use appxq\sdii\utils\SDUtility;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!isset(Yii::$app->session['dept_all'])) {
    $dept = BackupFunc::getDeptAll();
    if ($dept) {
        Yii::$app->session['dept_all'] = $dept;
    } else {
        Yii::$app->session['dept_all'] = [];
    }
}

//dept
$dept_all = [];
if (empty(Yii::$app->session['dept_all'])) {
    echo '<a class="btn btn-default" href="' . yii\helpers\Url::to(['/thaihis/default/configbku']) . '"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('dept_all = empty');
} else {
    $dept_all = ArrayHelper::map(Yii::$app->session['dept_all'], 'unit_code', 'id');
}

if (!(isset(Yii::$app->session['func']) && !empty(Yii::$app->session['func']))) {
    echo '<a class="btn btn-default" href="' . yii\helpers\Url::to(['/thaihis/default/configbku']) . '"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('func = empty');
}

if (!(isset(Yii::$app->session['table_name']) && !empty(Yii::$app->session['table_name']))) {
    echo '<a class="btn btn-default" href="' . yii\helpers\Url::to(['/thaihis/default/configbku']) . '"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('table_name = empty');
}

if (!(isset(Yii::$app->session['bku_step']) && !empty(Yii::$app->session['bku_step']))) {
    echo '<a class="btn btn-default" href="' . yii\helpers\Url::to(['/thaihis/default/configbku']) . '"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('bku_step = empty');
}

//version
$ezform = \backend\modules\thaihis\classes\BackupFunc::getVersionByTb(Yii::$app->session['table_name']);
if ($ezform && isset($ezform['ezf_version'])) {
    Yii::$app->session['bku_version'] = $ezform['ezf_version'];
} else {
    echo '<a class="btn btn-default" href="' . yii\helpers\Url::to(['/thaihis/default/configbku']) . '"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('ezf_version = empty');
}

if (!(isset(Yii::$app->session['bku_version']) && !empty(Yii::$app->session['bku_version']))) {
    echo '<a class="btn btn-default" href="' . yii\helpers\Url::to(['/thaihis/default/configbku']) . '"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump('bku_version  = empty');
}

$func_name = Yii::$app->session['func'];
$tbname = Yii::$app->session['table_name'];
$next = Yii::$app->session['bku_step'];
$init = Yii::$app->session['bku_initdata'];
$v = Yii::$app->session['bku_version'];

$s = isset($_GET['s']) ? $_GET['s'] : 0;
$e = isset($_GET['e']) ? $_GET['e'] : $next;

$total = 0;
$active = 0;
$success = 0;
$error = 0;

$data = [];
try {
    eval("\$data = {$func_name};");
} catch (\yii\base\Exception $ex) {
    echo '<a class="btn btn-default" href="' . yii\helpers\Url::to(['/thaihis/default/configbku']) . '"><-- Back</a><br><br>';
    \appxq\sdii\utils\VarDumper::dump($ex);
}
$total = count($data);
$text_error = '';
if ($total > 0) {

    foreach ($data as $key => $value) {
        $active++;
        try {

            //version
            $value['ezf_version'] = $v;
            //dept
            if (isset($value['xdepartmentx'])) {
                if (isset($dept_all[$value['xdepartmentx']])) {
                    $value['xdepartmentx'] = $dept_all[$value['xdepartmentx']];
                }
            }
            //init
            $value = ArrayHelper::merge($value, $init);

            if ($tbname == 'zdata_appoint') {
                if (isset($value['app_dept'])) {
                    if (isset($dept_all[$value['app_dept']])) {
                        $value['app_dept'] = $dept_all[$value['app_dept']];
                    }
                }
            } elseif ($tbname == 'zdata_visit_tran') {
                if (isset($value['visit_tran_dept'])) {
                    if (isset($dept_all[$value['visit_tran_dept']])) {
                        $value['visit_tran_dept'] = $dept_all[$value['visit_tran_dept']];
                    }
                }
            }

            if ($tbname == 'zdata_order_tran') {
                //header by target
                if (Yii::$app->session['vn_id'] != $value['target']) {
                    $data_oh['id'] = SDUtility::getMillisecTime();
                    $data_oh['ptid'] = $value['ptid'];
                    $data_oh['sitecode'] = $value['sitecode'];
                    $data_oh['ptcode'] = $value['ptcode'];
                    $data_oh['ptcodefull'] = $value['ptcodefull'];
                    $data_oh['target'] = $value['target'];
                    $data_oh['hptcode'] = $value['hptcode'];
                    $data_oh['hsitecode'] = $value['hsitecode'];
                    $data_oh['xdepartmentx'] = $value['xdepartmentx'];
                    $data_oh['xsourcex'] = $value['xsourcex'];
                    $data_oh['sys_lat'] = $value['sys_lat'];
                    $data_oh['sys_lng'] = $value['sys_lng'];
                    $data_oh['error'] = $value['error'];
                    $data_oh['rstat'] = $value['rstat'];
                    $data_oh['user_create'] = $value['user_create'];
                    $data_oh['create_date'] = $value['create_date'];
                    $data_oh['user_update'] = $value['user_update'];
                    $data_oh['update_date'] = $value['update_date'];
                    $data_oh['ezf_version'] = $value['ezf_version'];

                    $data_oh['order_visit_id'] = $value['target'];
                    $data_oh['my_5b9897b983c68'] = $value['my_59ad6ccc47b10'];
                    $data_oh['order_no'] = SDUtility::getMillisecTime();
                    //$data_oh['order_doctor_id'] = $value['id'];
                    if (isset($value['order_tran_dept'])) {
                        if (isset($dept_all[$value['order_tran_dept']])) {
                            $data_ot['order_dept'] = $dept_all[$value['order_tran_dept']];
                        }
                    }
//                    $data_oh['order_dept'] = $value['id'];

                    $order_header = BackupFunc::insertLogs('zdata_order_header', $data_oh['id'], $data_oh);
                    if ($order_header) {
                        Yii::$app->session['vn_id'] = $value['target'];
                        Yii::$app->session['pt_id'] = $value['my_59ad6ccc47b10'];
                        Yii::$app->session['oh_id'] = $data_oh['id'];
                    } else {
                        continue;
                    }
                }

                $data_ot = $value;

                $data_ot['get_5b9897b983c68'] = $value['my_59ad6ccc47b10'];
                $data_ot['my_59ad6ccc47b10'] = $value['target'];
                $data_ot['order_header_id'] = Yii::$app->session['oh_id'];
                $data_ot['target'] = Yii::$app->session['oh_id'];
                $data_ot['order_tran_oi_type'] = 'OPD';
                $data_ot['order_tran_cashier_id'] = $value['order_tran_cashier_status'];
                $data_ot['order_tran_cashier_status'] = isset($value['order_tran_cashier_status']) && !empty($value['order_tran_cashier_status']) ? '2' : '1';
                if (isset($value['order_tran_dept'])) {
                    if (isset($dept_all[$value['order_tran_dept']])) {
                        $data_ot['order_tran_dept'] = $dept_all[$value['order_tran_dept']];
                    }
                }

                //order tran
                $log = BackupFunc::insertLogs($tbname, $data_ot['id'], $data_ot);
                if ($log) {
                    $success++;
                } else {
                    $error++;
                }
            } else {
                //log
                $log = BackupFunc::insertLogs($tbname, $value['id'], $value);
                if ($log) {
                    $success++;
                } else {
                    $error++;
                }
            }

            //save data
            //$save = BackupFunc::insertData($tbname, $value);
        } catch (\yii\base\Exception $ex) {
            $error++;
            $text_error = $ex->getMessage();
        }
    }
}

if ($s == 0) {
    $s = $s + 1;
}

$s = $s + $e;
?>

<ul class="nav nav-tabs">
  <li role="presentation" class="active"><a href="<?= yii\helpers\Url::to(['/thaihis/default/configbku']) ?>">Backup</a></li>
  <li role="presentation" ><a href="<?= yii\helpers\Url::to(['/thaihis/default/log-to-db']) ?>">Logs2DB</a></li>
</ul>
<br>

<div class="alert alert-info" role="alert"> <strong>Total</strong> <?= number_format($total) ?> </div>
<div class="alert alert-warning" role="alert"> <strong>Active</strong> <?= number_format($active) ?> </div>
<div class="alert alert-success" role="alert"> <strong>Success</strong> <?= number_format($success) ?> </div>
<div class="alert alert-danger" role="alert"> <strong>Error</strong> <?= number_format($error) ?> <?= $text_error ?></div>

<a class="btn btn-default" href="<?= yii\helpers\Url::to(['/thaihis/default/configbku']) ?>">Back</a> 
<a class="btn btn-success" href="<?= yii\helpers\Url::to(['/thaihis/default/backup', 's' => $s, 'e' => $e]) ?>">Next</a>

<br><br>
