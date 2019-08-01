<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use appxq\sdii\helpers\SDHtml;

class ActionColumn extends \yii\grid\ActionColumn {

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons() { 
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $url = Url::to(['/ezbuilder/ezform-builder/viewform', 'id' => $model->ezf_id]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('yii', 'View'), $url, [
                            'data-action' => 'view',
                            'title' => Yii::t('yii', 'View'),
                            'class' => 'btn btn-info btn-xs',
                ]);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $url = Url::to(['/ezbuilder/ezform-builder/update', 'id' => $model->ezf_id]);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('yii', 'Update'), $url, [
                            'data-action' => 'update',
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'btn btn-primary btn-xs',
                ]);
            };
        }
        if (!isset($this->buttons['trash'])) {
            $this->buttons['trash'] = function ($url, $model, $key) {
                $url = Url::to(['/ezforms2/ezform/trash', 'id' => $model->ezf_id]);
                return Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('yii', 'Delete'), $url, [
                            'data-action' => 'delete',
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'btn btn-danger btn-xs',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                ]);
            };
        }
        if (!isset($this->buttons['undo'])) {
            $this->buttons['undo'] = function ($url, $model, $key) {
                $url = Url::to(['/ezforms2/ezform/trash', 'id' => $model->ezf_id]);
                return Html::a(SDHtml::getBtnRepeat().' '.Yii::t('ezform', 'Restore'), $url, [
                            'data-action' => 'delete',
                            'title' => Yii::t('ezform', 'Restore'),
                            'class' => 'btn btn-success btn-xs',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to restore this item?'),
                ]);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('yii', 'Delete'), $url, [
                            'data-action' => 'delete',
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'btn btn-danger btn-xs',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                ]);
            };
        }
    }

}
