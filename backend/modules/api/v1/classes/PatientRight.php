<?php

/**
 * Created by PhpStorm.
 * User: tyroroto
 * Date: 12/12/2018 AD
 * Time: 12:48
 */

namespace backend\modules\api\v1\classes;

use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\thaihis\classes\ThaiHisQuery;
use yii\db\Expression;

class PatientRight {

    /**
     * @param $pt_id
     * @param $visit_type
     * @param $dept
     * @param $rightData
     * @return array
     */
    public static function validateRight($pt_id, $visit_type, $dept, $rightData) {
        $visit_dept = null;
        $data = [];
        if ($rightData['maininscl'] == 'OFC') {//ข้าราชการ
            if (in_array($rightData['subinscl'], ['E1', 'E2'])) { //รัฐวิสาหกิจ
                $data['right_code'] = 'ORI'; //ต้นสังกัด
                $data['right_sub_code'] = $rightData['subinscl'];
                $data['right_status'] = '2'; //ผ่าน
            } else if ($rightData['maininscl'] == 'OFC') {
                $data['right_code'] = $rightData['maininscl'];
                $data['right_sub_code'] = $rightData['subinscl'];
                $data['right_status'] = '2';
            }
        } else if ($rightData['maininscl'] == 'LGO') {//ข้าราชการท้องถิ่น
            if ($rightData['subinscl'] == 'L9') { //L9 ไม่ระบุตำแหน่ง ต้องพบเจ้าหน้าที่ ตรวจสอบสิทธิ
                $data['right_code'] = $rightData['maininscl'];
                $data['right_sub_code'] = $rightData['subinscl'];
                $data['right_status'] = '8'; //ต้องพบเจ้าหน้าที่ ตรวจสอบสิทธิ
            } else {
                $data['right_code'] = $rightData['maininscl'];
                $data['right_sub_code'] = $rightData['subinscl'];
                $data['right_status'] = '2';
            }
        } elseif ($rightData['maininscl'] == 'VOF' || $rightData['maininscl'] == 'UCS' || $rightData['maininscl'] == 'WEL' || $rightData['maininscl'] == 'SSS') {//UCS.WEL บัตรทอง ,SSS ประกันสังคม
            /**
             * VOF equal UCS and will use as UCS sub AE by notation
             */
            if ($rightData['maininscl'] == 'VOF') {
                $rightData['maininscl'] = 'UCS';
                $rightData['subinscl'] = 'AE';
            }
            $right_last = ThaiHisQuery::getPtRightLast($pt_id);
            if (isset($rightData['hmain']) && $rightData['hmain'] == '12276') { //โรงพยาบาลต้นสิทธิ
                $data['right_code'] = $rightData['maininscl'];
                $data['right_sub_code'] = $rightData['subinscl'];
                $data['right_hos_main'] = $rightData['hmain'];
                $data['right_status'] = '2';
            } else if ($right_last) {
                if (isset($rightData['hmain']) && $rightData['hmain'] !== $right_last['right_hos_main']) {
                    $data['right_code'] = $rightData['maininscl'];
                    $data['right_sub_code'] = $rightData['subinscl'];
                    $data['right_hos_main'] = $rightData['hmain'];
                    $data['right_status'] = '4'; //โรงพยาบาลต้นสิทธิมีการเปลี่ยนแปลง
                } else if (date($right_last['right_refer_end']) < date('Y-m-d')) {
                    $data['right_code'] = $right_last['right_code'];
                    $data['right_sub_code'] = $right_last['right_sub_code'];
                    $data['right_hos_main'] = $right_last['right_hos_main'];
                    $data['right_status'] = '3'; //สทธิ หมดอายุ ต้องพบเจ้าหน้าที่ ตรวจสอบสิทธิ
                } else {
                    $data['right_hos_main'] = $right_last['right_hos_main'];
                    $data['right_refer_no'] = $right_last['right_refer_no'];
                    $data['right_refer_end'] = $right_last['right_refer_end'];
                    $data['right_prove_no'] = $right_last['right_prove_no'];
                    $data['right_code'] = $right_last['right_code'];
                    $data['right_refer_start'] = $right_last['right_refer_start'];
                    $data['right_sub_code'] = $right_last['right_sub_code'];
                    $data['right_status'] = $right_last['right_status'];
                    $data['right_flag'] = $right_last['right_flag'];
                    $data['right_prove_end'] = $right_last['right_prove_end'];
                    $data['right_hos_refer'] = $right_last['right_hos_refer'];
                    $data['right_project_id'] = $right_last['right_project_id'];
                    $data['note_detail'] = $right_last['note_detail'];
                }
            } else if ($visit_type == '3') {
                $data['right_code'] = $rightData['maininscl'];
                $data['right_sub_code'] = $rightData['subinscl'];
                $data['right_hos_main'] = $rightData['hmain'];
                $data['right_status'] = '5';
            } else {
                $data['right_status'] = '7';
                $data['right_code'] = 'CASH';
            }
        } else {
            //กรณีสิทธไม่เข้าเงือนไขไดเลยหรือระบบตรวจสอบสิทธิ Online ใช้งานไม่ได้ให้เอาสิทธิเดิมมาใช้
            //ถ้าไม่มีสิทธิเดิมให้เป็นเงินสด
            $right_last = ThaiHisQuery::getPtRightLast($pt_id);
            if ($right_last) {
                if (isset($right_last['right_refer_end']) && in_array($right_last, ['VOF', 'UCS', 'WEL', 'SSS']) && date($right_last['right_refer_end']) < date('Y-m-d')) {
                    $data['right_code'] = $right_last['right_code'];
                    $data['right_sub_code'] = $right_last['right_sub_code'];
                    $data['right_hos_main'] = $right_last['right_hos_main'];
                    $data['right_status'] = '3'; //สทธิ หมดอายุ ต้องพบเจ้าหน้าที่ ตรวจสอบสิทธิ
                } else {
                    $data['right_hos_main'] = $right_last['right_hos_main'];
                    $data['right_refer_no'] = $right_last['right_refer_no'];
                    $data['right_refer_end'] = $right_last['right_refer_end'];
                    $data['right_prove_no'] = $right_last['right_prove_no'];
                    $data['right_code'] = $right_last['right_code'];
                    $data['right_refer_start'] = $right_last['right_refer_start'];
                    $data['right_sub_code'] = $right_last['right_sub_code'];
                    $data['right_status'] = '2'; //ไปก่อน
                    $data['right_flag'] = $right_last['right_flag'];
                    $data['right_prove_end'] = $right_last['right_prove_end'];
                    $data['right_hos_refer'] = $right_last['right_hos_refer'];
                    $data['right_project_id'] = $right_last['right_project_id'];
                    $data['note_detail'] = $right_last['note_detail'];
                }
            } else {
                $data['right_status'] = '7';
                $data['right_code'] = 'CASH';
            }
        }

        switch ($visit_type) {
            case '1': //opd checkup
                $visit_dept = null;
                break;
            case '2': //Appointment
                $dataApp = PatientQuery::getAppointPt($pt_id, $dept, new Expression('CURDATE()')); //check appoint
                LogStash::Log(\Yii::$app->user->id, 'validateRight:PatientQuery::getAppointPt', var_export($dataApp, true), '', 'thaihis');
                if ($dataApp) {
                    $visit_dept = $dataApp['app_dept'];
                    $ezfApp_id = \backend\modules\patient\Module::$formID['appoint'];
                    $ezfApp_tbname = \backend\modules\patient\Module::$formTableName['appoint'];
                    PatientFunc::saveDataNoSys($ezfApp_id, $ezfApp_tbname, $dataApp['app_id'], ['app_status' => '2']);
                }
                break;
            case '3': //refer
                $visit_dept = '1538031598039175900';
                break;
            case '4': //ส่งตรวจสอบสิทธิก่อน
                //$visit_dept = $dept ? $dept : '1536740859027234200'; //ถ้า $dept ไม่มี่ค่าให้ส่ง opd ทั่วไป
                $visit_dept = $dept ? $dept : null;
                break;
            default:
                $visit_dept = null;
        }

        LogStash::Log('1', 'ValidateRight', var_export([$pt_id, $visit_type, $dept, $rightData], true), var_export($data, true), 'thaihis');

        return array($visit_dept, $data);
    }

}
