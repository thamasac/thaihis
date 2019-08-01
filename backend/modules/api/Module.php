<?php

namespace backend\modules\api;

use Yii;

class Module extends \yii\base\Module
{
    public $patientFormID = ['profile' => '1503378440057007100', 'visit' => '1503589101005614900', 'vs' => '1504083458067162000',
        'bmi' => '1504160572055696900', 'tk' => '1503904131053240800', 'pe_m' => '1503905825037005500', 'pe_f' => '1513607961029643200', 'di' => '1503906475038288200',
        'order_tran' => '1504537671028647300', 'soap' => '1504659278078885000', 'doctor' => '1504687634027501900',
        'admit' => '1504661230056849800', 'patientright' => '1505016314072939900', 'bed_tran' => '1505272580010394600', 'ward_bed' => '1504677752008586200',
        'discharge' => '1506496152086364900', 'visit_tran' => '1506694193013273800', 'appoint' => '1506908933027139000', 'advice' => '1506911890035349600',
        'refer_receive' => '1506927232048161000', 'staging' => '1507534686050918200', 'diag_como' => '1504880598029920400', 'diag_comp' => '1507729267040305200',
        'operat' => '1507731558041873700', 'app_conf' => '1508128862067166800', 'profilehistory' => '1513343009045814500', 'cytoreport' => '1513652249086894800',
        'app_checkup' => '1511490170071641200', 'receipt_mas' => '1514213281068509100', 'receipt_trn' => '1514213776022412800'];

    public $patientFormTableName = [
        'xrayreport' => 'zdata_xrayreport',
        'profile' => 'zdata_patientprofile', 'visit' => 'zdata_visit', 'vs' => 'zdata_vs',
        'bmi' => 'zdata_bmi', 'tk' => 'zdata_tk', 'pe_m' => 'zdata_pe_m', 'pe_f' => 'zdata_pe_f', 'di' => 'zdata_dt',
        'order_tran' => 'zdata_order_tran', 'soap' => 'zdata_soap', 'doctor' => 'zdata_doctor',
        'admit' => 'zdata_admit', 'patientright' => 'zdata_patientright', 'bed_tran' => 'zdata_bed_tran', 'ward_bed' => 'zdata_ward_bed',
        'discharge' => 'zdata_discharge', 'visit_tran' => 'zdata_visit_tran', 'appoint' => 'zdata_appoint', 'advice' => 'zdata_advice',
        'refer_receive' => 'zdata_refer_receive', 'staging' => 'zdata_staging', 'diag_como' => 'zdata_diag_como', 'diag_comp' => 'zdata_diag_comp',
        'operat' => 'zdata_operat', 'app_conf' => 'zdata_app_conf', 'profilehistory' => 'zdata_patienthistory', 'cytoreport' => 'zdata_cytoreport',
        'app_checkup' => 'zdata_app_checkup', 'temptext' => 'zdata_temptext', 'receipt_mas' => 'zdata_receipt_mas', 'receipt_trn' => 'zdata_receipt_trn'];

    public function init()
    {
        parent::init();
    }
}
