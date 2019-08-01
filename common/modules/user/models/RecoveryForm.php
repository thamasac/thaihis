<?php

namespace common\modules\user\models;

use dektrium\user\Finder;
use dektrium\user\Mailer;
use yii\base\Model;
use common\modules\user\classes\CNMail;
use dektrium\user\models\RecoveryForm as BaseRecoveryForm;
use dektrium\user\models\Token;
 
class RecoveryForm extends BaseRecoveryForm
{
    public function sendRecoveryMessage()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->finder->findUserByEmail($this->email);
        if(!$user){
            \Yii::$app->session->setFlash(
                'danger',
                \Yii::t('user', "Not found email {$this->email}")
            );

            return true;
        }
        if ($user instanceof User) {
            /** @var Token $token */
            $token = \Yii::createObject([
                'class' => Token::className(),
                'user_id' => $user->id,
                'type' => Token::TYPE_RECOVERY,
            ]);

            if (!$token->save(false)) {
                return false;
            }
            if(!CNMail::sendRecoveryMessage($user, $token)){
                return false;
            }
//            if (!$this->mailer->sendRecoveryMessage($user, $token)) {
//                return false;
//            }
        }

        \Yii::$app->session->setFlash(
            'info',
            \Yii::t('user', 'An email has been sent with instructions for resetting your password')
        );

        return true;
    }
}
