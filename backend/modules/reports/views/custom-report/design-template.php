<?php

use backend\modules\ezforms2\classes\BtnBuilder;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfStarterWidget;

$reloadDiv = "custom-template";
$modal = "modal-ezform-main";
$ezf_id = '1537848949032767100';
$user_id    = isset(Yii::$app->user->id) ? Yii::$app->user->id : '';
?>
<?php EzfStarterWidget::begin(); ?>


<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-left">Template Custom Print</div>
                    <div class="pull-right">
                        <?php
                            echo backend\modules\ezforms2\classes\BtnBuilder::btn()
                                ->ezf_id($ezf_id)
                                ->label('<i class="glyphicon glyphicon-pencil"></i>')->options(['class' => 'btn btn-primary btn-sm'])
                                ->ezf_id($ezf_id)
                                ->reloadDiv($reloadDiv)
                                ->label('<i class="glyphicon glyphicon-plus"></i> Add')->options(['class' => 'btn btn-success btn-sm'])
                                ->buildBtnAdd();
                        ?>
                    </div>
                </div>
            </div>
        </div> 
    </div>
    <div class="panel-body">
        <?php
            echo EzfHelper::ui($ezf_id)
                    ->data_column(['default_template', 'custom_layout_size', 'template_id','template_name'])
                    ->default_column(0)
                    ->reloadDiv($reloadDiv)
                    ->search_column(['user_create' => $user_id])
                    ->modal($modal)->buildGrid();
        ?>
    </div>
</div>
<?php EzfStarterWidget::end(); ?>