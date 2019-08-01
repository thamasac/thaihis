<?php

namespace backend\modules\reports;

/**
 * reports module definition class
 */
class Module extends \yii\base\Module {

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'backend\modules\reports\controllers';
    public $patientFormID = ['profile' => '1503378440057007100', 'visit' => '1503589101005614900', 'vs' => '1504083458067162000',
        'bmi' => '1504160572055696900', 'tk' => '1503904131053240800', 'pe_m' => '1503905825037005500', 'pe_f' => '1513607961029643200', 'di' => '1503906475038288200',
        'order_tran' => '1504537671028647300', 'soap' => '1504659278078885000', 'doctor' => '1504687634027501900',
        'admit' => '1504661230056849800', 'patientright' => '1505016314072939900', 'bed_tran' => '1505272580010394600', 'ward_bed' => '1504677752008586200',
        'discharge' => '1506496152086364900', 'visit_tran' => '1506694193013273800', 'appoint' => '1506908933027139000', 'advice' => '1506911890035349600',
        'refer_receive' => '1506927232048161000', 'staging' => '1507534686050918200', 'diag_como' => '1504880598029920400', 'diag_comp' => '1507729267040305200',
        'operat' => '1507731558041873700', 'app_conf' => '1508128862067166800', 'profilehistory' => '1513343009045814500', 'cytoreport' => '1513652249086894800',
        'app_checkup' => '1511490170071641200', 'receipt_mas' => '1514213281068509100', 'receipt_trn' => '1514213776022412800', 'report_checkup' => '1514016599071774100',
        'report_xray' => '1514170829022107900', 'report_ekg' => '1515119589019981900', 'project_patient_name' => '1544027808033655800',
        'pism_class' => '1515478343034651900', 'pism_type' => '1515480819083741900', 'pism_unit' => '1515481443046346400',
        'pism_use' => '1515482114005963500', 'pism_timeframe' => '1515482776063894300', 'pism_timeuse' => '1515484246075927100', 'pism_weightunit' => '1515485184036550700',
        'pism_item' => '1515588745039739100', 'pism_dosage' => '1515636336011013800', 'pism_tradname' => '1515644187085071900', 'pis_order' => '1516070151062902500', 'pis_order_tran' => '1516073084076581100',
        'pism_item2' => '1517015788080792100', 'pis_order2' => '1517015674031239200', 'pis_order_tran2' => '1517015701090624300', 'project' => '1513579791010722400'];
    public $patientFormTableName = [
        'xrayreport' => 'zdata_xrayreport',
        'profile' => 'zdata_patientprofile', 'visit' => 'zdata_visit', 'vs' => 'zdata_vs',
        'bmi' => 'zdata_bmi', 'tk' => 'zdata_tk', 'pe_m' => 'zdata_pe_m', 'pe_f' => 'zdata_pe_f', 'di' => 'zdata_dt',
        'order_tran' => 'zdata_order_tran', 'soap' => 'zdata_soap', 'doctor' => 'zdata_doctor',
        'admit' => 'zdata_admit', 'patientright' => 'zdata_patientright', 'bed_tran' => 'zdata_bed_tran', 'ward_bed' => 'zdata_ward_bed',
        'discharge' => 'zdata_discharge', 'visit_tran' => 'zdata_visit_tran', 'appoint' => 'zdata_appoint', 'advice' => 'zdata_advice',
        'refer_receive' => 'zdata_refer_receive', 'staging' => 'zdata_staging', 'diag_como' => 'zdata_diag_como', 'diag_comp' => 'zdata_diag_comp',
        'operat' => 'zdata_operat', 'app_conf' => 'zdata_app_conf', 'profilehistory' => 'zdata_patienthistory', 'cytoreport' => 'zdata_reportcyto',
        'app_checkup' => 'zdata_app_checkup', 'temptext' => 'zdata_temptext', 'receipt_mas' => 'zdata_receipt_mas', 'receipt_trn' => 'zdata_receipt_trn',
        'report_checkup' => 'zdata_reportcheckup', 'report_xray' => 'zdata_reportxray', 'report_ekg' => 'zdata_reportekg',
        'pism_class' => 'zdata_pism_class', 'pism_type' => 'zdata_pism_type', 'pism_unit' => 'zdata_pism_unit', 'pism_use' => 'zdata_pism_use', 'pism_timeframe' => 'zdata_pism_timeframe',
        'pism_timeuse' => 'zdata_pism_timeuse', 'pism_weightunit' => 'zdata_pism_weightunit', 'pism_item' => 'zdata_pism_item', 'pism_dosage' => 'zdata_pism_dosage', 'pism_tradname' => 'zdata_pism_tradname',
        'pis_order' => 'zdata_pis_order', 'pis_order_tran' => 'zdata_pis_order_tran',
        'pism_item' => 'zdata_pism_item2', 'pis_order2' => 'zdata_pis_order2', 'pis_order_tran2' => 'zdata_pis_order_tran2', 'project' => 'zdata_project'];
    public $FormID = ['profile' => '1503378440057007100', 'visit' => '1503589101005614900', 'vs' => '1504083458067162000',
        'bmi' => '1504160572055696900', 'tk' => '1503904131053240800', 'pe_m' => '1503905825037005500', 'pe_f' => '1513607961029643200', 'di' => '1503906475038288200',
        'order_tran' => '1504537671028647300', 'soap' => '1504659278078885000', 'doctor' => '1504687634027501900',
        'admit' => '1504661230056849800', 'patientright' => '1505016314072939900', 'bed_tran' => '1505272580010394600', 'ward_bed' => '1504677752008586200',
        'discharge' => '1506496152086364900', 'visit_tran' => '1506694193013273800', 'appoint' => '1506908933027139000', 'advice' => '1506911890035349600',
        'refer_receive' => '1506927232048161000', 'staging' => '1507534686050918200', 'diag_como' => '1504880598029920400', 'diag_comp' => '1507729267040305200',
        'operat' => '1507731558041873700', 'app_conf' => '1508128862067166800', 'profilehistory' => '1513343009045814500', 'cytoreport' => '1513652249086894800',
        'app_checkup' => '1511490170071641200', 'receipt_mas' => '1514213281068509100', 'receipt_trn' => '1514213776022412800', 'report_checkup' => '1514016599071774100',
        'report_xray' => '1514170829022107900', 'report_ekg' => '1515119589019981900', 'pism_generic' => '1515477469063230200',
        'pism_class' => '1515478343034651900', 'pism_type' => '1515480819083741900', 'pism_unit' => '1515481443046346400',
        'pism_use' => '1515482114005963500', 'pism_timeframe' => '1515482776063894300', 'pism_timeuse' => '1515484246075927100', 'pism_weightunit' => '1515485184036550700',
        'pism_item' => '1515588745039739100', 'pism_dosage' => '1515636336011013800', 'pism_tradname' => '1515644187085071900', 'pis_order' => '1516070151062902500', 'pis_order_tran' => '1516073084076581100',
        'pism_item2' => '1517015788080792100', 'pis_order2' => '1517015674031239200', 'pis_order_tran2' => '1517015701090624300', 'sks' => '1524886524041096400'];
    public $FormTableName = [
//        'xrayreport' => 'zdata_xrayreport',
        'profile' => 'zdata_patientprofile', 'visit' => 'zdata_visit', 'vs' => 'zdata_vs',
        'bmi' => 'zdata_bmi', 'tk' => 'zdata_tk', 'pe_m' => 'zdata_pe_m', 'pe_f' => 'zdata_pe_f', 'di' => 'zdata_dt',
        'order_tran' => 'zdata_order_tran', 'soap' => 'zdata_soap', 'doctor' => 'zdata_doctor',
        'admit' => 'zdata_admit', 'patientright' => 'zdata_patientright', 'bed_tran' => 'zdata_bed_tran', 'ward_bed' => 'zdata_ward_bed',
        'discharge' => 'zdata_discharge', 'visit_tran' => 'zdata_visit_tran', 'appoint' => 'zdata_appoint', 'advice' => 'zdata_advice',
        'refer_receive' => 'zdata_refer_receive', 'staging' => 'zdata_staging', 'diag_como' => 'zdata_diag_como', 'diag_comp' => 'zdata_diag_comp',
        'operat' => 'zdata_operat', 'app_conf' => 'zdata_app_conf', 'profilehistory' => 'zdata_patienthistory', 'cytoreport' => 'zdata_reportcyto',
        'app_checkup' => 'zdata_app_checkup', 'temptext' => 'zdata_temptext', 'receipt_mas' => 'zdata_receipt_mas', 'receipt_trn' => 'zdata_receipt_trn',
        'report_checkup' => 'zdata_reportcheckup', 'report_xray' => 'zdata_reportxray', 'report_ekg' => 'zdata_reportekg', 'pism_generic' => 'zdata_pism_generic',
        'pism_class' => 'zdata_pism_class', 'pism_type' => 'zdata_pism_type', 'pism_unit' => 'zdata_pism_unit', 'pism_use' => 'zdata_pism_use', 'pism_timeframe' => 'zdata_pism_timeframe',
        'pism_timeuse' => 'zdata_pism_timeuse', 'pism_weightunit' => 'zdata_pism_weightunit', 'pism_item' => 'zdata_pism_item', 'pism_dosage' => 'zdata_pism_dosage', 'pism_tradname' => 'zdata_pism_tradname',
        'pis_order' => 'zdata_pis_order', 'pis_order_tran' => 'zdata_pis_order_tran',
        'pism_item' => 'zdata_pism_item2', 'pis_order2' => 'zdata_pis_order2', 'pis_order_tran2' => 'zdata_pis_order_tran2', 'sks' => 'zdata_sks'];

    /**
     * {@inheritdoc}
     */
    public function init() {
        parent::init();

        // custom initialization code goes here
    }

}
