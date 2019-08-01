<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Ezform */

$this->title = 'Custom View Table';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ezforms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
      <?php EzfStarterWidget::begin(); ?>
      <?php
            echo Html::a('<i class="fa fa-mail-reply"></i> '. Yii::t('ezform', 'Back to Form page'), Url::to(['/ezforms2/ezform/index']), ['class' => 'btn btn-default']).' ';
            echo Html::a('<i class="fa fa-table" aria-hidden="true"></i> '.Yii::t('ezform', 'View Custom Table'), Url::to(['/ezforms2/ezform/view']), ['class' => 'btn btn-info']).'<br><br>';
        ?>
      
      <?php
      $reloadDiv = 'view-table-box';
      $fields = ['cname','cformt'];
      $actions = ['1548752916088628500'=> [
            'action' => '<button class="btn btn-info btn-xs btn-view-table " data-url="/ezforms2/ezform/view-table-wide?id={id}"><i class="glyphicon glyphicon-list-alt"></i> View Table</button>',
            'cond' => ''
      ]];
      echo \backend\modules\ezforms2\classes\EzfHelper::ui('1548222287069940600')
        ->data_column($fields)
        ->reloadDiv($reloadDiv)
        ->default_column(0)
        ->actions($actions)
        ->title('')
        ->buildGrid();
      //view-table-box
      ?>
      
      <?php \richardfan\widget\JSRegister::begin([
            //'key' => 'bootstrap-modal',
            'position' => \yii\web\View::POS_READY
        ]); ?>
        <script>
            // JS script
            $('#view-table-box').on('click', '.btn-view-table', function(){
                let url = $(this).attr('data-url');
                
                modalEzformMain(url, 'modal-ezform-main-xl');
            });
            
        </script>
        <?php \richardfan\widget\JSRegister::end(); ?>

      <?php EzfStarterWidget::end(); ?>
       
    </div>
</div>
