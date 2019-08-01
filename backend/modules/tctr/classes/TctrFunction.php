<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\tctr\classes;

use Yii;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfUiFunc;
use ZipArchive;
use backend\modules\tctr\classes\TctrValue;
use yii\helpers\Url;
use DateTime;

class TctrFunction {

    public static function Downloadxml($tctrid) {
        $url = file_get_contents('http://www.clinicaltrials.in.th/export/xmlv02/' . $tctrid . '.zip');
        $filezip = 'zip/' . $tctrid . '.zip';

        file_put_contents($filezip, $url);
        $zip = new ZipArchive;
        if ($zip->open($filezip) === TRUE) {
            $zip->extractTo('xmlfile/');
            $zip->close();
            return true;
        } else {
            return false;
        }
        unlink($filezip);
    }

    public static function DownloadAllXml($tctr) {
        $url = file_get_contents('http://www.clinicaltrials.in.th/export/xmlv02/' . $tctr . '.zip?20180430161957');
        $filezip = 'zip/' . $tctr . '.zip';
        file_put_contents($filezip, $url);
        $zip = new ZipArchive;
        if ($zip->open($filezip) === TRUE) {
            $zip->extractTo('xmlfile/');
            $zip->close();
            return true;
        } else {
            return false;
        }
        unlink($filezip);
    }

    public static function InsertdataNCT($ArrayData = []) {
        try {
            $Maindata = [];
            $idinfo = TctrValue::getValue($ArrayData['id_info']);
            $Maindata['trial_id'] = TctrValue::getValue($idinfo['nct_id']);
            $Maindata['public_title'] = TctrValue::getValue($ArrayData['brief_title']);
            $Maindata['ezf_version'] = 'v1';
            $Maindata['create_date'] = isset($ArrayData['study_first_submitted']) ? new DateTime($ArrayData['study_first_submitted']) : '';
            $Maindata['create_date'] = !empty($Maindata['create_date']) ? $Maindata['create_date']->format('Y-m-d') : '';
            $Maindata['date_enrolment'] = isset($ArrayData['start_date']) ? new DateTime($ArrayData['start_date']) : '';
            $Maindata['date_enrolment'] = !empty($Maindata['date_enrolment']) ? $Maindata['date_enrolment']->format('Y-m-d') : '';

            $Maindata['study_com_date'] = isset($ArrayData['completion_date']) ? new DateTime($ArrayData['completion_date']) : '';
            $Maindata['study_com_date'] = !empty($Maindata['study_com_date']) ? $Maindata['study_com_date']->format('Y-m-d') : '';

            $Maindata['primary_com_date'] = isset($ArrayData['primary_completion_date']) ? new DateTime($ArrayData['primary_completion_date']) : '';
            $Maindata['primary_com_date'] = !empty($Maindata['primary_com_date']) ? $Maindata['primary_com_date']->format('Y-m-d') : '';

            $eligibility = isset($ArrayData['eligibility']) ? $ArrayData['eligibility'] : '';
            $Maindata['inclusion_criteria'] = isset($eligibility['criteria']['textblock']) ? $eligibility['criteria']['textblock'] : '';
            $Maindata['agemin'] = isset($eligibility['minimum_age']) ? $eligibility['minimum_age'] : '';
            $Maindata['agemax'] = isset($eligibility['maximum_age']) ? $eligibility['maximum_age'] : '';
//        $Maindata['accept_healthy'] = isset($eligibility['healthy_volunteers']) ? $eligibility['healthy_volunteers'] : '';
            $Maindata['gender'] = isset($eligibility['gender']) ? $eligibility['gender'] : '';
            $Maindata['gender'] = TctrValue::getValue($Maindata['gender']) == "All" ? "Both" : TctrValue::getValue($Maindata['gender']);
            $Maindata['num_arms'] = isset($eligibility['number_of_arms']) ? $eligibility['number_of_arms'] : '';

            $Maindata['i_freetext'] = isset($ArrayData['brief_summary']['textblock']) ? $ArrayData['brief_summary']['textblock'] : '';
            $Maindata['i_freetext_detailed'] = isset($ArrayData['detailed_description']['textblock']) ? $ArrayData['detailed_description']['textblock'] : '';
            $Maindata['source_name'] = isset($ArrayData['source']) ? $ArrayData['source'] : '';
            $Maindata['target_size'] = isset($ArrayData['enrollment']) ? $ArrayData['enrollment'] : '';
            if ($ArrayData['overall_status'] == "Unknown status") {
                $Maindata['recruitment_status'] = self::ChangeChoice('1520776142078903600', 'recruitment_status', TctrValue::getValue($ArrayData['last_known_status']));
            } else {
                $Maindata['recruitment_status'] = self::ChangeChoice('1520776142078903600', 'recruitment_status', TctrValue::getValue($ArrayData['overall_status']));
            }
            $Maindata['phase'] = isset($ArrayData['phase']) ? TctrValue::GetPhase($ArrayData['phase']) : '';
            $Maindata['study_type'] = self::ChangeChoice('1520776142078903600', 'study_type', isset($ArrayData['study_type']) ? $ArrayData['study_type'] : '');
            $Maindata['party_name_title'] = isset($ArrayData['responsible_party']['investigator_full_name']) ? $ArrayData['responsible_party']['investigator_full_name'] : '';
            $Maindata['source_name'] = isset($ArrayData['responsible_party']['investigator_affiliation']) ? $ArrayData['responsible_party']['investigator_affiliation'] : '';

            $MainForm = self::backgroundInsert('1520776142078903600', '', '', $Maindata, 'main');
            //primary_outcome
            $primary_outcome = isset($ArrayData['primary_outcome']) ? $ArrayData['primary_outcome'] : '';
            if (isset($primary_outcome[0])) {
                foreach ($primary_outcome as $key => $value) {
                    $measure = isset($value['measure']) ? $value['measure'] : '';
                    $time_frame = isset($value['time_frame']) ? "[ Timeframe" . $value['time_frame'] . " ] " : '';
                    $description = isset($value['description']) ? $value['description'] : '';
                    $PartPrimary = [
                        'ezf_version' => 'v1',
                        'outcome_name' => $measure . " " . $time_frame . " " . $description,
                        'primary_target' => $MainForm['data']['id'],
                    ];
                    self::backgroundInsert('1520779798000066500', '', '', $PartPrimary, 'part');
                }
            } elseif (isset($primary_outcome['measure'])) {
                $measure = isset($primary_outcome['measure']) ? $primary_outcome['measure'] : '';
                $time_frame = isset($primary_outcome['time_frame']) ? "[ Timeframe" . $primary_outcome['time_frame'] . " ] " : '';
                $description = isset($primary_outcome['description']) ? $primary_outcome['description'] : '';
                $PartPrimary = [
                    'ezf_version' => 'v1',
                    'outcome_name' => $measure . " " . $time_frame . " " . $description,
                    'primary_target' => $MainForm['data']['id'],
                ];
                self::backgroundInsert('1520779798000066500', '', '', $PartPrimary, 'part');
            }
            //secondary_outcome
            $secondary_outcome = isset($ArrayData['secondary_outcome']) ? $ArrayData['secondary_outcome'] : '';
            if (isset($secondary_outcome[0])) {
                foreach ($secondary_outcome as $key => $value) {
                    $measure = isset($value['measure']) ? $value['measure'] : '';
                    $time_frame = isset($value['time_frame']) ? "[ Timeframe " . $value['time_frame'] . " ]" : '';
                    $description = isset($value['description']) ? $value['description'] : '';
                    $PartSecondary = [
                        'ezf_version' => 'v1',
                        'outcome_name' => $measure . " " . $time_frame . " " . $description,
                        'sec_target' => $MainForm['data']['id'],
                    ];
                    self::backgroundInsert('1520780022010173600', '', '', $PartSecondary, 'part');
                }
            } elseif (isset($secondary_outcome['measure'])) {
                $$measure = isset($secondary_outcome['measure']) ? $secondary_outcome['measure'] : '';
                $time_frame = isset($secondary_outcome['time_frame']) ? ". [ Timeframe " . $secondary_outcome['time_frame'] . " ]" : '';
                $description = isset($secondary_outcome['description']) ? $secondary_outcome['description'] : '';
                $PartSecondary = [
                    'ezf_version' => 'v1',
                    'outcome_name' => $$measure . " " . $time_frame . "  " . $description,
                    'sec_target' => $MainForm['data']['id'],
                ];
                self::backgroundInsert('1520780022010173600', '', '', $PartSecondary, 'part');
            }
            $urldata = isset($ArrayData['required_header']['url']) ? $ArrayData['required_header']['url'] : '';
            $PartLink = [
                'ezf_version' => 'v1',
                'url' => $urldata,
                'link_target' => $MainForm['data']['id'],
            ];
            self::backgroundInsert('1520783205000098500', '', '', $PartLink, 'part'); //link
            $location = isset($ArrayData['location']['facility']) ? $ArrayData['location']['facility'] : '';
            $PartContact = [
                'ezf_version' => 'v1',
                'affiliation' => isset($location['name']) ? $location['name'] : '',
                'city' => isset($location['address']['city']) ? $location['address']['city'] : '',
                'zip' => isset($location['address']['zip']) ? $location['address']['zip'] : '',
                'country1' => isset($location['address']['country']) ? $location['address']['country'] : '',
                'contact_target' => $MainForm['data']['id'],
            ];
            self::backgroundInsert('1521692554044165600', '', '', $PartContact, 'part'); //sectonC

            return $MainForm;
        } catch (\yii\db\Exception $e) {
            echo $e;
            \appxq\sdii\utils\VarDumper::dump($ArrayData);
            exit();
        }
    }

    public static function InsertdataXML($dataid, $ArrayData, $tctrid) {
        try {
            $Maindata = TctrValue::getValue($ArrayData['main']);
            $criteria = TctrValue::getValue($ArrayData['criteria']);
            $agemin = isset($criteria['agemin']) ? $criteria['agemin'] : '';
            $agemax = isset($criteria['agemax']) ? $criteria['agemax'] : '';
            $Maindata['inclusion_criteria'] = isset($criteria['inclusion_criteria']) ? $criteria['inclusion_criteria'] : '';
            $Maindata['gender'] = TctrValue::getValue($criteria['gender']);
            $Maindata['exclusion_criteria'] = TctrValue::getValue($criteria['exclusion_criteria']);
            $Maindata['source_name'] = TctrValue::getValue($ArrayData['source_support']['source_name']);

            $Maindata['hc_freetext'] = TctrValue::getValue($Maindata['hc_freetext']);
            $Maindata['i_freetext'] = '';
//            if (strrpos(TctrValue::getValue($Maindata['i_freetext']), ",")) {
//                $i_freetext = explode(",", $Maindata['i_freetext']);
//                $Maindata['i_freetext'] = $i_freetext[0];
//                $Maindata['i_freetext_detailed'] = $i_freetext[1];
//            } else {
//                $Maindata['i_freetext'] = TctrValue::getValue($Maindata['i_freetext']);
//            }
            $secondary_ids = TctrValue::getValue($ArrayData['secondary_ids']['secondary_id']);
            $Maindata['sec_id'] = TctrValue::getValue($secondary_ids['sec_id']);
            $Maindata['issuing_authority'] = TctrValue::getValue($secondary_ids['issuing_authority']);
            $Maindata['study_sec_sponsor'] = isset($ArrayData['source_support']['source_name']) ? $ArrayData['source_support']['source_name'] : '';
            $Maindata['acronym'] = TctrValue::getValue($Maindata['acronym']);
            $Maindata['agemin'] = $agemin;
            $Maindata['agemax'] = $agemax;
            $Maindata['phase'] = TctrValue::GetPhase(TctrValue::getValue($ArrayData['main']['phase']));
            $Maindata['ezf_version'] = 'v1';
            $Maindata['create_date'] = isset($Maindata['date_registration']) ? TctrValue::FormatDayEzf($Maindata['date_registration']) : NULL;
            $Maindata['date_enrolment'] = TctrValue::FormatDayEzf(TctrValue::getValue($Maindata['date_enrolment']));
            $Maindata['recruitment_status'] = TctrValue::GetRecruitment_status(TctrValue::getValue($Maindata['recruitment_status']));
            $Maindata['study_design'] = TctrValue::GetStudy_design(TctrValue::getValue($Maindata['study_design']));
            if (strrpos(TctrValue::getValue($Maindata['url']), ",")) {
                $urldata = explode(",", $Maindata['url']);
            } else {
                $urldata = TctrValue::getValue($Maindata['url']);
            }
            $MainForm = self::backgroundInsert('1520776142078903600', $dataid, '', $Maindata, 'main');
            if ($MainForm) {
                if (count($urldata) > 1) {
                    foreach ($urldata as $key => $value) {
                        $PartLink = [
                            'ezf_version' => 'v1',
                            'url' => $value,
                            'link_target' => $MainForm['data']['id'],
                        ];
                        self::backgroundInsert('1520783205000098500', $dataid, '', $PartLink, 'part');
                    }
                } elseif (!empty($urldata) and count($urldata) == 1) {
                    $PartLink = [
                        'ezf_version' => 'v1',
                        'url' => $urldata,
                        'link_target' => $MainForm['data']['id'],
                    ];
                    self::backgroundInsert('1520783205000098500', $dataid, '', $PartLink, 'part');
                }
                $primary_outcome = isset($ArrayData['primary_outcome']) ? $ArrayData['primary_outcome'] : '';
                if (isset($$primary_outcome[0])) {
                    foreach ($primary_outcome['prim_outcome'] as $key => $value) {
                        $PartPrimary = [
                            'ezf_version' => 'v1',
                            'outcome_name' => $value,
                            'primary_target' => $MainForm['data']['id'],
                        ];
                        self::backgroundInsert('1520779798000066500', $dataid, '', $PartPrimary, 'part');
                    }
                } elseif (isset($primary_outcome['prim_outcome'])) {
                    $PartPrimary = [
                        'ezf_version' => 'v1',
                        'outcome_name' => $primary_outcome['prim_outcome'],
                        'primary_target' => $MainForm['data']['id'],
                    ];
                    self::backgroundInsert('1520779798000066500', $dataid, '', $PartPrimary, 'part');
                }
                $secondary_outcome = isset($ArrayData['secondary_outcome']) ? $ArrayData['secondary_outcome'] : '';
                if (isset($secondary_outcome['sec_outcome'][0])) {
                    foreach ($secondary_outcome['sec_outcome'] as $key => $value) {
                        $PartSecondary = [
                            'ezf_version' => 'v1',
                            'outcome_name' => $value,
                            'sec_target' => $MainForm['data']['id'],
                        ];

                        self::backgroundInsert('1520780022010173600', $dataid, '', $PartSecondary, 'part');
                    }
                } elseif (isset($secondary_outcome['sec_outcome']['sec_outcome']) ) {
                    $PartSecondary = [
                        'ezf_version' => 'v1',
                        'outcome_name' => $secondary_outcome['sec_outcome'],
                        'sec_target' => $MainForm['data']['id'],
                    ];
                    self::backgroundInsert('1520780022010173600', $dataid, '', $PartSecondary, 'part');
                }
                $contacts = isset($ArrayData['contacts']['contact']) ? $ArrayData['contacts']['contact'] : '';
                if (isset($contacts[0])) {
                    foreach ($contacts as $key => $value) {
                        $PartContact = $value;
                        $datamap = self::GetLocation($PartContact['city']);
                        $PartContact['map_lat'] = $datamap['results'][0]['geometry']['location']['lat'];
                        $PartContact['map_lng'] = $datamap['results'][0]['geometry']['location']['lng'];
                        $PartContact['ezf_version'] = 'v1';
                        $PartContact['middlename'] = '';
                        $PartContact['contact_target'] = $MainForm['data']['id'];
                        if ($value['type'] == 'public') {
                            self::backgroundInsert('1521692554044165600', $dataid, '', $PartContact, 'part');
                        } elseif ($value['type'] == 'scientific') {
                            self::backgroundInsert('1521705933021675000', $dataid, '', $PartContact, 'part');
                        }
                    }
                } elseif (isset($contacts['city'])) {
                    $PartContact = $contacts;
                    $datamap = self::GetLocation($PartContact['city']);
                    $PartContact['map_lat'] = $datamap['results'][0]['geometry']['location']['lat'];
                    $PartContact['map_lng'] = $datamap['results'][0]['geometry']['location']['lng'];
                    $PartContact['ezf_version'] = 'v1';
                    $PartContact['middlename'] = '';
                    $PartContact['contact_target'] = $MainForm['data']['id'];
                    if ($value['type'] == 'public') {
                        self::backgroundInsert('1521692554044165600', $dataid, '', $PartContact, 'part');
                    } elseif ($value['type'] == 'scientific') {
                        self::backgroundInsert('1521705933021675000', $dataid, '', $PartContact, 'part');
                    }
                }
            } else {
                return false;
            }
            return $MainForm;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function backgroundInsert($ezf_id, $dataid, $target, $initdata = [], $type) {
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        Yii::$app->session['show_varname'] = 0;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        $userProfile = \common\modules\user\models\User::findOne(['id' => '1'])->profile;
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        $targetReset = false;
        //ขั้นตอนกรอกข้อมูลสำคัญ
        $evenFields = EzfFunc::getEvenField($modelFields);
        $special = isset($evenFields['special']) && !empty($evenFields['special']);
        $fieldSpecial = EzfFunc::checkSpecial($model, $evenFields, $targetReset);
//        $checkdataid = (new \yii\db\Query)->select('*')->from('zdata_tctr_main')->where(['id'=>$dataid])->all();
        if ($model->id) {
            $dataTarget = EzfQuery::getTarget($modelEzf->ezf_table, $model->id);
        } else {
            $dataTarget = [];
        }
        //เพิ่มและแก้ไขข้อมูล system
        $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, NULL, 0);
        if ($dataTarget && $type == "main") {
//            $model->id = $dataid;
            self::DeleteData($modelEzf->ezf_table, $dataid);
        }
        if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
            $model->attributes = $initdata;
            if (isset($initdata['rstat'])) {
                $model->rstat = $initdata['rstat'];
            } else {
                $model->rstat = 1;
            }
            $model->user_update = $userProfile->user_id;
            $model->update_date = new \yii\db\Expression('NOW()');
            $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
            return $result;
        }
        return $model;
    }

    public static function DeleteData($ezf_table, $dataid) {
        $sql = "delete FROM $ezf_table WHERE id = :id AND rstat not in(0,3)";
        Yii::$app->db->createCommand($sql, [':id' => $dataid])->execute();
    }

    public static function QueryData($dataid, $table, $fieldkey) {
        $sql = "select * FROM `$table` WHERE `$fieldkey` = :id AND rstat not in(0,3)";
        return Yii ::$app->db->createCommand($sql, [':id' => $dataid])->queryAll();
    }

    public static function QuerysectionB($dataid, $table, $fieldkey) {
        $sql = "select site_country FROM `$table` WHERE `$fieldkey` = :id AND rstat not in(0,3)";
        return Yii ::$app->db->createCommand($sql, [':id' => $dataid])->queryAll();
    }

    public static function getAllChoice($ezf_id, $field_name) {
        $sql = "select 
            ezf_choicevalue as `value`,
            ezf_choicelabel 
            from ezform_fields fid
            INNER JOIN ezform_choice cho on cho.ezf_field_id = fid.ezf_field_id
            where fid.ezf_id=:ezf_id
            and fid.ezf_field_name=:field_name ";
        return Yii::$app->db->createCommand($sql, [
                    ':ezf_id' => $ezf_id,
                    ':field_name' => $field_name]
                )->queryAll();
    }

    public static function getOneChoice($ezf_id, $field_name, $choicevalue) {
        if (isset($choicevalue)) {
            $sql = "select 
                cho.ezf_choicelabel 
                from ezform_fields fid
                INNER JOIN ezform_choice cho on cho.ezf_field_id = fid.ezf_field_id
                where fid.ezf_id=:ezf_id
                and fid.ezf_field_name=:field_name
                and cho.ezf_choicevalue=:ezf_choicevalue";
            return Yii::$app->db->createCommand($sql, [
                        ':ezf_id' => $ezf_id,
                        ':field_name' => $field_name,
                        ':ezf_choicevalue' => $choicevalue,
                    ])->queryScalar();
        } else {
            return '';
        }
    }

    public static function ChangeChoice($ezf_id, $field_name, $choicevalue) {
        if (isset($choicevalue)) {
            $sql = "select 
                    cho.ezf_choicevalue 
                    from ezform_fields fid
                    INNER JOIN ezform_choice cho on cho.ezf_field_id = fid.ezf_field_id
                    where fid.ezf_id=:ezf_id
                    and fid.ezf_field_name=:field_name
                    and cho.ezf_choicelabel LIKE '%$choicevalue%'";
            $data = Yii::$app->db->createCommand($sql, [
                        ':ezf_id' => $ezf_id,
                        ':field_name' => $field_name,
//                        ':ezf_choicevalue' => $choicevalue,
                    ])->queryScalar();
            if ($data) {
                return $data;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public static function QueryAllMarkmap($where) {
        $sql = "select id,trial_id,city,public_title,map_lat,map_lng from
                (
                        select contact_target,city,map_lat,map_lng FROM `zdata_tctr_part_sectionc`
                        WHERE rstat not in(0,3) and map_lat is not null and map_lng is not null and map_lat <>'' and map_lng <>''
                        UNION
                        select contact_target,city,map_lat,map_lng FROM `zdata_tctr_part_sectiond` 
                        WHERE rstat not in(0,3) and map_lat is not null and map_lng is not null and map_lat <>'' and map_lng <>''
                ) as sys
                INNER JOIN zdata_tctr_main main on sys.contact_target = main.`id`
                where 1 $where ";
        $data = Yii ::$app->db->createCommand($sql)->queryAll();
        $changedata = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $changedata[$key][0] = "Trial_id:" . $value['trial_id'] . "<br>"
                        . "Public_Title:<a class='marker' href=" . Url::to(['/tctr/tctr-data/ezform-view',
                            'ezf_id' => '1520776142078903600',
                            'dataid' => $value['id'],
                            'modal' => 'modal-view',
                            'reloadDiv' => 'modal-view',
                            'type' => 'map',
                        ]) . " "
                        . ">" . $value['public_title'] . "</a>";
                $changedata[$key][1] = $value['map_lat'];
                $changedata[$key][2] = $value['map_lng'];
            }
        }
        return json_encode($changedata);
    }
        public static function GetLocation($city) {
        if(isset($city)) {
            $data = preg_replace('/[[:space:]]+/', '', trim(TctrValue::getValue($city)));
            $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDsVkCe_UXKDMt1uyS65gqLKA0IsjziWz0&address=" . $data . "";
            $content = file_get_contents($url);
            $json = json_decode($content, true);
            $datamap = TctrValue::getValue($json);
            if ($datamap['status'] == "OVER_QUERY_LIMIT") {
                $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDJkZNDJiHfOH0upP_w8nr9Sl_rMaspvSg&address=" . $data . "";
                $content = file_get_contents($url);
                $json = json_decode($content, true);
                $datamap = TctrValue::getValue($json);
                if ($datamap['status'] == "OVER_QUERY_LIMIT") {
                    $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCq1YL-LUao2xYx3joLEoKfEkLXsEVkeuk&address=" . $data . "";
                    $content = file_get_contents($url);
                    $json = json_decode($content, true);
                    $datamap = TctrValue::getValue($json);
                    if ($datamap['status'] == "OVER_QUERY_LIMIT") {
                        $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyA92rvG4O4vVGV9ZTicTZ2WL7839Q0jG5c&address=" . $data . "";
                        $content = file_get_contents($url);
                        $json = json_decode($content, true);
                        $datamap = TctrValue::getValue($json);
                        if ($datamap['status'] == "OVER_QUERY_LIMIT") {
                            $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDsVkCe_UXKDMt1uyS65gqLKA0IsjziWz0&address=" . $data . "";
                            $content = file_get_contents($url);
                            $json = json_decode($content, true);
                            $datamap = TctrValue::getValue($json);
                            if ($datamap['status'] == "OVER_QUERY_LIMIT") {
                                return 'over_query';
                            }
                        }
                    }
                }
            }
            return $datamap;
        } else {
            return '';
        }
    }

    public static function getMillisecTime() {
        list($t1, $t2) = explode(' ', microtime());
        $mst = str_replace('.', '', $t2 . $t1);
        return $mst;
    }

}
