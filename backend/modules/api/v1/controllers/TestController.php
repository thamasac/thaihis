<?php
/**
 * Created by PhpStorm.
 * User: tyroroto
 * Date: 12/12/2018 AD
 * Time: 12:27
 */

namespace backend\modules\api\v1\controllers;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\thaihis\classes\ThaiHisQuery;
use backend\modules\thaihis\controllers\PatientVisitController;
use backend\modules\api\v1\classes\Nhso;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;

class TestController extends Controller
{

    public $testCid = [4423351325435,7417464130239,3733655066552];
    public $testTarget = [1544491435007847300,1544491442057768800,1544491477025582000];
    public function beforeAction($event)
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($event);
    }

    public function actionMinimum() {
        return Nhso::getNhso('1100501040231');
        // \Yii::getAlias('common')
//        return Yii::$app->basePath."common/lib/nhso-lib/nusoap.php";
//        $ptid = '';
//        $dataOnline = PatientFunc::getRightOnlineByNhso($cid); //ตรวจสอบ web service สปสช
//
//        $ezf_id = '1503589101005614900';
//        $res = [];
//        $res[0] = PatientVisitController::actionSaveVisit('1543467322034227700', $ezf_id, '3');
//        $res[1] = PatientVisitController::actionSaveVisit('1544490437096891900', $ezf_id, '3');
//        $res[2] = PatientVisitController::actionSaveVisit('1544488707044427400', $ezf_id, '3');
//        $res[3] = PatientVisitController::actionSaveVisit('1543799150098872500', $ezf_id, '3');
//        $res[4] = PatientVisitController::actionSaveVisit('1544455717091319000', $ezf_id, '3');
//        return $res;

    }//minimum
    public function actionVisitDate() {
        
        $ptid = Yii::$app->request->get('ptid','1543799150098872500');
        $data1 = PatientQuery::getVisitDate($ptid);
        $data2 = PatientQuery::getVisitDate('');
        $data3 = PatientQuery::getVisitDate(null);
        //$data4 = PatientQuery::getVisitDate();
        
        $out = [
          'function'=>'actionVisitDate',
          'output'=>[
                'case 1' =>[
                    'ptid'=> isset($data1['ptid']) ? $data1 ['ptid'] : '',
                    'status'=>isset($data1['ptid']) && $data1['ptid'] == $ptid ? true : false,
                    'msg'=>"ptid = {$ptid}"
                ],
                'case 2' =>[
                    'ptid'=> isset($data2['ptid']) ? $data2['ptid'] : '',
                    'status'=>isset($data2['ptid']) && $data2['ptid'] == $ptid ? true : false,
                    'msg'=>'ptid = \'\' '
                 ],
                'case 3' =>[
                    'ptid'=> isset($data3['ptid']) ? $data3['ptid'] : '',
                    'status'=>isset($data3['ptid']) && $data3['ptid'] == $ptid ? true : false,
                    'msg'=>'ptid = null'
                ],
          ]
        ];    
        return $out;
    }//visit-date
    public function actionGetVisitByDate() {
         
        $ptid = Yii::$app->request->get('ptid','1543467322034227700');
        
        $data1= PatientQuery::getVisitByDate($ptid, ''); 
        $data2= PatientQuery::getVisitByDate($ptid, '2018-12-11'); 
        $data3= PatientQuery::getVisitByDate($ptid, '2018-12-11 07:42:54'); 
        $data4= PatientQuery::getVisitByDate('', '2018-12-11');
        $data5= PatientQuery::getVisitByDate(null, '2018-12-11 07:42:54');
        $data6= PatientQuery::getVisitByDate(null, '2018-12-11');
        $data7= PatientQuery::getVisitByDate($ptid, null); 

        $out = [
          'function'=>'actionGetVisitByDate',
          'output'=>[
                'case 1' =>['data1'=>$data1,  'msg'=>'ptid = '.$ptid.' visit_date = \'\' '],
                'case 2' =>[
                    'ptid'=> isset($data2['ptid']) ? $data2['ptid'] : '',
                    'status'=>isset($data2['ptid']) && $data2['ptid'] == $ptid ? true : false,
                    'msg'=>'ptid = '.$ptid.' visit_date = 2018-12-11'
                ],
                'case 3' =>[
                    'ptid'=> isset($data3['ptid']) ? $data3['ptid'] : '',
                    'status'=>isset($data3['ptid']) && $data3['ptid'] == $ptid ? true : false,
                    'msg'=>'ptid = '.$ptid.' visit_date = 2018-12-11 07:42:54'
                ],
                'case 4' =>[
                    'ptid'=> isset($data4['ptid']) ? $data4['ptid'] : '',
                    'status'=>isset($data4['ptid']) && $data4['ptid'] == $ptid ? true : false,
                    'msg'=>'ptid = \'\' visit_date = 2018-12-11',
                    
                ],
                'case 5' =>[
                    'ptid'=> isset($data5['ptid']) ? $data5['ptid'] : '',
                    'status'=>isset($data5['ptid']) && $data5['ptid'] == $ptid ? true : false,
                    'msg'=>'ptid = null visit_date = 2018-12-11 07:42:54'
                ],
                'case 6' =>[
                    'ptid'=> isset($data6['ptid']) ? $data6['ptid'] : '',
                    'status'=>isset($data6['ptid']) && $data6['ptid'] == $ptid ? true : false,
                    'msg'=>'ptid = null visit_date = 2018-12-11'
                ],
                'case 7' =>[
                    'ptid'=> isset($data7['ptid']) ? $data7['ptid'] : '',
                    'status'=>isset($data7['ptid']) && $data7['ptid'] == $ptid ? true : false,
                    'msg'=>'ptid = '.$ptid.' null'
                ],  
          ]
          
        ];    
        return $out;
    }//get-visit-by-date
    public function actionGetPtWarning() {
         
        $ptid = Yii::$app->request->get('ptid','1539139725003540200');
        
        $data1 = PatientQuery::getPtWarning($ptid);
        $data2 = PatientQuery::getPtWarning('');
        $data3 = PatientQuery::getPtWarning(null);

        $out = [
            'function' => 'actionGetPtWarning',
            'output' => [
                'case 1' => [
                    'ptid'=> isset($data1['ptid']) ? $data1['ptid'] : '',
                    'status'=>isset($data1['ptid']) && $data1['ptid'] == $ptid ? true : false,
                    'msg' => 'ptid = '.$ptid],
                'case 2' => [
                    'ptid'=> isset($data2['ptid']) ? $data2['ptid'] : '',
                    'status'=>isset($data2['ptid']) && $data2['ptid'] == $ptid ? true : false,
                    'msg' => 'ptid = \'\' '
                ],
                'case 3' => [
                    'ptid'=> isset($data3['ptid']) ? $data3['ptid'] : '',
                    'status'=>isset($data3['ptid']) && $data3['ptid'] == $ptid ? true : false,
                    'msg' => 'ptid = null'
                ],
            ]
        ];
        return $out;
    }//get-pt-warning
    public function actionGetPatientprofile() {
        $visit_id = Yii::$app->request->get('visit_id','1544495292016931900');
        $visit_id2 = Yii::$app->request->get('visit_id','12312312121');
        
        $data1 = \backend\modules\reports\classes\CustomReport::getPatientprofile($visit_id);
        $data2 = \backend\modules\reports\classes\CustomReport::getPatientprofile('');
        $data3 = \backend\modules\reports\classes\CustomReport::getPatientprofile(null);
        $data4 = \backend\modules\reports\classes\CustomReport::getPatientprofile($visit_id2);
        
        $out = [
            'function' => 'getPatientprofile',
            'output' => [
                'case 1' => [
                    'data'=> $data1,
                    'status'=>isset($data2) ? true : false,
                    'msg' => 'visit_id = '.$visit_id],
                'case 2' => [
                    'data'=> $data2,
                    'status'=> isset($data2) && $data2 ? true : false,
                    'msg' => 'visit_id = \'\' '
                ],
                'case 3' => [
                    'data'=> $data3,
                    'status'=> isset($data3) && $data3 != false,
                    'msg' => 'visit_id = null'
                ],
                'case 4' => [
                    'data'=> $data4,
                    'status'=>isset($data4) && $data4 != false ? true : false,
                    'msg' => 'visit_id = '.$visit_id
                ],
            ]
        ];
        return $out;
    }//get-patientprofile
    public function actionGetEzfData() {
        $ezf_id = Yii::$app->request->get('ezf_id','1503378440057007100');
        $data_id = Yii::$app->request->get('data_id','1544088139077079300');
        $data1 = \backend\modules\reports\classes\CustomReport::getEzfData($ezf_id, $data_id);
        $data2 = \backend\modules\reports\classes\CustomReport::getEzfData($ezf_id, '');
        $data3 = \backend\modules\reports\classes\CustomReport::getEzfData('', $data_id);
        $data4 = \backend\modules\reports\classes\CustomReport::getEzfData($ezf_id, null);
        
         $out = [
            'function' => 'actionGetEzfData',
            'output' => [
                'case 1' => [
                    'data'=> isset($data1['output']['pt_contact_name']) ? $data1['output']['pt_contact_name'] : '',
                    'status'=>isset($data1['output']) ? true : false,
                    'msg' => 'ezf_id = '.$ezf_id."data_id={$data_id}"],
                'case 2' => [
                    'data'=> isset($data2['output']['pt_contact_name']) ? $data1['output']['pt_contact_name']: '',
                    'status'=> isset($data2['output']) && $data2['output'] ? true : false,
                    'msg' => 'ezf_id = '.$ezf_id."data_id== \"\" "],
                ],
                'case 3' => [
                    'data'=> isset($data3['output']['pt_contact_name']) ? $data1['output']['pt_contact_name']: '',
                    'status'=> isset($data3['output']) && $data3['output'] ? true : false,
                    'msg' => "ezf_id = '' data_id = {$data_id}"
                ],
                'case 4' => [
                    'data'=> isset($data4['output']['pt_contact_name']) ? $data1['output']['pt_contact_name']: '',
                    'status'=> isset($data4['output']) && $data4['output'] ? true : false,
                    'msg' => 'ezf_id = '.$ezf_id." data_id = null"
                ],
        ];
        return $out;
        
    }//get-ezf-data
    public function actionSaveVisitTran(){
        $pt_id = Yii::$app->request->get('pt_id', '');
        $dept = Yii::$app->request->get('dept', '');
        $date = Yii::$app->request->get('date', '');
        $tran_status = Yii::$app->request->get('tran_status', '');
        
        $data = PatientVisitController::saveVisitTran($pt_id, $visit_id, $data, $ezf);
        return $data;
        
    }//save-visit-tran
    public function actionCheckRightByPass(){
        
        $visit_id = Yii::$app->request->get('visit_id', '');
        $pt_id = Yii::$app->request->get('pt_id', '');
        $patient_right = Yii::$app->request->get('patient_right', '');
        $dept = Yii::$app->request->get('dept', '');
        $cid = Yii::$app->request->get('cid', '');
        $visit_type = Yii::$app->request->get('visit_type', '');
        
        $data = PatientVisitController::checkRightByPass($visit_id, $pt_id, $cid, $visit_type, null);
        //$data = Yii::$app->runAction('/thaihis/patient-visit/get-form-ref');
        return $data;
        
    }//check-right-by-pass
    function actionCheckVersion(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $file = Yii::$app->request->post('files',null);
        $fileArr = json_decode($file,true);
        $res = (new Query)->select('filename,checksum,path,extractpath')->from('zdata_application_checksum')->where(['enabled'=>1])->all();
        return ['success'=>true, 'files'=>$res];
    }


    function actionTestAppointment(){
        $response = [];
        /**
         *  1544711763097160600 , app_status 1 , app_date 2018-12-02
            1544711608019574500 , app_status 1 , app_date 2018-12-20

         * dept 1536740849035024600
         */
        $appTest1 = PatientQuery::getAppointPt($this->testTarget[0],'1536740849035024600');
        $response['appoint must and id is 1544711763097160600'] = $appTest1['id'] == '1544711763097160600';
        $response['appoint must  status 1'] = $appTest1['app_status'] == '1';
        $response['appoint must in dept id 1536740849035024600 '] = $appTest1['app_dept'] == '1536740849035024600';
        try{
            PatientQuery::getAppointPt(null,'1536740849035024600');
            $response['$appTestNull must Error'] = false;
        }catch (\Exception $e){
            $response['$appTestNull must Error'] = true;
        }
        $appTestDate = PatientQuery::getAppointPt($this->testTarget[0],'1536740849035024600','2018-12-20');
        $response['$appTestVisitDate must id is 1544711608019574500'] = $appTestDate['id'] == '1544711763097160600';
        $response['$appTestVisitDate must status 1 '] = $appTestDate['app_status'] == '1';
        $response['$appTestVisitDate must correct date 2018-12-20'] = $appTestDate['app_date'] == '2018-12-20';

        $appTestAppointArray = PatientQuery::getAppointmentByPtid('1544711763097160600');
        $response['$appTestVisitDate must has 3 record'] = count($appTestAppointArray) == 3;
        return $response;
    }//test-appointment

    function actionTestRight(){

        $response = [];
        // UCS right_code
        try {
            $resTestRight = PatientQuery::getRightByVisitId('1544711109062591200');
            $response['getRightByVisitId id is 1544711110027577900']=$resTestRight['id']=='1544711110027577900';
        } catch (Exception $e) {
            $response['getRightByVisitId should not ERROR'] = false;
        }

        $resTestgetRightByPtid = ThaiHisQuery::getPtRightLast($this->testTarget[0]);
        $response['getPtRightLast should has code'] = $resTestgetRightByPtid['right_code'] != '';
        $response['getPtRightLast should has not cash'] = $resTestgetRightByPtid['right_code'] != 'CASH';
        // need to add case first

    }//test-right

    function actionTestVisit(){
//        PatientQuery::getVisitTran();
//        PatientQuery::getVisitDate();
//        PatientQuery::getVisitByDate();
    }
}