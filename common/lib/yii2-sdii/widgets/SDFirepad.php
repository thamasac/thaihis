<?php

namespace appxq\sdii\widgets;

/**
 * SDFirepad class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 25 พ.ย. 2558 13:08:20
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\helpers\Url;
use appxq\sdii\assets\firepad\FirepadAsset;

class SDFirepad extends InputWidget {

    public $config = [];
    public $ezf_id = 0;
    public $ezf_field_id = 0;
    
    public function init() {
        parent::init();

        if(!isset($this->options['id'])){
            $this->options['id'] = $this->id;
        }
    }

    public function run() {
        $id = $this->options['id'];
        
        if(isset($this->options['class'])){
            $this->options['class'] .= ' ';
        } else {
            $this->options['class'] = 'firepad-container';
        }
        $this->options['style'] = 'min-height: 250px;';
                
        $boxOptions = $this->options;
        unset($boxOptions['id']);
        $dataEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformWithField($this->ezf_field_id);
        
        echo Html::beginTag('div', $boxOptions);
        echo Html::a('<i class="glyphicon glyphicon-fullscreen"></i>', '#', ['class'=>'btn btn-sm btn-default fullscreen']);
        echo Html::tag('div', '', ['id'=>"$id-userlist", 'class'=>'userlist-box']);
        echo Html::beginTag('div', ['class'=>'firepad-info-box']);
        if($dataEzf){
            $hStr = '<div class="media">
            <div class="media-left">
                '.\backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($dataEzf, 64, ['class'=>'media-object']).'
            </div>
            <div class="media-body">
              <h4 class="media-heading">'.$dataEzf['ezf_name'].'</h4>
              <div style="color: #999">'.$dataEzf['ezf_detail'].'</div>
              <div><b>'.$dataEzf['ezf_field_label'].'</b></div>
            </div>
          </div>';
            echo $hStr;
        }
        echo Html::endTag('div');
        echo Html::tag('div', '', ['id'=>"$id-firepad", 'class'=>'firepad-box']);
        echo Html::endTag('div');
        
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute);
        } else {
            echo Html::hiddenInput($this->name, $this->value, ['id'=>$this->name]);
        }
        
        $this->registerClientScript();
    }

    public function registerClientScript() {
        $id = $this->options['id'];
        $inputID;
	if ($this->hasModel()) {
            $inputID = Html::getInputId($this->model, $this->attribute);
        } else {
	    $inputID = $this->name;
        }
        
        $view = $this->getView();        
        FirepadAsset::register($view);

        $view->registerCss("
            .firepad-info-box{
                position: absolute;
                left: 0; 
                top: 0; 
                bottom: 0; 
                right: 0; 
                height: 64px;
                display: none;
            }
            .vfull .firepad-info-box{
                display: block;
                left: 185px;
                top: 10px;
                right: 10px; 
            }
            
            .btn.fullscreen{
                position: absolute;
                z-index: 1010;
                right: 5px;
                bottom: 5px;
            }
            
            .firepad-container {
                position: relative;
                border: 1px solid #ccc;
            }
            
            .userlist-box .firepad-userlist {
              position: absolute; left: 0; top: 0; bottom: 0; height: auto;
              display: none;
            }
            
            .vfull .userlist-box .firepad-userlist {
                width: 175px;
                display: block;
            }
            

            .firepad-box {
              position: absolute; left: 0; top: 0; bottom: 0; right: 0; height: auto;
            }
            
            .vfull .firepad-box{
                left: 175px;
                top: 64px;
            }
            
            .powered-by-firepad{
                display: none;
            }
        ");
        $userId = '';
        $userName = '';
        
        if(Yii::$app->user->isGuest){
            $userId = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $userName = 'Guest-'.$userId;
        } else {
            $userProfile = Yii::$app->user->identity->profile;
            $userId = Yii::$app->user->id;
            $userName = "{$userProfile->firstname} {$userProfile->lastname}";
        }
        
        $refId = $this->ezf_field_id;
        if ($this->hasModel()) {
            if(isset($this->model['id'])){
                $refId = $refId.'-'.$this->model['id'];
            }
        }
        
        $view->registerJs("
            
            setTimeout(function(){ init('{$id}', '{$refId}', '{$inputID}'); }, 500);
                
            $('.firepad-container').bind('fscreenclose', function() {
                $(this).removeClass('vfull');
            });

            $('.fullscreen').click(function(){
              
                if ($.fullscreen.isFullScreen()) {
                        $.fullscreen.exit();
                        $(this).parent().removeClass('vfull');
                        return false;
                } else {
                        $(this).parent().fullscreen();
                        $(this).parent().addClass('vfull');
                        return false;
                }
                
            });
            
            function init(id, ref, inputID) {
              //// Initialize Firebase.
              //// TODO: replace with your Firebase project configuration.
              let config = ".\yii\helpers\Json::encode($this->config).";

              if (!firebase.apps.length) {
                firebase.initializeApp(config);
              }
            
              //// Get Firebase Database reference.
              let firepadRef = firebase.database().ref();
              firepadRef = firepadRef.child(ref);
              //let headless = new Firepad.Headless(firepadRef);
              
              //// Create CodeMirror (with lineWrapping on).
              let codeMirror = CodeMirror(document.getElementById(id+'-firepad'), { lineWrapping: true });
                  
              // Create a random ID to use as our user ID (we must give this to firepad and FirepadUserList).
              let userId = '{$userId}';
              //// Create Firepad (with rich text features and our desired userId).
              let firepad = Firepad.fromCodeMirror(firepadRef, codeMirror,
                  { richTextToolbar: true, richTextShortcuts: true, userId: userId});
              //// Create FirepadUserList (with our desired userId).
              let firepadUserList = FirepadUserList.fromDiv(firepadRef.child('users'),
                  document.getElementById(id+'-userlist'), userId, '{$userName}');
              
                //// Initialize contents.
              firepad.on('ready', function() {
                if (!firepad.isHistoryEmpty()) {
                  $('#'+inputID).val(firepad.getText());
                }
              });
              
              firepad.on('synced', function(isSynced) {
                $('#'+inputID).val(firepad.getText());
              });
              
            }
            
        ");
    }

}
