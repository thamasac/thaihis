<?php

namespace backend\modules\ezforms2\controllers;

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
class SelectVisitController extends Controller {

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

    public function actionUser($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        $data = (new \yii\db\Query())->select(['user_id', 'firstname', 'lastname'])
                ->from('profile')
                ->where('sitecode=:sitecode AND CONCAT(firstname, lastname) LIKE :q', [':sitecode' => \Yii::$app->user->identity->profile->sitecode, ':q' => "%$q%"])
                ->limit(50)
                ->all();
        $i = 0;
//        \appxq\sdii\utils\VarDumper::dump($data);
        foreach ($data as $value) {
            $out["results"][$i] = ['id' => $value['user_id'], 'text' => $value["firstname"] . " " . $value["lastname"]];
            $i++;
        }

        if ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
        }

        return $out;
    }

    public static function initValue($model, $modelFields) {
        $value = $model[$modelFields['ezf_field_name']];

        $ezf_field = EzfQuery::getFieldByName($modelFields['ezf_id'], $modelFields['ezf_field_name']);
        $field_data = \appxq\sdii\utils\SDUtility::string2Array($ezf_field['ezf_field_data']);

        $dataItems = \backend\modules\subjects\classes\SubjectManagementQuery::getVisitScheduleByWidget($field_data['widget'], null, null, $value);

        return $dataItems;
    }

    public static function getUserValue($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        if (!is_array($code)) {
            $code = SDUtility::string2Array($code);
        }
        $id = join(',', $code);
        $str = [];
        if ($id != '') {
            $data = (new \yii\db\Query())->select(['firstname', 'lastname'])
                    ->from('profile')
                    ->where("sitecode=:sitecode AND user_id IN ({$id})", [':sitecode' => \Yii::$app->user->identity->profile->sitecode])
                    ->all();
//            $sql = "SELECT * FROM `const_icd9` WHERE `code`=:code";
//            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
//            \appxq\sdii\utils\VarDumper::dump($data);
            foreach ($data as $value) {

                $str[] = $value['firstname'] . ' ' . $value['lastname'];
            }
        }
        $str = join(',', $str);
//        \appxq\sdii\utils\VarDumper::dump($str);
        return $str;
    }

}
