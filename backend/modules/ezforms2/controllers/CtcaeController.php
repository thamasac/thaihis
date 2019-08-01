<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Response;

/**
 * Description of CtcaeController
 *
 * @author AR Soft
 */
class CtcaeController extends \yii\web\Controller {

//put your code here
    public function actionTest() {
        echo \dms\aomruk\widgets\DSCtcae::widget(['name' => 'test']);
    }

    public function actionGetSoc($q = null, $ctcae = null, $grade = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        $soc = false;

        if ($grade != null && $grade != '') {
            $soc = (new \yii\db\Query())->select('soc_id')->from('const_grade')->andWhere(['id' => $grade])->scalar();
        } else if ($ctcae != null && $ctcae != '') {
            $soc = (new \yii\db\Query())->select('soc_id')->from('const_ctcae_term')->andWhere(['id' => $ctcae])->scalar();
        }

        $data = (new \yii\db\Query())->select('id AS id,soc AS text')->from('const_soc')->where('1');
        if ($soc) {
            $data->andWhere(['id' => $soc]);
        }
        if ($q != null && $q != '') {
            $data->andWhere('soc LIKE :q', [':q' => "%$q%"]);
        }
        $data = $data->limit(50)->all();
        $out['results'] = array_values($data);
        return $out;
    }

    public function actionGetCtcaeTerms($q = null, $soc = null, $grade = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        $data = (new \yii\db\Query())->select('id AS id,ctcae_term AS text')->from('const_ctcae_term')->where('1');
        if ($grade != null && $grade != '') {
            $ctcae = (new \yii\db\Query())->select('ctcae_id')->from('const_grade')->andWhere(['id' => $grade])->scalar();
            if ($ctcae) {
                $data->andWhere(['id' => $ctcae]);
            }
            if ($soc == 100) {
                $data->orWhere(['sitecode' => \Yii::$app->user->identity->profile->sitecode]);
            }
        } else if ($soc != null && $soc != '') {
            $data->andWhere(['soc_id' => $soc]);
            if ($soc == 100) {
                $data->orWhere(['sitecode' => \Yii::$app->user->identity->profile->sitecode]);
            }
        }

        if ($q != null && $q != '') {
            $data->andWhere('ctcae_term LIKE :q', [':q' => "%$q%"]);
        }
        $data = $data->limit(50)->all();
        $out['results'] = array_values($data);
        return $out;
    }

    public function actionGetGrades($q = null, $soc = null, $ctcae = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        $data = (new \yii\db\Query())->select(["id as id", "CONCAT('(',grade,') ',grade_detail) as text"])->from('const_grade')->where('1');
        if ($soc != null && $soc != '') {
            $data->andWhere(['soc_id' => $soc]);
            if ($soc == 100) {
                $data->orWhere(['soc_id' => '100']);
            }
        }
        if ($ctcae != null && $ctcae != '' && $soc != 100) {
            $data->andWhere(['ctcae_id' => $ctcae]);
        }
        if ($q != null && $q != '') {
            $data->andWhere('CONCAT(\'(\',grade,\') \',grade_detail) LIKE :q', [':q' => "%$q%"]);
        }
//                ->where('soc_id = :id', [':id' => $id])
//                ->all();
        $data = $data->limit(50)->all();
        $out['results'] = array_values($data);
//        \appxq\sdii\utils\VarDumper::dump($out);
//        $out = array_values($data);
        return $out;
    }

    public function actionAddCtcaeTerm() {
        $id = \Yii::$app->request->get('id', '');
        $socId = \Yii::$app->request->get('soc_id', '');
        $data = null;
        if ($id != '') {
            $data = (new \yii\db\Query())->select('*')->from('const_ctcae_term')->where(['id' => $id])->one();
        }
        return $this->renderAjax('_add-ctcae', ['data' => $data, 'socId' => $socId]);
    }

    public function actionSaveCtcaeTerm() {
        $id = \Yii::$app->request->post('id', '');
        $socId = \Yii::$app->request->post('soc_id', '');
        $ctcae_term = \Yii::$app->request->post('ctcae_term', '');

        if ($id != '') {
            try {
                (new \yii\db\Query())->createCommand()->update('const_ctcae_term', ['ctcae_term' => $ctcae_term, 'soc_id' => $socId], ['id' => $id])->execute();
                return TRUE;
            } catch (\yii\db\Exception $error) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                return FALSE;
            }
        } else {
            try {
                (new \yii\db\Query())->createCommand()->insert('const_ctcae_term', ['ctcae_term' => $ctcae_term, 'soc_id' => $socId, 'sitecode' => \Yii::$app->user->identity->profile->sitecode])->execute();
                return TRUE;
            } catch (\yii\db\Exception $error) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                return FALSE;
            }
        }
    }

    public function actionAddGrade() {
        $id = \Yii::$app->request->get('id', '');
        $socId = \Yii::$app->request->get('soc_id', '');
        $ctcaeId = \Yii::$app->request->get('ctcae_id', '');
        $data = null;
        if ($id != '') {
            $data = (new \yii\db\Query())->select('*')->from('const_grade')->where(['id' => $id])->one();
        }
        return $this->renderAjax('_add-grade', ['data' => $data, 'socId' => $socId, 'ctcaeId' => $ctcaeId]);
    }

    public function actionSaveGrade() {
        $id = \Yii::$app->request->post('id', '');
        $socId = \Yii::$app->request->post('soc_id', '');
        $ctcaeId = \Yii::$app->request->post('ctcae_id', '');
        $grade = \Yii::$app->request->post('grade', '');
        $grade_detail = \Yii::$app->request->post('grade_detail', '');
        if ($id != '') {
            try {
                (new \yii\db\Query())->createCommand()->update('const_grade', ['grade' => $grade, 'grade_detail' => $grade_detail], ['id' => $id])->execute();
                return TRUE;
            } catch (\yii\db\Exception $error) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                return FALSE;
            }
        } else {
            try {
                (new \yii\db\Query())->createCommand()->insert('const_grade', ['grade' => $grade, 'grade_detail' => $grade_detail, 'soc_id' => $socId, 'ctcae_id' => $ctcaeId, 'sitecode' => \Yii::$app->user->identity->profile->sitecode])->execute();
                return TRUE;
            } catch (\yii\db\Exception $error) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                return FALSE;
            }
        }
    }

    public function actionGetCtcaeTerm() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = empty($parents[0]) ? null : $parents[0];
                if ($id != null) {
                    $param1 = null;
                    if (!empty($_POST['depdrop_params'])) {
                        $params = $_POST['depdrop_params'];
                        $param1 = $params[0]; // get the value of input-type-1
                    }
//                    $pid = Yii::$app->db->createCommand("SELECT id FROM const_soc WHERE id = :id", [':id' => $id])->queryScalar();
//                    $sql = "SELECT `id` AS id,`ctcae_term` AS name FROM `zdata_const_ctcae` WHERE `soc_id` = :id";
//                    $data = Yii::$app->db->createCommand($sql, [':id' => $id])->queryAll();
                    $data = (new \yii\db\Query())->select('id AS id,ctcae_term AS name')->from('const_ctcae_term')
                            ->where('soc_id = :id', [':id' => $id])
                            ->all();

                    $out = array_values($data);
                    echo Json::encode(['output' => empty($out) ? '' : $out, 'selected' => $param1]);

                    return;
                }
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionGetGrade() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            $ids = $_POST['depdrop_parents'];
            $pid = empty($ids[0]) ? null : $ids[0];
            $aid = empty($ids[1]) ? null : $ids[1];
            if ($aid != null) {
                $param1 = null;
                if (!empty($_POST['depdrop_params'])) {
                    $params = $_POST['depdrop_params'];
                    $param1 = $params[0]; // get the value of input-type-1
                }

//                $id = Yii::$app->db->createCommand("SELECT id FROM const_ctcae_term WHERE id = :id", [':id' => $aid])->queryScalar();
//                $sql = "SELECT `id` as id,CONCAT('(',grade,') ',grade_de) as name FROM `zdata_const_grade` WHERE `ctcae_id` = :aid";
//                $pid = (new \yii\db\Query())->select('id')->from('zdata_const_ctcae')->where('soc_id = :id AND rstat not in (0,3)', [':id' => $pid])->scalar();
//                $data = Yii::$app->db->createCommand($sql, [':aid' => $pid])->queryAll();
                $data = (new \yii\db\Query())->select(["id as id", "CONCAT('(',grade,') ',grade_detail) as name"])->from('const_grade')->where('ctcae_id = :aid AND soc_id = :sid', [':aid' => $aid, ':sid' => $pid])->all();
//                \appxq\sdii\utils\VarDumper::dump($pid);
                $out = array_values($data);
                echo Json::encode(['output' => empty($out) ? '' : $out, 'selected' => $param1]);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

}
