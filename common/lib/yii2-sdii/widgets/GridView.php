<?php

namespace appxq\sdii\widgets;

use Yii;
use yii\helpers\Html;
use yii\grid\GridView as BaseGridView;

/**
 * GridView class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @package appxq\sdii\widgets
 * @version 2.0.0 Date: Sep 5, 2015 3:18:34 PM
 * @example 
 */
class GridView extends BaseGridView {

	/**
	 * @var array the HTML attributes for the grid table element.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $tableOptions = ['class' => 'table table-striped table-bordered table-hover'];
	public $responsiveTable = true;
	public $panel = true;
	public $panelBtn = '';
        public $spacBottom = false;
	/**
	 * Initializes the widget.
	 */
	public function init() {
		parent::init();
                $spacBottom = $this->spacBottom?'<br><br><br><br>':'';
		if ($this->panel) {
			$items = ($this->responsiveTable) ? Html::tag('div', '{items}'.$spacBottom, ['class' => 'table-responsive']) : '{items}';
			$this->layout = <<<EOD
	    <div class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
			    <div class="col-md-6">{summary}</div>
			    <div class="col-md-6 text-right">
				    $this->panelBtn
			    </div>
			</div>
		</div>
		$items
		<div class="panel-footer">{pager}</div>
	    </div>	
EOD;
		} else {
                    $items = ($this->responsiveTable) ? Html::tag('div', '{items}', ['class' => 'table-responsive']) : '{items}';
			$this->layout = <<<EOD
                {summary}
		$items
                $spacBottom
		{pager}
EOD;
                }
	}

}
