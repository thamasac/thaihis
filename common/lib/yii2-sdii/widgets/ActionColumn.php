<?php

namespace appxq\sdii\widgets;

use Yii;
use yii\helpers\Html;
use yii\grid\ActionColumn as BaseActionColumn;

/**
 * ActionColumn class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @package 
 * @version 2.0.0 Date: Sep 5, 2015 9:52:45 AM
 * @example 
 */
class ActionColumn extends BaseActionColumn {

	public $pjax_id;

	/**
	 * Initializes the default button rendering callbacks.
	 */
	protected function initDefaultButtons() {
		if (!isset($this->buttons['view'])) {
			$this->buttons['view'] = function ($url, $model, $key) {
				return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
							'data-action' => 'view',
							'title' => Yii::t('yii', 'View'),
							'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
				]);
			};
		}
		if (!isset($this->buttons['update'])) {
			$this->buttons['update'] = function ($url, $model, $key) {
				return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
							'data-action' => 'update',
							'title' => Yii::t('yii', 'Update'),
							'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
				]);
			};
		}
		if (!isset($this->buttons['delete'])) {
			$this->buttons['delete'] = function ($url, $model, $key) {
				return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
							'data-action' => 'delete',
							'title' => Yii::t('yii', 'Delete'),
							'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
							'data-method' => 'post',
							'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
				]);
			};
		}
	}

}
