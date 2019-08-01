<?php

namespace appxq\sdii\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu;

/**
 * Sidebar class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @package 
 * @version 2.0.0 Date: Sep 5, 2015 9:52:45 AM
 * @example <div class="page-sidebar navbar-collapse collapse " >
  <?= appxq\sdii\widgets\Sidebar::widget([
  'firstItemCssClass'=>'start',
  'lastItemCssClass'=>'last',
  'items' => [
  ['label'=>'หน้าแรก', 'icon'=>'icon-home', 'url'=>array('//site/index')],
  ['label'=>'ข้อมูลพนักงาน', 'icon'=>'icon-user', 'url'=>['//kaduct'], 'active' =>($controllerID == 'kaduct')],
  ['label'=>'ตั้งค่า', 'icon'=>'icon-wrench', 'url'=>'#', 'items'=>[
  ['label'=>'ข้อมูลองค์กร', 'icon'=>'icon-book', 'url'=>['//companyProfile/index']],
  ['label'=>'รายการเงินเดือน', 'icon'=>'icon-group', 'active' =>$controllerID=='masterPaylist', 'url'=>['//masterPaylist']],
  ]],
  ['label'=>'Help', 'icon'=>'icon-question', 'url'=>'#'],
  ],
  ]); ?>
  </div>
 */
class Sidebar extends Menu {

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
	public $submenuTemplate = "\n<ul class='sub-menu'>\n{items}\n</ul>\n";
	public $activeStatus = '';

	/**
	 * Initializes the menu widget.
	 * This method mainly normalizes the {@link items} property.
	 * If this method is overridden, make sure the parent implementation is invoked.
	 */
	public function init() {
		parent::init();
		$this->options['role'] = 'menu';

		if (isset($this->options['class'])) {
			$this->options['class'] .= ' page-sidebar-menu';
		} else {
			$this->options['class'] = 'page-sidebar-menu';
		}
	}

	/**
	 * Recursively renders the menu items (without the container tag).
	 * @param array $items the menu items to be rendered recursively
	 * @return string the rendering result
	 */
	protected function renderItems($items) {
		$n = count($items);
		$lines = [];
		foreach ($items as $i => $item) {
			$options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
			$tag = ArrayHelper::remove($options, 'tag', 'li');
			$class = [];
			if ($item['active'] && $this->activeCssClass != '') {
				if (!empty($item['items'])) {
					$this->activeStatus = ' open';
				}
				$class[] = $this->activeCssClass . $this->activeStatus;
			}
			if ($i === 0 && $this->firstItemCssClass !== null) {
				$class[] = $this->firstItemCssClass;
			}
			if ($i === $n - 1 && $this->lastItemCssClass !== null) {
				$class[] = $this->lastItemCssClass;
			}
			if (isset($item['disabled'])) {
				$class[] = 'disabled';
			}
			if (!empty($class)) {
				if (empty($options['class'])) {
					$options['class'] = implode(' ', $class);
				} else {
					$options['class'] .= ' ' . implode(' ', $class);
				}
			}

			$menu = $this->renderItem($item);
			if (!empty($item['items'])) {//if (isset($item['items']) && !empty($item['items'])) {
				$submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
				$menu .= strtr($submenuTemplate, [
					'{items}' => $this->renderItems($item['items']),
				]);
			}
			if ($tag === false) {
				$lines[] = $menu;
			} else {
				$lines[] = Html::tag($tag, $menu, $options);
			}
		}

		return implode("\n", $lines);
	}

	/**
	 * Renders the content of a menu item.
	 * Note that the container and the sub-menus are not rendered here.
	 * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
	 * @return string the rendering result
	 */
	protected function renderItem($item) {
		$label = '<span class="title">' . $item['label'] . '</span>';
		$icon = isset($item['icon']) ? '<i class="' . $item['icon'] . '"></i>' : '';

		if (!empty($item['items'])) {
			$item['url'] = '#';
			$label = '<span class="title">' . $item['label'] . '</span> <span class="arrow' . $this->activeStatus . '"></span>';
			$this->activeStatus = '';
		}

		if (isset($item['url'])) {
			$template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

			return strtr($template, [
				'{url}' => Html::encode(Url::to($item['url'])),
				'{label}' => $label,
				'{icon}' => $icon,
			]);
		} else {
			$template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

			return strtr($template, [
				'{label}' => $label,
				'{icon}' => $icon,
			]);
		}
	}

}
