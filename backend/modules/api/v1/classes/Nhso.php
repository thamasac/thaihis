<?php
/**
 * Created by PhpStorm.
 * User: tyroroto
 * Date: 18/12/2018 AD
 * Time: 07:11
 */

namespace backend\modules\api\v1\classes;


use nusoap_client;

class Nhso
{

    static function getNhso($cid)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if (strlen($cid) == 13) {
            $sql = "SELECT token_id,token_cid FROM zdata_nhso_token WHERE rstat not in(0,3) ORDER BY update_date DESC";
            $tokenArr = \Yii::$app->db->createCommand($sql)->queryOne();

            $data = null;
            require_once( "nhso-lib/nusoap.php");
            $client = new nusoap_client("http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?WSDL", true);
            $client->soap_defencoding = 'UTF-8';
            $client->decode_utf8 = false;
            $params = array(
                'user_person_id' => $tokenArr['token_cid'],
                'smctoken' => $tokenArr['token_id'],
                'person_id' => $cid);
            $data = $client->call("searchCurrentByPID", $params);
            $data = $data['return'];
            if(!$data){
                return json_encode(['status-system' => 'error', 'message'=>'RESPONSE FAILED']);
            }else if ( empty($data['fname'])) {
                return json_encode(['status-system' => 'error', 'message'=>'NOT FOUND IN NHSO']);
            }else if ($data['ws_status'] == 'NHSO-00003') {
                return json_encode(['status-system' => 'error', 'message'=>'TOKEN EXPIRE']);
            }
            return json_encode($data);
        }
    }
}