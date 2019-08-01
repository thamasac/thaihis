<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 6/20/2018
 * Time: 9:25 PM
 */

namespace backend\modules\ezforms2\controllers;

use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformMobile;
use yii\web\Response;
use backend\modules\ezforms2\models\EzformSearch;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class MobileController extends Controller
{
    public function actionIndex() {

        $query = (new Query())->select(['ezform.*'])
            ->from('ezform')
            ->innerJoin('ezform_mobile', 'ezform_mobile.ezf_id=ezform.ezf_id')
            ->where('ezform_mobile.userid=:userid', [':userid'=> Yii::$app->user->id])
            ->orderBy('ezform_mobile.forder')
            ->all();
        return $this->render('index', [  "favoriteForm" => $query]);
    }


    public function actionList()
    {
        $searchModel = new EzformSearch();
        $tab = Yii::$app->request->get('tab', '1');
        $dataProvider = $searchModel->searchMyForm(Yii::$app->request->queryParams, $tab);
        $modelFav = EzformMobile::find()
            ->where('userid=:userid', [':userid' => Yii::$app->user->id])->all();

        return $this->renderAjax('_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tab' => $tab,
            'modelFav' => $modelFav,
        ]);
    }

    public function actionAdd($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = EzformMobile::find()
                ->where('ezf_id=:ezf_id and userid=:userid', [
                    ':ezf_id'=>$ezf_id,
                    ':userid'=> Yii::$app->user->id
                ])->one();
            if($model){
                $model->delete();
            } else {
                $model = new EzformMobile();
                $model->ezf_id = $ezf_id;
                $model->userid = Yii::$app->user->id;
                $model->forder = EzformMobile::getOrder(Yii::$app->user->id);

                if($model->save()){
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $ezf_id,
                    ];

                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
                        'data' => $ezf_id,
                    ];

                    return $result;
                }
            }


        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }



}

