<?php

namespace backend\modules\tmf\controllers;

use yii\web\Controller;

/**
 * Default controller for the `tmf` module
 */
class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }
    
    public function actionTest() {
        return $this->render('_test');
    }
    
    public function actionTestQueue(){
//         \Yii::$app->mailer->compose('@backend/mail/layouts/notify', [
//                    'notify' => 'Test',
//                    'detail' => 'Setail',
//                    'url' => '/notify',
//                ])
//                ->setFrom(['ncrc.damasac@gmail.com' => 'nCRC Thailand'])
//                ->setTo('aomruk12123@gmail.com')
//                ->setSubject('test')
//                //->attach(Yii::getAlias('@webroot').'/attach/'.'brochure.pdf')
//                //->attach(Yii::getAlias('@webroot').'/attach/'.'Poster.pdf')        
//                ->send();
//
//        \backend\modules\line\classes\LineFn::setLine()
//                ->message('Detail')
//                ->altMessage('test')
//                ->typeTemplateConfirm('/notify')
//                ->token('0PKMkmn+JngtXzvXsyHLOMA2cDBxZK2qE4aQVT9JSAR8+qkoMzyQSHgtDwezogrYQAlDBgWCp+2MkCw8v1NRzEvSijH0Lq2kQTnFCheGi/t4ASm4B2iC31knSxdVnxAUCuteev8/zW7d78TElFgVfQdB04t89/1O/w1cDnyilFU=')
//                ->pushMessage('U5d7e0c99aefac210694320e6b7d3c300');
        \Yii::$app->queue->push(new \dms\aomruk\classese\NotifyJob());
    }

    public function actionSave() {
//        $data = (new \yii\db\Query())->select('soc')->from('zdata_const_soc')->all();
        $data1 = " -
Asymptomatic
Asymptomatic; clinical or diagnostic observations only
Mild symptoms; intervention not indicated
Mild symptoms; intervention not indicated
Adult: Systolic BP 120 - 139 mm Hg or diastolic BP 80 - 89 mm Hg; Pediatric: Systolic/diastolic BP >90th percentile but< 95th percentile; Adolescent: BP ≥120/80 even if < 95th percentile
Asymptomatic, intervention not indicated
 -
Trace thickening or faint discoloration
Asymptomatic; clinical or diagnostic observations only; intervention not indicated
 -
 -
 -
Asymptomatic; incidental finding of SVC thrombosis
Medical intervention not indicated (e.g., superficial thrombosis)
Asymptomatic or mild symptoms; clinical or diagnostic observations only; intervention not indicated
Asymptomatic, intervention not indicated";

        $data2 = " -
Symptomatic; medical intervention indicated
Moderate symptoms; limiting instrumental ADL
Minimally invasive evacuation or aspiration indicated
Moderate symptoms; limiting instrumental ADL
Adult: Systolic BP 140 - 159 mm Hg or diastolic BP 90 - 99 mm Hg if previously WNL; change in baseline medical intervention indicated; recurrent or persistent (>=24 hrs); symptomatic increase by >20 mm Hg (diastolic) or to >140/90 mm Hg; monotherapy indicated initiated; Pediatric and adolescent: Recurrent or persistent (>=24 hrs) BP >ULN; monotherapy indicated; systolic and /or diastolic BP between the 95th percentile and 5 mmHg above the 99th percentile; Adolescent: Systolic between 130-139 or diastolic between 80-89 even if < 95th percentile
Non-urgent medical intervention indicated
Symptomatic; medical intervention indicated
Marked discoloration; leathery skin texture; papillary formation; limiting instrumental ADL
Symptomatic; medical intervention indicated
Brief (<24 hrs) episode of ischemia managed medically and without permanent deficit
Present
Present
Symptomatic; medical intervention indicated (e.g., anticoagulation, radiation or chemotherapy)
Medical intervention indicated
Moderate; minimal, local or noninvasive intervention indicated; limiting age-appropriate instrumental ADL
Moderate symptoms, medical intervention indicated
";

        $data3 = "Urgent intervention indicated
Severe symptoms; intervention indicated
Symptomatic, associated with hypotension and/or tachycardia; limiting self care ADL
Transfusion; invasive intervention indicated
Severe symptoms; limiting self care ADL
Adult: Systolic BP >=160 mm Hg or diastolic BP >=100 mm Hg; medical intervention indicated; more than one drug or more intensive therapy than previously used indicated; Pediatric and adolescent: Systolic and/or diastolic > 5 mmHg above the 99th percentile
Medical intervention indicated; hospitalization indicated
Severe symptoms; invasive intervention indicated
Severe symptoms; limiting self care ADL
Severe symptoms; invasive intervention indicated
Prolonged (>=24 hrs) or recurring symptoms and/or invasive intervention indicated
 -
 -
Severe symptoms; multi-modality intervention indicated (e.g., anticoagulation, chemotherapy, radiation, stenting)
Urgent medical intervention indicated (e.g., pulmonary embolism or intracardiac thrombus)
Severe or medically significant but not immediately life-threatening; hospitalization or prolongation of existing hospitalization indicated; limiting self care ADL
Severe symptoms, medical intervention indicated (e.g., steroids)
";

        $data4 = "Life-threatening consequences; hemodynamic or neurologic instability; organ damage; loss of extremity(ies)
Life-threatening consequences; urgent intervention indicated
 -
Life-threatening consequences; urgent intervention indicated
 -
Adult and Pediatric: Life-threatening consequences (e.g., malignant hypertension, transient or permanent neurologic deficit, hypertensive crisis); urgent intervention indicated
Life-threatening consequences and urgent intervention indicated
Life-threatening consequences; urgent intervention indicated
 -
 -
Life-threatening consequences; evidence of end organ damage; urgent operative intervention indicated
 -
 -
Life-threatening consequences; urgent multi-modality intervention indicated (e.g., lysis, thrombectomy, surgery)
Life-threatening consequences with hemodynamic or neurologic instability
Life-threatening consequences; urgent intervention indicated
Life-threatening consequences; evidence of peripheral or visceral ischemia; urgent intervention indicated
";

        $data5 = "Death
Death
 -
Death
 -
Death
Death
Death
 -
 -
Death
 -
 -
Death
Death
Death
Death
";
        $data1 = preg_split('/\r\n|\r|\n/', $data1);
        $data2 = preg_split('/\r\n|\r|\n/', $data2);
        $data3 = preg_split('/\r\n|\r|\n/', $data3);
        $data4 = preg_split('/\r\n|\r|\n/', $data4);
        $data5 = preg_split('/\r\n|\r|\n/', $data5);
        $i = 0;
        $sid = (new \yii\db\Query())->select('MAX(soc_id)+1')->from('const_grade')->scalar();
        $sid = $sid ? $sid : 1;
        $ctid = (new \yii\db\Query())->select('MAX(ctcae_id)+1')->from('const_grade')->scalar();
        $ctid = $ctid ? $ctid : 1;
        foreach ($data1 as $value) {
            if ($value != '') {
                (new \yii\db\Query())->createCommand()->insert('const_grade', [
                    'soc_id' => $sid,
                    'ctcae_id' => $ctid,
                    'grade' => 'Grade 1',
                    'grade_detail' => $value
                ])->execute();
                (new \yii\db\Query())->createCommand()->insert('const_grade', [
                    'soc_id' => $sid,
                    'ctcae_id' => $ctid,
                    'grade' => 'Grade 2',
                    'grade_detail' => $data2[$i]
                ])->execute();
                (new \yii\db\Query())->createCommand()->insert('const_grade', [
                    'soc_id' => $sid,
                    'ctcae_id' => $ctid,
                    'grade' => 'Grade 3',
                    'grade_detail' => $data3[$i]
                ])->execute();
                (new \yii\db\Query())->createCommand()->insert('const_grade', [
                    'soc_id' => $sid,
                    'ctcae_id' => $ctid,
                    'grade' => 'Grade 4',
                    'grade_detail' => $data4[$i]
                ])->execute();
                (new \yii\db\Query())->createCommand()->insert('const_grade', [
                    'soc_id' => $sid,
                    'ctcae_id' => $ctid,
                    'grade' => 'Grade 5',
                    'grade_detail' => $data5[$i]
                ])->execute();
                $i++;
                $ctid++;
            }
        }
        echo $i;
    }

}
