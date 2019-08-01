<?php

namespace appxq\sdii\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu as BaseMenu;

/**
 * Menu class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @package 
 * @version 2.0.0 Date: Sep 5, 2015 9:52:45 AM
 * @example
 */
class SDMenu extends BaseMenu {

	/**
	 * @var string the template used to render the body of a menu which is a link.
	 * In this template, the token `{url}` will be replaced with the corresponding link URL;
	 * while `{label}` will be replaced with the link text.
	 * This property will be overridden by the `template` option set in individual menu items via [[items]].
	 */
	public $linkTemplate = '<a href="{url}">{icon} {label}</a>';

	/**
	 * @var string the template used to render the body of a menu which is NOT a link.
	 * In this template, the token `{label}` will be replaced with the label of the menu item.
	 * This property will be overridden by the `template` option set in individual menu items via [[items]].
	 */
	public $labelTemplate = '{icon} {label}';

	/**
	 * @var string the template used to render a list of sub-menus.
	 * In this template, the token `{items}` will be replaced with the rendered sub-menu items.
	 */
	public $submenuTemplate = "\n<ul class='dropdown-submenu'>\n{items}\n</ul>\n";

	/**
	 * Initializes the menu widget.
	 * This method mainly normalizes the {@link items} property.
	 * If this method is overridden, make sure the parent implementation is invoked.
	 */
	public function init() {
		parent::init();
		$this->options['role'] = 'menu';
	}

	/**
	 * Renders the content of a menu item.
	 * Note that the container and the sub-menus are not rendered here.
	 * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
	 * @return string the rendering result
	 */
	protected function renderItem($item) {
		$icon = isset($item['icon']) ? '<i class="' . $item['icon'] . '"></i>' : '';

		if (isset($item['url'])) {
			$template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

			return strtr($template, [
				'{url}' => Html::encode(Url::to($item['url'])),
				'{label}' => $item['label'],
				'{icon}' => $icon,
			]);
		} else {
			$template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

			return strtr($template, [
				'{label}' => $item['label'],
				'{icon}' => $icon,
			]);
		}
	}

}
