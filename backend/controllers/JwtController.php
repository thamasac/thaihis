<?php 
namespace backend\controllers;
use backend\modules\api\v1\classes\JwtWebToken; 
class JwtController extends \yii\web\Controller{ 
    public function actionTest(){
        $data = ['id'=>'1', 'name'=>'Nuttaphon Chanpan'];
        //$token = \backend\modules\api\v1\classes\JwtWebToken::enCode($data, '123456', 123);
        
        $token = JwtWebToken::deCode('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE1MzgxNTc0NjMsImlhdCI6MTUzODE1MDI2Mywic3ViIjoiMTIzNDV4eHgifQ.eLnh_BCz77aT6xKdx4htnsr_Iy19ILmhPBDbhCcwavo', '123456');
        \appxq\sdii\utils\VarDumper::dump($token);
    }
    
    
}
