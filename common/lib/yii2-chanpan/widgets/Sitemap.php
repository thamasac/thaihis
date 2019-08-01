<?php

namespace cpn\chanpan\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu;
use Yii;

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
class Sitemap extends Menu {
    public function run()
    {
      parent::run();
      $view = $this->getView();
       \cpn\chanpan\assets\SiteMapAssets::register($view); 
    }
}

