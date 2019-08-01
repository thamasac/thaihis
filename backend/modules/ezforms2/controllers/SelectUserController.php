<?php

namespace backend\modules\ezforms2\controllers;

use appxq\sdii\utils\VarDumper;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\utils\SDUtility;

/**
 * Select2Controller implements the CRUD actions for EzformInput model.
 */
class SelectUserController extends Controller
{

    public function actionCreate()
    {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $row = isset($_POST['row']) ? $_POST['row'] : 0;

            $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/select2/_formitem', [
                'row' => $row,
            ]);

            $result = [
                'status' => 'success',
                'action' => 'create',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                'html' => $html,
            ];
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionUser($q = null, $id = null, $sitecode = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        $data = (new \yii\db\Query())->select(['user_id','email', 'firstname', 'lastname'])
            ->from('profile')
            ->innerJoin('user', 'user.id = profile.user_id')
            ->where('user.confirmed_at is not null AND user.blocked_at is NULL AND CONCAT(firstname, lastname) LIKE :q', [':q' => "%$q%"]);
        
    //return \yii\helpers\Json::encode($data->all());    
    //VarDumper::dump($data->all());
        
        if (is_null($sitecode)) {
            $data->andWhere('sitecode = :sitecode', [':sitecode' => \Yii::$app->user->identity->profile->sitecode]);
        }
        $data = $data->limit(50)->all();
        $i = 0;
        if ($data) {
            foreach ($data as $value) {
                $out["results"][$i] = ['id' => $value['user_id'], 'text' => $value["firstname"] . " " . $value["lastname"]." ({$value["firstname"]})"];
                $i++;
            }
        }
        return $out;
    }

    public static function initUser($model, $modelFields)
    {
        try {
            $code = $model[$modelFields['ezf_field_name']];
            $id = '';
            $dataQuery = (new \yii\db\Query())->select(['user_id AS id', 'CONCAT(firstname,\' \',lastname) AS name'])
                ->from('profile')
                ->innerJoin('user', 'user.id = profile.user_id')
                ->where("user.confirmed_at is not null AND user.blocked_at is null");

            $options = SDUtility::string2Array($modelFields['ezf_field_options']);
            if (isset($options['options']['all_user']) && $options['options']['all_user'] != 1) {
                $dataQuery->andWhere('sitecode=:sitecode', [':sitecode' => \Yii::$app->user->identity->profile->sitecode]);
            }

            if (isset($options['options']['multiple']) && $options['options']['multiple'] == 1 && is_array($code)) {

//             $code = SDUtility::string2Array($code); 
//                foreach ($code as $v) {
//                    $id .= "'" . $v . "',";
//                }
//                $id = substr($id, 0, strlen($id) - 1);
//                foreach ($code as $v) {
//                    $id .= "'" . $v . "',";
//                }
//                $id = substr($id, 0, strlen($id) - 1);
                $str = [];
                if (!empty($code)) {
                    try {
                        $data = $dataQuery->andWhere(['user_id' => $code])->all();
//                        foreach ($data as $key => $value) {
//                            $str[] = $value['firstname'] . " " . $value['lastname'];
//
//                        }
//                        VarDumper::dump($data);
                        return $data;
                    } catch (Exception $ex) {
                        EzfFunc::addErrorLog($ex);
                    }
                }

//                VarDumper::dump($str);
            } else {
                $id = "{$code}";
                try {
                    $data = $dataQuery->andWhere("user_id =:user_id", [":user_id" => $id])->one();
//                \appxq\sdii\utils\VarDumper::dump($data);
                    if ($data) {
                        return $data['name'];
                    } else {
                        return '';
                    }
                } catch (\yii\db\Exception $ex) {
                    EzfFunc::addErrorLog($ex);
                }
            }
        } catch (\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    public static function getUserValue($model, $modelFields)
    {
        try {
            $code = $model[$modelFields['ezf_field_name']];
            $multi = substr_count($code, '[');
//            $id = null;
            $str = null;
            $data = (new \yii\db\Query())->select(['firstname', 'lastname', 'email'])
                ->from('profile')
                ->innerJoin('user', 'user.id = profile.user_id')
                ->where("user.confirmed_at is not null AND user.blocked_at is null ");
            $options = SDUtility::string2Array($modelFields['ezf_field_options']);
            if (isset($options['options']['all_user']) && $options['options']['all_user'] != 1) {
                $data->andWhere('sitecode = :sitecode', [':sitecode' => \Yii::$app->user->identity->profile->sitecode]);
            }
            if ($multi > 0) {
                $code = SDUtility::string2Array($code);
//                $id = $code;
                if (!empty($code)) {
                    try {
                        $data = $data->andWhere(['user_id' => $code])->all();
                        foreach ($data as $value) {
                            $str[] = $value['firstname'] . ' ' . $value['lastname']; //"<div style='margin-top:5px;' class='label label-primary'>" . $value['firstname'] . ' ' . $value['lastname'] . "</div>";
                        }
                        $str = join(' , ', $str);
                    } catch (\yii\db\Exception $ex) {
                        EzfFunc::addErrorLog($ex);
                    }
                }
            } else {
                if ($code != '') {
                    try {
                        $data = $data->andWhere(['user_id' => $code])->one();
                        if ($data) {
                            $str = $data['firstname'] . ' ' . $data['lastname']." {$data['email']}";
                        }
                    } catch (\yii\db\Exception $ex) {
                        EzfFunc::addErrorLog($ex);
                    }
                }
            }
//            \appxq\sdii\utils\VarDumper::dump($code);
            return $str;
        } catch (\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

}
