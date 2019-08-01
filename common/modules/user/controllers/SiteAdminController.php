<?php

namespace common\modules\user\controllers;

use dektrium\user\controllers\AdminController as BaseAdminController;
use dms\aomruk\classese\Notify;
use yii\filters\AccessControl;
use yii\helpers\Url;
use Yii;
use common\modules\user\models\Profile;
use common\modules\user\models\User;
use yii\web\Response;

class SiteAdminController extends BaseAdminController
{


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function actionValidateAjax()
    {
        $model = new Profile();
        $model->position = 0;
        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax) {
            if ($model->load(\Yii::$app->request->post())) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                echo json_encode(\yii\bootstrap\ActiveForm::validate($model));
                \Yii::$app->end();
            }
        }
    }

    public function actionUserRequestView()
    {
        return $this->renderAjax('user_view');
    }


    public function actionAllowSiteRequest($request_id, $target_site)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->db->createCommand()->update("zdata_site_request", ["rstat" => 2, "approve_result" => 4], "(approve_result = 0 OR approve_result IS NULL) AND  id <> :requestId AND user_create = :user_id ", [":requestId" => $request_id,":user_id" => Yii::$app->user->id])->execute();
        return ["success" => false, 'message' => 'currently site request always need approve.'];
//        $sitePermission = Yii::$app->db->createCommand("SELECT permission,site_detail FROM zdata_sitecode WHERE site_name = :target", [":target" => $target_site])->queryOne();
//        if ($sitePermission["permission"] == null || $sitePermission["permission"] == 1 || $sitePermission["permission"] == 3) {
//            // 3 is no allow anyway
//            $approveResult = $sitePermission["permission"] == 3 ? 2 : 1;
//            Yii::$app->db->createCommand()->update("zdata_site_request", ["rstat" => 2, "approve_result" => $approveResult], "id = :id", [":id" => $request_id])->execute();
//            Yii::$app->db->createCommand()->update("zdata_site_request", ["rstat" => 2, "approve_result" => 4], "(approve_result = 0 OR approve_result IS NULL) AND user_create = :user_id ", [":user_id" => Yii::$app->user->id])->execute();
//            $user = $this->findModel(Yii::$app->user->id);
//            $profile = $user->profile;
//            $profile->sitecode = $target_site;
//            $profile->site_permission = $sitePermission["permission"] == 1 ? 1 : 0;
//
//            if ($profile->update()) {
//                $message = $profile->site_permission == 1 ? "ได้รับการอนุมัติแล้ว" : "ได้รับการอนุมัติแล้ว แต่ไม่มีสิทธิใน Project";
//                $siteTitle = $sitePermission["site_detail"];
//                Notify::setNotify()->send_email(true)->send_system(true)->notify("Site Request")->detail("$message $siteTitle($target_site)")->assign([Yii::$app->user->id])->sendRedirect("");
//                return  ["success" => true];
//            } else {
//                return ["success" => false];
//            }
//        }
//        return ["success" => false];
    }

    public function actionUpdateProfile($id)
    {

        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $profile = $user->profile;
        if ($profile == null) {
            $profile = \Yii::createObject(Profile::className());
            $profile->link('user', $user);
        }
        $event = $this->getProfileEvent($profile);

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);
        $target = Yii::$app->request->get("target", null);
        $request_id = Yii::$app->request->get("request_id", null);
        $permission = Yii::$app->request->post("permission", null);

        if (Yii::$app->request->isAjax) {
            if (isset($target) && isset($permission)) {
                // 1 ALLOW 2 CONSIDER 3 DENIED 4 SYSTEM REJECTED
                Yii::$app->db->createCommand()->update("zdata_site_request", ["rstat" => 2, "approve_result" => $permission], "id = :id", [":id" => $request_id])->execute();
                Yii::$app->db->createCommand()->update("zdata_site_request", ["rstat" => 2, "approve_result" => 4], "(approve_result = 0 OR approve_result IS NULL) AND user_create = :user_id ", [":user_id" => $id])->execute();

                /**
                 * Check site permssion before assign.
                 */
                if ($permission < 3) {
                    $profile->sitecode = $target;
                    $profile->site_permission = $permission == 1 ? 1 : 0;
                    if ($profile->update()) {
                        //\Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Profile details have been updated'));
                        $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
                        $message = $profile->site_permission == 1 ? "ได้รับการอนุมัติแล้ว" : "ได้รับการอนุมัติแล้ว แต่ไม่มีสิทธิใน Project";
                        $siteTitle = Yii::$app->db->createCommand("SELECT site_detail FROM zdata_sitecode WHERE site_name = :target", [":target" => $target])->queryOne();
                        $siteTitle = $siteTitle["site_detail"];
                        Notify::setNotify()->send_email(true)->send_system(true)->notify("Site Request")->detail("$message $siteTitle($target)")->assign([$id])->sendRedirect("");

                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        $result = [
                            'status' => 'success',
                            'action' => 'create',
                            'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Update Site completed.'),
                        ];
                        return $result;
                    } else {
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        $result = [
                            'status' => 'error',
                            'action' => 'create',
                            'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Update Site Error.'),
                        ];
                        return $result;
                    }
                } else {
                    Notify::setNotify()->send_email(true)->send_system(true)->notify("Site Request")->detail("ถูกปฏิเสธ ($target)")->assign([$id])->sendRedirect("");
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    $result = [
                        'status' => 'success',
                        'action' => 'create',
                        'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Denied completed.'),
                    ];
                    return $result;
                }
            }
            return $this->renderAjax('site_permission', ["request_id" => $request_id]);
        }
        return $this->render('site_permission', ["request_id" => $request_id]);
        \dektrium\user\models\User::AFTER_CONFIRM();
    }


}
