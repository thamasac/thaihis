<?php

namespace appxq\sdii\widgets;

use Yii;
use yii\helpers\Html;
use yii\bootstrap\Widget as BaseWidget;

/**
 * ModalForm class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @package appxq\sdii\widgets
 * @version 2.0.0 Date: Sep 5, 2015 3:18:34 PM
 * @example 
 */
class ModalForm extends BaseWidget {

	const SIZE_LARGE = "modal-lg";
	const SIZE_SMALL = "modal-sm";
	const SIZE_DEFAULT = "";

	/**
	 * @var string the header content in the modal window.
	 */
	public $header;

	/**
	 * @var string additional header options
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 * @since 2.0.1
	 */
	public $headerOptions;

	/**
	 * @var string the footer content in the modal window.
	 */
	public $footer;

	/**
	 * @var string additional footer options
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 * @since 2.0.1
	 */
	public $footerOptions;

	/**
	 * @var string the modal size. Can be [[SIZE_LARGE]] or [[SIZE_SMALL]], or empty for default.
	 */
	public $size;

	/**
	 * @var array|false the options for rendering the close button tag.
	 * The close button is displayed in the header of the modal window. Clicking
	 * on the button will hide the modal window. If this is false, no close button will be rendered.
	 *
	 * The following special options are supported:
	 *
	 * - tag: string, the tag name of the button. Defaults to 'button'.
	 * - label: string, the label of the button. Defaults to '&times;'.
	 *
	 * The rest of the options will be rendered as the HTML attributes of the button tag.
	 * Please refer to the [Modal plugin help](http://getbootstrap.com/javascript/#modals)
	 * for the supported HTML attributes.
	 */
	public $closeButton = [];

	/**
	 * @var array the options for rendering the toggle button tag.
	 * The toggle button is used to toggle the visibility of the modal window.
	 * If this property is false, no toggle button will be rendered.
	 *
	 * The following special options are supported:
	 *
	 * - tag: string, the tag name of the button. Defaults to 'button'.
	 * - label: string, the label of the button. Defaults to 'Show'.
	 *
	 * The rest of the options will be rendered as the HTML attributes of the button tag.
	 * Please refer to the [Modal plugin help](http://getbootstrap.com/javascript/#modals)
	 * for the supported HTML attributes.
	 */
	public $toggleButton = false;

	public $tabindexEnable = true;
	
	public function init() {
		parent::init();

		$this->initOptions();

		echo $this->renderToggleButton() . "\n";
		echo Html::beginTag('div', $this->options) . "\n";
		echo Html::beginTag('div', ['class' => 'modal-dialog ' . $this->size]) . "\n";
		echo Html::beginTag('div', ['class' => 'modal-content']) . "\n";
	}

	/**
	 * Renders the widget.
	 */
	public function run() {
		echo "\n" . Html::endTag('div'); // modal-content
		echo "\n" . Html::endTag('div'); // modal-dialog
		echo "\n" . Html::endTag('div');

		$this->registerPlugin('modal');
	}

	/**
	 * Renders the toggle button.
	 * @return string the rendering result
	 */
	protected function renderToggleButton() {
		if ($this->toggleButton !== false) {
			$tag = ArrayHelper::remove($this->toggleButton, 'tag', 'button');
			$label = ArrayHelper::remove($this->toggleButton, 'label', 'Show');
			if ($tag === 'button' && !isset($this->toggleButton['type'])) {
				$this->toggleButton['type'] = 'button';
			}

			return Html::tag($tag, $label, $this->toggleButton);
		} else {
			return null;
		}
	}

	/**
	 * Initializes the widget options.
	 * This method sets the default values for various options.
	 */
	protected function initOptions() {
		$this->options = array_merge([
			'class' => 'fade',
			'role' => 'dialog',
			], $this->options);
		
		if($this->tabindexEnable){
		    $this->options = array_merge([
			'tabindex' => -1,
		    ], $this->options);
		}
		
		Html::addCssClass($this->options, 'modal');

		if ($this->clientOptions !== false) {
			$this->clientOptions = array_merge(['show' => false], $this->clientOptions);
		}

		if ($this->closeButton !== false) {
			$this->closeButton = array_merge([
				'data-dismiss' => 'modal',
				'aria-hidden' => 'true',
				'class' => 'close',
					], $this->closeButton);
		}

		if ($this->toggleButton !== false) {
			$this->toggleButton = array_merge([
				'data-toggle' => 'modal',
					], $this->toggleButton);
			if (!isset($this->toggleButton['data-target']) && !isset($this->toggleButton['href'])) {
				$this->toggleButton['data-target'] = '#' . $this->options['id'];
			}
		}
	}

}
