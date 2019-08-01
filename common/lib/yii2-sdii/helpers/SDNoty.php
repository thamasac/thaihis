<?php

namespace appxq\sdii\helpers;

use Yii;
use \yii\helpers\Json;

/**
 * SDNoty class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; 2013 AppXQ
 * @license http://www.appxq.com/license/
 * @package 
 * @version 1.0.0 Date: Sep 14, 2013 11:08:07 AM
 */

/**
 * notification extension for Yii.

  EX1 noty show
  var noty_id = noty({"text":responseText.message, "type":"success"});
  or fix
  <?php echo SDNoty::show('js:responseText.message', 'success');?>


  EX2 alert
  var noty_id = noty({"text":"Hi! I'm an example text. When I grow up I want to be a noty message.", "type":"alert", "timeout":false, "buttons":"???"});
  or fix
  <?php echo SDNoty::alert('js:responseText.message', 'success');?>


  EX3 confirm
  var noty_id = noty({"text":"Hi! I'm an example text. When I grow up I want to be a noty message.", "type":"alert", "timeout":false, "buttons":"???"});
  or fix
  <?php echo SDNoty::confirm('js:responseText.message','js:noty({force: true, text: "You clicked Ok button", type: "success"});' , 'success');?>
 * 
 */
class SDNoty {

	const TYPE_ALERT = 'alert';
	const TYPE_ERROR = 'error';
	const TYPE_SUCCESS = 'success';
	const TYPE_INFO = 'information';
	const LAYOUT_TOP = 'top';
	const LAYOUT_TOPCENTER = 'topCenter';
	const LAYOUT_BOOTTOM = 'bottom';
	const LAYOUT_CENTER = 'center';
	const LAYOUT_TOPLEFT = 'topLeft';
	const LAYOUT_TOPRIGHT = 'topRight';
	const LAYOUT_BOTTOMLEFT = 'bottomLeft';
	const LAYOUT_BOTTOMRIGHT = 'bottomRight';

	/**
	 * @var array the initial JavaScript options that should be passed to the JUI plugin.
	 */

	/**
	  'options' => array(
		'layout' => 'bottomRight',
		'theme' => 'noty_theme_twitter',
		'animateOpen' => '{height: 'toggle'}',
		'animateClose' => '{height: 'toggle'}',
		'easing' => 'swing',
		'text' => '',
		'type' => 'alert',
		'speed' => 500,
		'timeout' => 5000,
		'closeButton' => false,
		'closeOnSelfClick' => true,
		'closeOnSelfOver' => false,
		'force' => false,
		'onShow' => false,
		'onShown' => false,
		'onClose' => false,
		'onClosed' => false,
		'buttons' => false,
		'modal' => false,
		'template' => '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
		'cssPrefix' => 'noty_',
		'custom' => {
			container: null
		},
	  ),
	 */
	public static function setDefault($options = array()) {
		if (!isset($options['layout'])) {
			$options['layout'] = self::LAYOUT_BOTTOMRIGHT;
		}

		if (!isset($options['type'])) {
			$options['type'] = self::TYPE_ALERT;
		}

		$optionsJS = Json::encode($options);

		$js = " var options = {$optionsJS};
			for (var key in options) {
				$.noty.defaultOptions[key] = options[key];
			}";
		Yii::$app->view->registerJs($js);
	}

	public static function show($text, $type = 'alert') {
                $js = 'if(' . $type . '=="error"){
                        var noty_id = noty({"text":' . $text . ', "type":' . $type . ',
			"buttons": [{type: "btn btn-default", text: "Ok", click: function($noty) {					
							$noty.close();
						}
					},
				], "timeout":false });    
                       } else {
                            var noty_id = noty({"text":' . $text . ', "type":' . $type . '});
                       }';
		return $js;
	}

	public static function confirm($text, $callback, $type = 'alert') {
		return 'var noty_id = noty({"text":' . $text . ', "type":' . $type . ',
			"buttons": [{type: "btn btn-primary", text: "Ok", click: function($noty) {
							$noty.close();
							' . $callback . '
						}
					},
					{type: "btn", text: "Cancel", click: function($noty) {
							$noty.close();
						}
					},
				], "timeout":false });';
	}

	public static function alert($text, $type = 'alert') {
		return 'var noty_id = noty({"text":' . $text . ', "type":' . $type . ',
			"buttons": [{type: "btn btn-default", text: "Ok", click: function($noty) {					
							$noty.close();
						}
					},
				], "timeout":false });';
	}

}
