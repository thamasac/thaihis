<?php

namespace backend\modules\ezforms2\controllers;

use appxq\sdii\utils\VarDumper;
use Yii;
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
class SelectRoleController extends Controller {

    public function actionCreate() {
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

    public function actionRole($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        $data = (new \yii\db\Query())->select(['role_name', 'role_detail'])
                ->from('zdata_role')
                ->where('CONCAT(role_name, role_detail) LIKE :q', [':q' => "%$q%"])
                ->andWhere('rstat not in(0,3)')
                ->all();
        $i = 0;

        foreach ($data as $value) {
            $out["results"][$i] = ['id' => $value['role_name'], 'text' => $value["role_detail"] . " (" . $value["role_name"] . ")"];
            $i++;
        }

//       return $out["results"][1] = [
//                'id'=>'1519708877000441500',
//                'text'=>'PI Principal Investigator'
//            ];
        return $out;
    }

    public static function initRole($model, $modelFields) {
        try {
            $code = $model[$modelFields['ezf_field_name']];
            $id = '';
            $dataQuery = (new \yii\db\Query())->select(['role_name', 'role_detail'])
                    ->from('zdata_role');

            $options = SDUtility::string2Array($modelFields['ezf_field_options']);

            if (isset($options['options']['multiple']) && $options['options']['multiple'] == 1 && is_array($code)) {

                foreach ($code as $v) {
                    $id .= "'" . $v . "',";
                }

                $id = substr($id, 0, strlen($id) - 1);
                $str = [];

                if ($id != '') {
                    try {
                        $data = $dataQuery->where("role_name IN ($id)")->all();
                        foreach ($data as $key => $value) {
                            $str[] = ['id'=>$value['role_name'],'name' => $value['role_detail'] . " (" . $value['role_name'] . ")"];
                        }
                    } catch (\yii\db\Exception $ex) {
                        EzfFunc::addErrorLog($ex);
                    }
                }
                return $str;
            } else {
                $id = "{$code}";
                try {
                    $data = $dataQuery->where("rstat not in(0,3) AND role_name =:role_name", [":role_name" => $id])->one();
//                \appxq\sdii\utils\VarDumper::dump($data);
                    if ($data) {
                        return $data['role_detail'] . " (" . $data['role_name'] . ")";
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

    public static function getRoleValue($model, $modelFields) {
        try {
            $code = $model[$modelFields['ezf_field_name']];
            $id = '';
            $multi = substr_count($code, '[');
            if ($multi > 0) {
                $code = SDUtility::string2Array($code);
                foreach ($code as $v) {
                    $id .= "'" . $v . "',";
                }
                $id = substr($id, 0, strlen($id) - 1);
            } else {
                $id = "'" . $code . "'";
            }
            $str = [];
            if ($id != '') {
                $data = (new \yii\db\Query())->select(['role_name', 'role_detail'])
                        ->from('zdata_role')
                        ->where("role_name IN ($id)")
                        ->andWhere('rstat not in(0,3)')
                        ->all();

                foreach ($data as $value) {
                    $str[] = $value['role_detail'] . " (" . $value['role_name'].")";//"<div style='margin-top:5px;' class='label label-primary'>" . $value['role_detail'] . " (" . $value['role_name'] . ")</div>";
                }
            }
//            \appxq\sdii\utils\VarDumper::dump($modelFields['ezf_field_name']);
            $str = join(' , ', $str);
            return $str;
        } catch (\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

}
