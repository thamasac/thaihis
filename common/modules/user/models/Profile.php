<?php
namespace common\modules\user\models;

use dektrium\user\models\Profile as BaseProfile;
use Yii;
use backend\modules\core\classes\CoreQuery;
use backend\modules\core\classes\CoreFunc;
use yii\helpers\ArrayHelper;
use trntv\filekit\behaviors\UploadBehavior;

class Profile extends BaseProfile
{
    public $dynamicFields;
    public $blocked_at;
    public $flags;
    public $flags_id;
    public $picture;
    public $auth_str;
//    public $site_switch;
//    public $original_site;
//    public $secret;
//    public $citizenid;
    
    public function behaviors()
    {
        return [
            'picture' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'avatar_path',
                'baseUrlAttribute' => 'avatar_base_url'
            ],
 
        ];
    }
    
    public function init() {
	parent::init();
	$this->dynamicFields = isset(Yii::$app->params['profilefields'])?Yii::$app->params['profilefields']:[];
    }
    
    public function rules()
    {
	$rules = [
            'bioString' => ['bio', 'string'],
            'publicEmailPattern' => ['public_email', 'email'],
            'gravatarEmailPattern' => ['gravatar_email', 'email'],
            'websiteUrl' => ['website', 'url'],
            'nameLength' => ['name', 'string', 'max' => 255],
            'publicEmailLength' => ['public_email', 'string', 'max' => 255],
            'gravatarEmailLength' => ['gravatar_email', 'string', 'max' => 255],
            'locationLength' => ['location', 'string', 'max' => 255],
            'websiteLength' => ['website', 'string', 'max' => 255],
            
            
        ];
        
	$addon = [
            //[['tel'], 'required'],
            //[['cid'],'unique'],
            //[['cid'], 'validateIdCard'],
            [['firstname'],'required','message'=> \Yii::t('chanpan','Firstname cannot be blank.')],
            [['lastname'],'required','message'=> \Yii::t('chanpan','Lastname cannot be blank.')],
            [['avatar_path', 'avatar_base_url', 'line_id'], 'string'],//, 'secret_file', 'citizenid_file'
            [['firstname','lastname','picture','secret_file','citizenid_file','sitecode','auth_str','department','allow_assign','tel'], 'safe']
        ];
        
        return ArrayHelper::merge($rules, $addon, CoreFunc::getTableRules('profile'));
    }
     
    /** @inheritdoc */
    public function attributeLabels()
    {
	$labels = [
            'auth_str'      => Yii::t('rbac-admin', 'Role'),
            //'cid'            => Yii::t('chanpan', 'ID card number'),
            'tel'            => Yii::t('chanpan', 'Telephone number'),
            'sitecode'       => Yii::t('rbac-admin','Sitecode'),
            'name'           => Yii::t('user', 'Nickname'),
            'firstname'      => Yii::t('chanpan', 'First name'),
            'lastname'       => Yii::t('chanpan', 'Last name'),
            'public_email'   => Yii::t('user', 'Email (public)'),
            'gravatar_email' => Yii::t('user', 'Gravatar email'),
            'location'       => Yii::t('user', 'Location'),
            'website'        => Yii::t('user', 'Website'),
            'bio'            => Yii::t('chanpan', 'Birth day'),
            'picture' => Yii::t('app', 'My Picture'),
//            'secret' => Yii::t('app', 'My Secret'),
//            'citizenid' => Yii::t('app', 'My Citizenid'),
            'citizenid_file' => Yii::t('app', 'Citizenid File'),
            'secret_file'    => Yii::t('app', 'Secret File'),
            
            'department'=>Yii::t('app','Department'),
        ];
	
	$dynamicFields = $this->dynamicFields;
	foreach ($dynamicFields as $key => $value) {
            $labels["{$value['table_varname']}"] = isset($value['input_label']) ? Yii::t('user', $value['input_label']) : Yii::t('user', $value['table_varname']);
	}
	
        return $labels;
    }
    
    
}