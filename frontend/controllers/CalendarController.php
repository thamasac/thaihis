<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Calendar controller
 */
class CalendarController extends Controller {

    public function actionIndex($date = null) {
        $dateNow = isset($date) ? $date : date('Y-m-d');
        $userProfile = Yii::$app->user->identity->profile;
        $pt_id = '1503471963004608800';
        $initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['app_chk_pt_id' => $pt_id]);
        $events = \backend\modules\patient\classes\CalendarFunc::getEventCalendarFront('app', $dateNow);

        return $this->render('calendar-frontend', ['events' => $events, 'dateNow' => $dateNow, 'initdata' => $initdata]);
    }

}
