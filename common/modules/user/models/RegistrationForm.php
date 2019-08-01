<?php
namespace common\modules\user\models;

use Yii;
use dektrium\user\models\RegistrationForm as BaseRegistrationForm;
use backend\modules\core\components\CoreFunc;
use backend\modules\core\components\CoreQuery;
use yii\helpers\ArrayHelper;
use common\modules\user\models\Profile;

class RegistrationForm extends BaseRegistrationForm
{
    /**
    * @var string
    */
    public $password;
    public $captcha;
    public $name;
    public $gender;
    public $firstname;
    public $lastname;
    public $telephone;
    public $sitecode;
    public $department;
    public $dob;
    public $confirm_password;
    public $email;
   // public $cid;
    
     


    /**
     * @inheritdoc
     */
    public function rules()
    {
	//$rules = parent::rules();
	//$rules[] = ['cid','required']; 
        //$rules[] = ['cid','validateIdCard'];
        $rules[] = ['username', 'required'];
        $rules[] = ['username', 'customValidateUsername'];
        $rules[] = ['email', 'required'];
        $rules[] = ['email', 'trim'];
	$rules[] = ['name', 'required'];
        $rules[] = ['name', 'string', 'max' => 255];
         
        $rules[] = ['firstname', 'required'];
        $rules[] = ['lastname', 'required'];
        //$rules[] = ['gender', 'required'];
        //$rules[] = ['telephone', 'required'];
        
        //$rules[] = ['department', 'required'];
        //$rules[] = ['dob', 'required'];
        $rules[] = ['password', 'required', 'skipOnEmpty' => $this->module->enableGeneratingPassword];
        $rules[] = ['password', 'string', 'min' => 6, 'max' => 72];
        $rules[] = ['confirm_password','required'];
        $rules[] = ['confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=> Yii::t('chanpan','Passwords don\'t match')];
        //$rules[] = ['sitecode', 'required'];
        $rules[] = [['email'], 'email','message'=>Yii::t('chanpan','Not a valid email address.')];
//	$rules[] = ['captcha', 'required'];
//      $rules[] = ['captcha', 'captcha'];
	
	return $rules;
    }
    
    public function customValidateUsername(){
        if(!empty($this->username)){   
            if(!preg_match("/[^a-zA-Z0-9_]/", $this->username) == 0){
                $this->addError('username',Yii::t('chanpan','Username is invalid.'));
            }
        }
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['name'] = Yii::t('user', 'Nickname');
//        $labels['citizenid_file'] = Yii::t('app', 'Citizenid File');
        $labels['secret_file'] = Yii::t('chanpan', 'Secret File');
        
        $labels['firstname'] = Yii::t('chanpan', 'First name');
        $labels['lastname'] = Yii::t('chanpan', 'Last name');
        $labels['gender'] = Yii::t('chanpan', 'Gender');
	$labels['telephone'] = Yii::t('chanpan', 'Telephone number');
        $labels['sitecode'] = Yii::t('chanpan', 'Sitecode');
        $labels['department'] = Yii::t('chanpan', 'Department');
        $labels['dob'] = Yii::t('chanpan', 'Birth date');
        $labels['confirm_password']=Yii::t('chanpan', 'Confirm password');
       // $labels['cid'] = Yii::t('chanpan', 'ID card number');
         
        
        return $labels;
    }
    
    /**
     * Registers a new user account.
     * @return bool
     */
    public function register()
    {
        
//        if (!$this->validate()) {
//            return false;
//        }
        
        $user = Yii::createObject(User::className());
        $user->setScenario('register');       

        $user->setAttributes([
            'email'    => $this->email,
            'username' => $this->username,
            'password' => $this->password
            ]);
            
	/** @var Profile $profile */
        $profile = \Yii::createObject(Profile::className());
        $profile->setAttributes([
            //'cid' => $this->cid,
	    'name' => $this->firstname.' '.$this->lastname,
	    'public_email' => $this->email,
	    'gravatar_email' => $this->email,
            'dob'=>' ',//$this->dob,
            'firstname'=>$this->firstname,
            'lastname'=>$this->lastname,             
            'department'=>'00',//$this->department,
            'position'=>'0',
            'sitecode'=>'00',//$this->sitecode,
            'tel'=> ' ',//$this->telephone
             
            
        ]);
//        $auth = \Yii::$app->authManager;
//        $author = $auth->createRole('author');
//        $auth->add($author); 
//        \appxq\sdii\utils\VarDumper::dump($profile);
	$user->modelProfile = $profile;
	
        return $user->register();
    }
}