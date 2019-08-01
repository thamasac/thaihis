<?php
namespace common\modules\user\models;

use dektrium\user\models\User as BaseUser;
use dektrium\user\helpers\Password;
use yii\log\Logger;
use Yii;
use dektrium\user\models\Token;
use yii\db\AfterSaveEvent;

class User extends BaseUser
{
    public $modelProfile;
  public function rules()
    {
      
        return [
            // username rules
            'usernameTrim'     => ['username', 'trim'],
            'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
            'usernameMatch'    => ['username', 'match', 'pattern' => static::$usernameRegexp],
            'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernameUnique'   => [
                'username',
                'unique',
                'message' => \Yii::t('user', 'This username has already been taken')
            ],

            // email rules
            'emailTrim'     => ['email', 'trim'],
            'emailRequired' => ['email', 'required', 'on' => ['register', 'connect', 'create', 'update']],
            'emailPattern'  => ['email', 'email'],
            'emailLength'   => ['email', 'string', 'max' => 255],
            'emailUnique'   => [
                'email',
                'unique',
                'message' => \Yii::t('user', 'This email address has already been taken')
            ],
            // password rules
            'passwordRequired' => ['password', 'required', 'on' => ['register', 'create']],
//            [['password'],'required'],
            'passwordLength'   => ['password', 'string', 'min' => 6, 'max' => 72, 'on' => ['register', 'create']],
            //['password','required']
            //['status', 'default', 'value' => self::STATUS_ACTIVE],//
            //['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }
    public static function getNotAdminsite() {
	$sql = "SELECT profile.sitecode
		FROM profile INNER JOIN auth_assignment ON auth_assignment.user_id = profile.user_id
		WHERE auth_assignment.item_name = 'adminsite' OR auth_assignment.item_name = 'administrator'
		GROUP BY profile.sitecode";
	
	return Yii::$app->db->createCommand($sql)->queryColumn();
    }
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        if ($this->module->enableConfirmation == false) {
            $this->confirmed_at = time();
        }

        if ($this->module->enableGeneratingPassword) {
            $this->password = Password::generate(8);
        }

        $this->trigger(self::BEFORE_REGISTER);
        //$this->trigger(self::USER_REGISTER_INIT);

        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        
        
        if ($this->save()) {
            //$this->trigger(self::USER_REGISTER_DONE);
            $this->trigger(self::AFTER_REGISTER);
            $isInvite = \Yii::$app->session->getFlash("invitation",null,true);

            if ($this->module->enableConfirmation && $isInvite == null) {
                $token = \Yii::createObject([
                    'class' => Token::className(),
                    'type'  => Token::TYPE_CONFIRMATION,
                ]);
                $token->link('user', $this);
                //$this->mailer->sendConfirmationMessage($this, $token);
            } else {
                \Yii::$app->user->login($this);
            }
            if ($this->module->enableGeneratingPassword) {
                    $this->mailer->sendWelcomeMessage($this);
            }
            
            \Yii::$app->session->setFlash('info', \Yii::t('chanpan', 'A message has been sent to your email address. It contains a confirmation link that you must click to complete registration.'));
            \Yii::getLogger()->log('User has been registered', Logger::LEVEL_INFO);

//            if($isInvite){                
//               $this->updateAttributes(['confirmed_at' => time()]);
//            }else {
//                 
//                if ($this->module->enableConfirmation) {                    
//                    /** @var Token $token */
//                    $token = Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
//                    $token->link('user', $this);
//                    \common\modules\user\classes\CNMail::sendMail($this, $token);
//                }
//            }
            if ($this->module->enableConfirmation) {
                //
                /** @var Token $token */
                $token = Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
                $token->link('user', $this);
                \common\modules\user\classes\CNMail::sendMail($this, $token);
            }

            // the following three lines were added:
	    $auth = Yii::$app->authManager;
	    $authorRole = $auth->getRole('author');
	    $auth->assign($authorRole, $this->getId());
            

            return true;
        }

        \Yii::getLogger()->log('An error occurred while registering user account', Logger::LEVEL_ERROR);

        return false;
    }
    
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $this->confirmed_at = time();

        if ($this->password == null) {
            $this->password = Password::generate(8);
        }
        
        if ($this->username === null) {
            $this->generateUsername();
        }

        //$this->trigger(self::USER_CREATE_INIT);
        $this->trigger(self::BEFORE_CREATE);
        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        
        if ($this->save()) {
            //$this->trigger(self::USER_CREATE_DONE);
            $this->trigger(self::AFTER_CREATE);
            
            //$this->mailer->sendWelcomeMessage($this);
            \Yii::getLogger()->log('User has been created', Logger::LEVEL_INFO);
	    
	    // the following three lines were added:
	    $auth = Yii::$app->authManager;
	    $authorRole = $auth->getRole('user');
	    $auth->assign($authorRole, $this->getId());
	    
            return true;
        }

        \Yii::getLogger()->log('An error occurred while creating user account', Logger::LEVEL_ERROR);

        return false;
    }
    
    /** @inheritdoc */
    public function afterSave($insert, $changedAttributes)
    {
//        parent::afterSave($insert, $changedAttributes);
//        if ($insert) {
//            if ($this->modelProfile == null) {
//                $this->modelProfile = Yii::createObject(Profile::className());
//            }
//            $this->modelProfile->link('user', $this);
//        }
            if ($insert) {
                if ($this->modelProfile == null) {
                    $this->modelProfile = \Yii::createObject(Profile::className());
                    
                    $this->modelProfile->public_email = $this->email;
                    $this->modelProfile->gravatar_email = $this->email;
                   
                }
                $this->modelProfile->user_id = $this->id;
                
                $this->modelProfile->save(false);
            }

            $this->trigger($insert ? self::EVENT_AFTER_INSERT : self::EVENT_AFTER_UPDATE, new AfterSaveEvent([
                'changedAttributes' => $changedAttributes
            ]));
    }
    public function getAuth(){
        return $this->hasMany(Auth::className(), ['user_id'=>'id']);
    }

    /** @inheritdoc */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }
    
    
}