<?php

namespace cpn\chanpan\classes;

class CNAuthQuery {

    public static function getRoleName($user_id, $sitecode) {
        $roles = [];
        $data = (new \yii\db\Query())
                ->select("*")
                ->from("zdata_matching")
                ->where("user_id LIKE :user_id AND sitecode=:sitecode", [':user_id' => "%{$user_id}%", ':sitecode' => $sitecode])
                ->andWhere('rstat <> 3 AND rstat <> 0')
                ->all();

        if (!empty($data)) {

            foreach ($data as $k => $v) {
                $role_arr = \appxq\sdii\utils\SDUtility::string2Array($v['user_id']);
                if (in_array($user_id, $role_arr)) {
                    $role_name = \backend\modules\manageproject\classes\CNRole::getRoleName($v['role_name']);
                    $role_arr = ['role_name' => $role_name, 'user_id' => $role_arr];
                    array_push($roles, $role_arr);
                }
            }
        }
        return $roles;
    }

    public static function get_role_name($user_id, $sitecode) {
        $roles = [];
        $data = (new \yii\db\Query())
                ->select("*")
                ->from("zdata_matching")
                ->where("user_id LIKE :user_id AND sitecode=:sitecode", [':user_id' => "%{$user_id}%", ':sitecode' => $sitecode])
                ->andWhere('rstat <> 3 AND rstat <> 0')
                ->all();

        if (!empty($data)) {

            foreach ($data as $k => $v) {
                $role_arr = \appxq\sdii\utils\SDUtility::string2Array($v['user_id']);
                if (in_array($user_id, $role_arr)) {
                    $role_name = \backend\modules\manageproject\classes\CNRole::getRoleName($v['role_name']);
                    $role_arr = ['role_name' => $v['role_name'], 'user_id' => $role_arr];
                    array_push($roles, $role_arr);
                }
            }
        }
        return $roles;
    }

    public static function getPermissionModule($module_id, $sitecode) {


        $data = (new \yii\db\Query())
                ->select("*")
                ->from("zdata_permission_module")
                ->where("module_id=:module_id", [//
                    ":module_id" => $module_id,
                        //':sitecode'=>$sitecode
                ])
                ->andWhere('rstat <> 3 AND rstat <> 0')
                ->orderBy(['permission_type' => SORT_DESC])
                ->all();

        //\appxq\sdii\utils\VarDumper::dump($data);
        return isset($data) ? $data : '';
    }

}
