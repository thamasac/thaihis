<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\classes;

use appxq\sdii\utils\SDUtility;
use backend\modules\api\v1\models\ApplicationInfo;
use backend\modules\api\v1\models\DownloadLinkModel;
use backend\modules\api\v1\models\UserApplicationInfo;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\models\Ezform;
use common\modules\user\classes\EzformQuery;
use common\modules\user\models\User;
use common\modules\user\classes\CNDepartment;
use yii\helpers\ArrayHelper;
use DateTime;
use DateTimeZone;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Exception;
use yii\base\ErrorException;

class MainQuery
{


    public static function GetHospitalInfo($hcode)
    {
        try {
            $query = (new Query())->select("*")->from('all_hospital_thai')->where(['hcode' => $hcode]);
            $res = $query->one();
            return $res;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function CheckToken($token)
    {
//        $isService = (new Query())->select('*')->from('user_service')->where(['api_key' => $token])->one();
        $isService = null;
        if ($isService != null) {
            $resUser = User::findIdentity($isService["user_id"]);
        } else {
            $resUser = User::findIdentityByAccessToken($token);
        }
        if ($resUser == null) return false;
        Yii::$app->user->login($resUser, 3600 * 1);
        return true;
    }

    public static function LoginWithToken($appInfo, $username, $token)
    {
        try {
            $resUser = self::CheckToken($token);
            $cause = "user not found.";
            if ($resUser) {
                $res_profile = (new Query())->select(['zdata_sitecode.site_detail', 'profile.*'])->from('profile')
                    ->innerJoin("zdata_sitecode", "profile.sitecode = zdata_sitecode.site_name")->where(['profile.user_id' => Yii::$app->user->id])->one();
                $department_name = ['department_name' => CNDepartment::getDepartmentValue(Yii::$app->user->id)];

                $res_profile = ArrayHelper::merge($department_name, $res_profile);
                if (!$res_profile) {
                    $cause = "user exist but profile not found.";
                    $result["success"] = false;
                    $result["message"] = $cause;
                    return $result;
                }
                $result["success"] = true;
                $result["data"] = $array = array(
                    'id' => Yii::$app->user->id,
                    'username' => Yii::$app->user->identity->attributes['username'],
                    'token' => $token,
                    'email' => Yii::$app->user->identity->attributes['email'],
                    'profile' => $res_profile,
                );

                $userAppInfo = UserApplicationInfo::find()->where(["user_id" => Yii::$app->user->id,
                    "device_id" => $appInfo->deviceId,
                    "application" => $appInfo->application])->one();
                if ($userAppInfo == null)
                    $userAppInfo = new UserApplicationInfo();
                $userAppInfo->setAttributes([
                    "user_id" => Yii::$app->user->id,
                    "application" => $appInfo->application,
                    "device_id" => $appInfo->deviceId,
                    "platform" => $appInfo->platform,
                    "last_active" => new Expression('NOW()'),
                    "version" => $appInfo->version], false);
                $userAppInfo->save();
                return $result;
            }
            $cause = "token not correct.";
            $result["success"] = false;
            $result["message"] = $cause;
            return $result;
        } catch (\Exception $e) {
            LogStash::ErrorEx($username, "Login", $e, "", $appInfo->application);
            return $e;
        }
    }


    /**
     * @param $appInfo ApplicationInfo
     * @param $username
     * @param $password
     * @return \Exception|ErrorException|InvalidConfigException|Exception
     */
    public static function Login($appInfo, $username, $password)
    {
        try {
            $query = (new Query())
                ->select(['username', 'id', 'email', 'password_hash', 'auth_key'])
                ->from('user')
                ->where(['username' => $username])
                ->orWhere(['email'=>$username]);
            $resUser = $query->one();
            $cause = "user not found -.";
            if ($resUser) {
                $res_profile = (new Query())->select(['zdata_sitecode.site_detail', 'profile.*'])->from('profile')
                    ->innerJoin("zdata_sitecode", "profile.sitecode = zdata_sitecode.site_name")->where(['profile.user_id' => $resUser['id']])->one();
                $department_name = ['department_name' => CNDepartment::getDepartmentValue($resUser['id'])];
                $res_profile = ArrayHelper::merge($department_name, $res_profile);
                if (!$res_profile) {
                    $cause = "user exist but profile not found.";
                    $result["success"] = false;
                    $result["message"] = $cause;
                    return $result;
                }
                if (\Yii::$app->getSecurity()->validatePassword($password, $resUser['password_hash'])) {
                    $result["success"] = true;
                    $result["data"] = $array = array(
                        'id' => $resUser["id"],
                        'username' => $resUser["username"],
                        'token' => $resUser["auth_key"],
                        'email' => $resUser["email"],
                        'profile' => $res_profile,
                    );

                    $userIdentity = User::findIdentity($resUser["id"]);
                    Yii::$app->user->login($userIdentity, 3600 * 60 * 24 * 365 * 10);
                    $sessionId = Yii::$app->session->getId();
                    $sessionDuration = Yii::$app->session->getTimeout();
                    $result["cookie"] = ["PHPSESSID" => $sessionId, "sessionDuration" => $sessionDuration];
                    $userAppInfo = UserApplicationInfo::find()->where(["user_id" => $resUser["id"], "device_id" => $appInfo->deviceId, "application" => $appInfo->application])->one();
                    if (!$userAppInfo)
                        $userAppInfo = new UserApplicationInfo();
                    $userAppInfo->setAttributes([
                        "user_id" => $resUser["id"],
                        "application" => $appInfo->application,
                        "device_id" => $appInfo->deviceId,
                        "platform" => $appInfo->platform,
                        "last_active" => new Expression('NOW()'),
                        "version" => $appInfo->version
                    ], false);
                    $userAppInfo->save();
                    return $result;
                }
                $cause = "password not correct.";
            }
            $result["success"] = false;
            $result["message"] = $cause;
            return $result;

        } catch (\Exception $e) {
            LogStash::ErrorEx($username, "Login", $e, "", $appInfo->application);
            return $e;
        }
    }

    // get new user add to tb_data_1 after sync
    public static function SyncFormCreatedRecordsByReference($ezf_id, $ref_ezf_id, $syncFrom, $syncTo)
    {
//        try {
            $table_name = EzformQuery::getFormTableName($ezf_id)->ezf_table;
            $ezform = EzfQuery::getEzformOne($ezf_id);
            $profile = Yii::$app->user->identity->profile;
            $query1 = (new Query())
                ->select("$ezform->ezf_table.*")
                ->from($ezform->ezf_table);
            if ($ref_ezf_id != null) {
                $ref_table_name = EzformQuery::getFormTableName($ref_ezf_id)->ezf_table;
                if ($ref_table_name != null) {
                    $query1->innerJoin($ref_table_name,
                        "$ref_table_name.target = $table_name.target AND $ref_table_name.rstat != 3 and $ref_table_name.create_date > :syncdate and $table_name.update_date < :syncdate",
                        [
                            ':sitecode' => $profile->sitecode,
                            ':syncdate' => $syncFrom
                        ]);
                }
            }

            switch ($ezform->public_listview) {
                case 0:
                    $query1->where([$ezform->ezf_table.'.user_create' => Yii::$app->user->id]);
                    if ($syncTo != null) {
                        $query1->andWhere(["<=", $table_name . ".update_date", $syncTo]);
                    }
                    return $query1->all();
                case 1:
                    if ($syncTo != null) {
                        $query1->andWhere(["<=", $table_name . ".update_date", $syncTo]);
                    }
                    return $query1->all();
                case 2:
                    $query1->where("$ezform->ezf_table.hsitecode = :sitecode", [':sitecode' => $profile->sitecode]);
                    if ($syncTo != null) {
                        $query1->andWhere(["<=", $table_name . ".update_date", $syncTo]);
                    }
                    return $query1->all();
                case 3:
                    $query1->where("$ezform->ezf_table.hsitecode = :sitecode", [':sitecode' => $profile->sitecode])
                        ->andWhere("$ezform->ezf_table.xdepartmentx > :department", [':department' => $profile->department]);
                    if ($syncTo != null) {
                        $query1->andWhere(["<=", $table_name . ".update_date", $syncTo]);
                    }

                    return $query1->all();
                default:
                    return new \Exception("Data privacy not exist.");
            }
//        } catch (\Exception $e) {
//            return $e;
//        }
    }

    public static function SyncFormRecords($ezf_id, $syncFrom, $syncTo = null)
    {
//        try {
            $table_name = EzformQuery::getFormTableName($ezf_id)->ezf_table;
            $ezform = EzfQuery::getEzformOne($ezf_id);
            $profile = Yii::$app->user->identity->profile;
            $query1 = (new Query())
                ->select("$ezform->ezf_table.*")
                ->from($ezform->ezf_table);

            switch ($ezform->public_listview) {
                case 0:
                    $query1->where(['user_create' => Yii::$app->user->id]);
                    if ($syncFrom != null) {
                        $query1->andWhere([">", $table_name . ".update_date", $syncFrom]);
                    }
                    if ($syncTo != null) {
                        $query1->andWhere(["<=", $table_name . ".update_date", $syncTo]);
                    }
                    return $query1->all();
                case 1:
                    if ($syncFrom != null) {
                        $query1->andWhere([">", $table_name . ".update_date", $syncFrom]);
                    }
                    if ($syncTo != null) {
                        $query1->andWhere(["<=", $table_name . ".update_date", $syncTo]);
                    }
                    return $query1->all();
                case 2:
                    $query1->where("$ezform->ezf_table.hsitecode = :sitecode", [':sitecode' => $profile->sitecode]);
                    if ($syncFrom != null) {
                        $query1->andWhere([">", $table_name . ".update_date", $syncFrom]);
                    }
                    if ($syncTo != null) {
                        $query1->andWhere(["<=", $table_name . ".update_date", $syncTo]);
                    }
                    return $query1->all();
                case 3:
                    var_dump($profile->department);
                    $query1->where("$ezform->ezf_table.hsitecode = :sitecode", [':sitecode' => $profile->sitecode])
                        ->andWhere("$ezform->ezf_table.xdepartmentx > :department", [':department' => $profile->department]);
                    if ($syncFrom != null) {
                        $query1->andWhere([">", $table_name . ".update_date", $syncFrom]);
                    }
                    if ($syncTo != null) {
                        $query1->andWhere(["<=", $table_name . ".update_date", $syncTo]);
                    }
                    return $query1->all();
                default:
                    return new \Exception("Data privacy not exist.");
            }
//        } catch (\Exception $e) {
//            return $e;
//        }
    }

    /**
     * Get form record in table with same sitecode
     * @param $ezf_id
     * @return array|\Exception|Exception
     */
    public static function GetFormRecords($ezf_id)
    {
        try {
            $ezform = EzfQuery::getEzformOne($ezf_id);
            $profile = Yii::$app->user->identity->profile;
            switch ($ezform->public_listview) {
                case 0:
                    $query1 = (new Query())
                        ->select("$ezform->ezf_table.*")
                        ->from($ezform->ezf_table)
                        ->where(['user_create' => Yii::$app->user->id])
                        ->andWhere(['not', ["$ezform->ezf_table.rstat" => 3]]);
                    return $query1->all();
                case 1:
                    $query1 = (new Query())
                        ->select("$ezform->ezf_table.*")
                        ->from($ezform->ezf_table)
                        ->where(['not', ["$ezform->ezf_table.rstat" => 3]]);
                    return $query1->all();
                case 2:
                    $query1 = (new Query())
                        ->select("$ezform->ezf_table.*")
                        ->from($ezform->ezf_table)
                        ->where("$ezform->ezf_table.hsitecode = :sitecode", [':sitecode' => $profile->sitecode])
                        ->andWhere(['not', ["$ezform->ezf_table.rstat" => 3]]);
                    return $query1->all();
                case 3:
                    $query1 = (new Query())
                        ->select("$ezform->ezf_table.*")
                        ->from($ezform->ezf_table)
                        ->where("$ezform->ezf_table.hsitecode = :sitecode", [':sitecode' => $profile->sitecode])
                        ->andWhere("$ezform->ezf_table.xdepartmentx = :department", [':department' => $profile->department])
                        ->andWhere(['not', ["$ezform->ezf_table.rstat" => 3]]);

                    return $query1->all();
                default:
                    return new \Exception("Data privacy not exist.");
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * @param $ezf_id
     * @return array|null|\yii\db\ActiveRecord|Ezform
     */
    public static function GetEzformRef($ezf_id)
    {
        $model = Ezform::find()
            ->innerJoin('ezform_fields', 'ref_ezf_id = ezform.ezf_id and ezf_target = 1')
            ->where('ezform_fields.ezf_id = :ezf_id', [':ezf_id' => $ezf_id])
            ->one();
        return $model;
    }

    static function GetNowSql()
    {
        $sql = "SELECT NOW()";
        try {
            $rs = Yii::$app->db->createCommand($sql)->queryOne()['NOW()'];
        } catch (Exception $e) {
            $rs = null;
        }
        return $rs;
    }

    public static function GetEzFields($ezf_id)
    {
        try {
            $query = (new Query())->select(
                ['ezform_fields.ezf_field_id',
                    'ezform_fields.ezf_field_name',
                    'ezform_fields.ezf_field_type',
                    'ezform_fields.ezf_field_lenght',
                    'ezform_fields.ezf_field_order',
                    'ezform_fields.table_field_type',
                    'ezform_fields.table_field_length',
                    'ezform.ezf_table',
                    'ezform_input.input_name',
                    'ezform_fields.ezf_field_label'])
                ->from('ezform')
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id AND ezform_fields.ezf_version = ezform.ezf_version')
                ->innerJoin('ezform_input', 'ezform_fields.ezf_field_type = ezform_input.input_id AND ezform_input.input_version = ezform.ezf_version')
                ->where(['ezform.ezf_id' => $ezf_id, 'enable_version' => 1])->limit(1);
            $res = $query->all();

            $query = (new Query())->select(
                ['ezform_fields.ezf_field_id',
                    'ezform_fields.ezf_field_name',
                    'ezform_fields.ezf_field_type',
                    'ezform_fields.ezf_field_lenght',
                    'ezform_fields.ezf_field_order',
                    'ezform_fields.ezf_field_sub_id',
                    'ezform_fields.ezf_field_sub_textvalue',
                    'ezform.ezf_table',
                    'ezform_fields.ezf_field_label'])
                ->from('ezform')
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id and ezform_fields.ezf_field_type = 0  AND ezform_fields.ezf_version = ezform.ezf_version')
                ->where(['ezform.ezf_id' => $ezf_id]);
            $res3 = $query->all();

            $query2 = (new Query())->select([
                'ezform_fields.ezf_field_id',
                'ezform_fields.ezf_field_name',
                'ezform_fields.ezf_field_type',
                'ezform_fields.ezf_field_lenght',
                'ezform_fields.ezf_field_order',
                'ezform.ezf_table',
                'ezform_fields.ezf_field_label'])
                ->from('ezform')
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id AND ezform_fields.ezf_version = ezform.ezf_version')
                ->where(['ezform.ezf_id' => $ezf_id])
                ->andWhere(['ezform_fields.ezf_field_type' => 21]);
            $res2 = $query2->all();
            $res = array_merge($res, $res2);
            $res = array_merge($res, $res3);


            for ($i = 0; $i < count($res); $i++) {
                if ($res[$i]["ezf_field_type"] == "13") {
                    $res[$i]["child_field_id"] = (new Query())
                        ->select('ezf_field_name')
                        ->from('ezform_fields')
                        ->where(['ezf_field_sub_id' => $res[$i]["ezf_field_id"]])->all();
                }
            }
            return $res;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function GetFavoriteEzform($user_id)
    {
        try {
            $query = (new Query())
                ->select(['ezf_id',])
                ->from('ezform_favorite')
                ->where(['userid' => $user_id]);
            return $query->all();
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * ไม่เอา id
     * @param $ezf_id
     * @param $ezf_version
     * @return \Exception
     */
    public static function GetEzFieldsOnly($ezf_id, $ezf_version)
    {
        try {
            $query = (new Query())
                ->select(['ezform_fields.ezf_field_id',
                    'ezform_fields.ezf_field_name',
                    'ezform_fields.ezf_field_label',
                    'ezform_fields.ezf_field_default',
                    'ezform_fields.ezf_field_type',
                    'ezform_fields.table_field_type',
                    'ezform_fields.table_field_length'])
                ->from('ezform_fields')
                ->where(['ezf_id' => $ezf_id])
                ->andWhere("(ezf_version=:ezf_version OR ezf_version='all')", [':ezf_version' => $ezf_version])
                ->andWhere("ezform_fields.ezf_field_name <> 'id' ");

            return $query->all();
        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function GetEZFormStructById($formId)
    {
        try {
            $out = [];

            $query = (new Query())
                ->select(['ezf_name', 'ezf_id', 'ezf_table', 'ezf_version'])
                ->from('ezform')
                ->where(['ezf_id' => $formId, 'enable_version' => 1])->limit(1);
            $out["ezform"] = $query->one();
            if (!$out["ezform"]) {
                return null;
            }
            $out["ezfields"] = MainQuery::GetEzFieldsOnly($formId, $out["ezform"]["ezf_version"]);
            $out["ezform"]["count"] = (new Query())->select("COUNT(*) as c")->from($out["ezform"]["ezf_table"])->one()['c'];
            $out["ezform"]["checksum"] = md5(json_encode($out));
            return $out;

        } catch (\Exception $e) {
            return $e;
        }
    }


    public static function GetEZFormById($formId)
    {
        try {
            $out = null;
            $ezform = EzfQuery::getEzformOne($formId);
            $selectFields = 'SELECT ezf_id, ref_ezf_id, ezf_field_id,ezf_field_name,ezf_field_label,ezf_field_type,ezf_field_order, ezf_field_ref, ezf_field_data FROM ezform_fields WHERE ezf_id = :ezf_id';
            $ezfield = Yii::$app->db->createCommand($selectFields, [':ezf_id' => $formId])->queryAll();

            $query_choices = (new Query())
                ->select(['ezform_fields.ezf_field_id', 'ezform_choice.ezf_choicelabel', 'ezform_choice.ezf_choice_id',
                    'ezform_choice.ezf_choicevalue', 'ezform_fields.ezf_field_label', 'ezform_fields.ezf_field_order',
                'ezform_fields.ref_ezf_id', 'ezform_fields.ref_field_id'])
                ->from('ezform_fields')
                ->where(['ezform_fields.ezf_id' => $formId])
                ->innerJoin('ezform_choice', 'ezform_fields.ezf_field_id = ezform_choice.ezf_field_id');
            $choices = $query_choices->all();

            $tempCondition = (new Query())->select('*')->from('ezform_condition')->where(['ezf_id' => $formId])->all();
            $condition = [];
            foreach ($tempCondition as $value) {
                $value['cond_require'] = json_decode($value['cond_require']);
                $value['cond_jump'] = json_decode($value['cond_jump']);
                array_push($condition, $value);
            }
            foreach ($condition as $value) {
                $value['cond_require'] = SDUtility::string2Array($value['cond_require']);
            }
            $result = [
                'ezform' => $ezform->attributes,
                'ezfields' => $ezfield,
                'condition' => $condition,
                'ezchoices' => $choices
            ];
            return $result;

        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function GetEZFormOnlyById($formId)
    {
        try {
            $out = null;
            $ezform = EzfQuery::getEzformOne($formId);
//            $query = (new Query())
//                ->select(['ezf_name', 'ezf_id', 'ezf_table','ezf_version','public_listview'])
//                ->from('ezform')
//                ->where(['ezf_id' => $formId,'enable_version' => 1])->limit(1);
//            $out = $query->one();
            return $ezform->attributes;

        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function GetEZFormByArrId($ezf_id_arr)
    {
        try {
            $out = null;
            $models = Ezform::find()->where(['ezf_id' => $ezf_id_arr])->all();
            $query = (new Query())
                ->select(['ref_ezf_id'])
                ->from('ezform_fields')
                ->where(['ezf_id' => $ezf_id_arr, 'ezf_target' => 1]);
            $output = $query->all();
            $arr = [];
            $arrRefEzf = [];
            $arrEzf = [];
            foreach ($models as $item) {
                array_push($arrEzf, $item->attributes);
            }
            $arr["ezforms"] = $arrEzf;
            foreach ($output as $item) {
                array_push($arrRefEzf, $item["ref_ezf_id"]);
            }
            $arr["ref_ezf_id"] = $arrRefEzf;
            return $arr;

        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function CreateDownloadLink($ezf_id, $ezf_table, $secret, $sitecode)
    {
        try {
            $offset = 0;
            $limit = 10000;
            $query = (new Query())
                ->select(['COUNT(*) as c'])
                ->where(['sitecode' => $sitecode])
                ->from($ezf_table);
            $count = $query->one()['c'];
            $division = $count / $limit;
            $links = [];
            if ($count > 0) {
                for ($i = 0; $i < $division; $i++) {

                    $query = (new Query())
                        ->select(['*'])
                        ->from($ezf_table)->where(['sitecode' => $sitecode])->offset($offset)->limit($limit);
                    $queryData = [];
                    $queryData['records'] = $query->all();

                    $offset += $limit;
                    $donwloadLink = new DownloadLinkModel();
                    $id = SDUtility::getMillisecTime();
                    $queryData["ezf_id"] = $ezf_id;
                    $donwloadLink->setAttribute('data_string', json_encode($queryData));
                    $donwloadLink->setAttribute('secret', $secret);
                    $donwloadLink->setAttribute('id', $id);
                    $donwloadLink->setAttribute('ezf_id', $ezf_id);
                    array_push($links, $id);
                    $donwloadLink->insert(false);
                }
                return $links;
            }
            return null;

        } catch (\Exception $e) {
            var_dump($e);
        } catch (\Throwable $e) {
            var_dump($e);
        }
        return null;
    }

    public static function ConvertUtcDate($dateString)
    {
        $date = new DateTime($dateString, new DateTimeZone("UTC"));
        $date->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        return $date;
    }

}
