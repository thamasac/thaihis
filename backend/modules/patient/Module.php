<?php

namespace backend\modules\patient;

use Yii;

/**
 * register module definition class
 */
class Module extends \yii\base\Module {

//    public $controllerNamespace = 'backend\modules\patient\controllers';

    static public $formID = [
        'visit' => '1503589101005614900',
        'visit_type' => '1517474101030781900',
        'visit_tran' => '1506694193013273800',
        'generate_number' => '1539327830012854300',
        'order_package' => '1538665924026293000',
        'profile' => '1503378440057007100', 'vs' => '1504083458067162000',
        'bmi' => '1504160572055696900', 'tk' => '1503904131053240800', 'pe_m' => '1503905825037005500', 'pe_f' => '1513607961029643200', 'di' => '1503906475038288200',
        'order_tran' => '1504537671028647300', 'soap' => '1544073468012843800', 'doctor' => '1504687634027501900',
        'admit' => '1504661230056849800', 'patientright' => '1505016314072939900',
        'bed_tran' => '1505272580010394600', 'ward_bed' => '1504677752008586200',
        'discharge' => '1506496152086364900', 'appoint' => '1506908933027139000', 'advice' => '1506911890035349600',
        'refer_receive' => '1506927232048161000', 'staging' => '1507534686050918200', 'diag_como' => '1504880598029920400', 'diag_comp' => '1507729267040305200',
        'operat' => '1507731558041873700', 'app_conf' => '1508128862067166800', 'profilehistory' => '1513343009045814500', 'cytoreport' => '1513652249086894800',
        'app_checkup' => '1511490170071641200', 'receipt_mas' => '1514213281068509100', 'receipt_trn' => '1514213776022412800', 'report_checkup' => '1514016599071774100',
        'report_xray' => '1514170829022107900', 'report_ekg' => '1515119589019981900',
        'pism_class' => '1515478343034651900', 'pism_type' => '1515480819083741900', 'pism_unit' => '1515481443046346400',
        'pism_use' => '1515482114005963500', 'pism_timeframe' => '1515482776063894300', 'pism_timeuse' => '1515484246075927100', 'pism_weightunit' => '1515485184036550700', 'pism_generic' => '1515477469063230200',
        'pism_item' => '1515588745039739100', 'pism_dosage' => '1515636336011013800', 'pism_tradname' => '1515644187085071900', 'pis_order' => '1516070151062902500', 'pis_order_tran' => '1516073084076581100',
        'diag_basic' => '1513396134023895700', 'project' => '1513579791010722400', 'project_patient_name' => '1544027808033655800', 'certificate' => '1520997145096407200',
        'tranfer' => '1544072622045409600', 'lab_external' => '1523947270038513500', 'drug_km4' => '1525233934043050300', 'food_order' => '1525666583098642200', 'nurse_note_main' => '1525704878006229600',
        'receipt_no' => '1527664185052797300', 'treatment' => '1527772049068890200', 'mapacc_pacs' => '1529408416056026100', 'warning' => '1529651796035921600', 'pis_order_count' => '1533105123019958400',
        'order_lists' => '1536050720044903200', 'order_header' => '1536726852029196700', 'hos_config' => '1543300631034291100',
        'pis_allergy' => '1533111256009157800', 'pis_package' => '1543373009005305900', 'pis_package_item' => '1543373104040051000',
        'pism_use_set' => '1528124034015757000',
        'pe' => '1503905825037005500',
        'ipd_receive_tran' => '1544338602074795800',
        'profilehistory_new' => '1545533461093478400',
        'dept_map_form' => '1546576077073826800',
    ];
    static public $formTableName = [
        'visit_type' => 'zdata_visit_type', 'hos_config' => 'zdata_hos_config',
        'xrayreport' => 'zdata_xrayreport', 'visit_tran' => 'zdata_visit_tran', 'generate_number' => 'zdata_gen_number',
        'profile' => 'zdata_patientprofile', 'visit' => 'zdata_visit', 'vs' => 'zdata_vs', 'order_package' => 'zdata_order_package',
        'bmi' => 'zdata_bmi', 'tk' => 'zdata_tk', 'pe_m' => 'zdata_pe_m', 'pe_f' => 'zdata_pe_f', 'di' => 'zdata_dt',
        'order_tran' => 'zdata_order_tran', 'soap' => 'zdata_soap', 'doctor' => 'zdata_doctor',
        'admit' => 'zdata_admit', 'patientright' => 'zdata_patientright',
        'bed_tran' => 'zdata_bed_tran', 'ward_bed' => 'zdata_ward_bed',
        'discharge' => 'zdata_discharge', 'appoint' => 'zdata_appoint', 'advice' => 'zdata_advice',
        'refer_receive' => 'zdata_refer_receive', 'staging' => 'zdata_staging', 'diag_como' => 'zdata_diag_como', 'diag_comp' => 'zdata_diag_comp',
        'operat' => 'zdata_operat', 'app_conf' => 'zdata_app_conf', 'profilehistory' => 'zdata_patienthistory', 'cytoreport' => 'zdata_reportcyto',
        'app_checkup' => 'zdata_app_checkup', 'temptext' => 'zdata_temptext', 'receipt_mas' => 'zdata_receipt_mas', 'receipt_trn' => 'zdata_receipt_trn',
        'report_checkup' => 'zdata_reportcheckup', 'report_xray' => 'zdata_reportxray', 'report_ekg' => 'zdata_reportekg',
        'pism_class' => 'zdata_pism_class', 'pism_type' => 'zdata_pism_type', 'pism_unit' => 'zdata_pism_unit', 'pism_use' => 'zdata_pism_use', 'pism_timeframe' => 'zdata_pism_timeframe',
        'pism_timeuse' => 'zdata_pism_timeuse', 'pism_weightunit' => 'zdata_pism_weightunit', 'pism_item' => 'zdata_pism_item', 'pism_dosage' => 'zdata_pism_dosage', 'pism_tradname' => 'zdata_pism_tradname',
        'pis_order' => 'zdata_pis_order', 'pis_order_tran' => 'zdata_pis_order_tran',
        'project' => 'zdata_project', 'project_patient_name' => 'zdata_project_patient_name', 'certificate' => 'zdata_certificate_doctor',
        'tranfer' => 'zdata_tranfer', 'lab_external' => 'zdata_lab_external', 'drug_km4' => 'zdata_drugtran_km4', 'food_order' => 'zdata_food_order', 'nurse_note_main' => 'zdata_nurse_note_main',
        'receipt_no' => 'zdata_receipt_no', 'treatment' => 'zdata_treatment', 'warning' => 'zdata_warning', 'pism_generic' => 'zdata_pism_generic',
        'pis_order_count' => 'zdata_pis_ordercount', 'order_lists' => 'zdata_order_lists', 'order_header' => 'zdata_order_header',
        'pis_allergy' => 'zdata_pis_allergy', 'pis_package' => 'zdata_pis_package', 'pis_package_item' => 'zdata_pis_package_item',
        'pism_use_set' => 'zdata_pis_use_set', 'pe' => 'zdata_pe',
        'ipd_receive_tran' => 'zdata_receive_tran',
        'profilehistory_new' => 'zdata_patienthistory_new',
        'dept_map_form' => 'zdata_dept_map_form',
    ];
    static public $dataidForm = [
        'hos_config' => '1544022740066495300'
    ];

    public function init() {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['patient'])) {
            Yii::$app->i18n->translations['patient'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/patient/messages'
            ];
        }

        if (!isset(Yii::$app->i18n->translations['ezform'])) {
            Yii::$app->i18n->translations['ezform'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@backend/modules/ezforms2/messages'
            ];
        }
    }

}
