<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\tmf\classes;

use appxq\sdii\utils\SDUtility;

/**
 * Description of TnfFn
 *
 * @author AR Soft
 */
class TmfFn {

    public static function GetUserName($userid) {
        $query = new \yii\db\Query();
        $result = $query->select('firstname,lastname')
                        ->from('profile')->where('user_id=:user_id', [':user_id' => $userid])->one();

        return $result;
    }

    public static function getRole($data) {
        $data_value = [];
        if (!empty($data)) {
            $query = new \yii\db\Query();
            $query->select('user_id')->from('zdata_matching')->innerJoin('user', 'zdata_matching.user_id LIKE concat(\'%"\', user.id ,\'"%\')')->where('user_id != "" OR user_id IS NOT NULL');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if ($key == 0) {
                        $query->andWhere('role_name = :role_name' . $key, [':role_name' . $key => $value]);
                    } else {
                        $query->orWhere('role_name = :role_name' . $key, [':role_name' . $key => $value]);
                    }
                }
            } else {
                $query->andWhere('role_name = :role_name', [':role_name' => $data]);
            }

            $data_role = $query->all();
            foreach ($data_role as $key => $value) {
                $data_id = SDUtility::string2Array($value['user_id']);
                foreach ($data_id as $vId) {
                    $data_value[] = $vId;
                }
            }
        }
        return $data_value;
    }

    public static function saveDocName($docType = '', $docName = '') {
        if ($docType != '' && $docName != '') {
            try {
                \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert('1519736488056830700', '', $docType, ['F2v1' => $docType, 'F2v2' => $docName, 'ezf_version' => 'v1']);
                return true;
            } catch (yii\db\Exception $error) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                return false;
            }
//            $model->F2v1 = $docType;
//            $model->F2v2 = $docName;
//            if($model->save()){
//                return true;
//            }else{
//                return false;
//            }
        }else{
            return false;
        }
    }

}
