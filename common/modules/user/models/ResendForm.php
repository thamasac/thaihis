<?php
namespace common\modules\user\models;

use dektrium\user\models\ResendForm as BaseResendForm;
use dektrium\user\models\Token;
  
class ResendForm extends BaseResendForm{
    public function resend()
    {
        
        if (!$this->validate()) {
            return false;
        }

        $user = $this->finder->findUserByEmail($this->email);
        
        if ($user instanceof User && !$user->isConfirmed) {
//            \appxq\sdii\utils\VarDumper::dump('ok');
            /** @var Token $token */
            $token = \Yii::createObject([
                'class' => Token::className(),
                'user_id' => $user->id,
                'type' => Token::TYPE_CONFIRMATION,
            ]);
            $token->save(false);
            \common\modules\user\classes\CNMail::sendMail($user, $token);   
            
        }
         
        \Yii::$app->session->setFlash(
            'info',
            \Yii::t(
                'chanpan',
                'A message has been sent to your email address. It contains a confirmation link that you must click to complete registration.'
            )
        );

        return true;
    }
}
