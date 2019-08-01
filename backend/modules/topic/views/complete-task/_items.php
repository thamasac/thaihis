<?php 
    use yii\helpers\Html;
    $class = '';
    if(!empty($model['status_complete']) && $model['status_complete'] == '1'){
        $class = 'alert alert-info';
    }else{
        $class = 'alert alert-warning';
    }
//    \appxq\sdii\utils\VarDumper::dump($widget);
?>
<div class="card dad <?= $class?>" data-id='<?= $model['id']?>' id="card-task-<?= $model['id']?>">
    <div class="header">
        <div class="row">
             <div class="pull-left">
                <a class="color-gray btn-task-delete" href='#'  data-id='<?= $model['id']?>'><i class="fa fa-times"></i></a>
            </div>
            <div class="pull-right">
                <?php 
                    echo Html::button("<i class='fa fa-eye'></i>", [
                        'class'=>'btn btn-default btn-sm btn-task-propover',
                        'data-placement'=>'top',
                        'title'=>isset($model['header_text']) ? $model['header_text'] : '',
                        'data-toggle'=>'popover',
                        'data-url'=>'/topic/complete-task/detail?id='.$model['id'],
                        //'data-trigger'=>'focus',
                        'data-content'=>isset($model['detail']) ? $model['detail'] : '',
                        'tabindex'=>'0',
                        //'title'=> Yii::t('topic','View'),
                        ]);
                ?>
                <?php 
                        if($model['status_complete'] != '1'){
                        echo Html::button("<i class='fa fa-check'></i>", [
                            'class'=>'btn btn-success btn-sm btn-task-done',
                            'data-url'=>'/topic/complete-task/detail?id='.$model['id'],
                            'tabindex'=>'0',
                            'data-id'=>$model['id'],
                            'data-status'=>'1',
                            'title'=> Yii::t('topic','Done'),
                            ]);
                        }else{
                           echo Html::button("<i class='fa fa-exclamation-triangle '></i>", [
                            'class'=>'btn btn-warning btn-sm btn-task-done',
                            'data-url'=>'/topic/complete-task/detail?id='.$model['id'],
                            'tabindex'=>'0',
                            'data-id'=>$model['id'],
                            'data-status'=>'2',
                            'title'=> Yii::t('topic','Warning'),
                            ]); 
                        }
                 ?>
                <?php
                $modalId = 'modal-mark';
            echo \backend\modules\ezforms2\classes\BtnBuilder::btn()
                        ->ezf_id('1542120564041895900')
                        ->options(['class' => 'btn btn-primary btn-sm', 'widget_id'=>$model['widget_id']])
                        ->modal($modalId)
                        //->reloadDiv($reloadDiv)
                        ->label('<i class="fa fa-pencil"></i> ')
                        ->buildBtnEdit($model['id']);
        ?>
            </div>
        </div> 
    </div>

    <div class="content">
        <div class="row">
            <div class="col-xs-1 text-center">
                <div class="avatar content-image-check">
                    <?php if(!empty($model['status_complete']) && $model['status_complete'] == '1'):?>
                        <i class="fa fa-check check-complete"></i>
                    <?php else:?>
                        <i class="fa fa-exclamation-triangle check-warning" aria-hidden="true"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-xs-11">
                <h4><?= isset($model['header_text']) ? $model['header_text'] : ''?></h4>
                <div class="text-muted">
                    <small>
                        <?= isset($model['content']) ? $model['content'] : ''?>
                    </small>
                </div>
                <div class="text-muted">
                    <small>
                        <i class="fa fa-user"></i>
                        <?php
                            $user_id = isset($model['user_id']) ? $model['user_id'] : '';
                            $user = cpn\chanpan\classes\CNUser::getUserNcrcById($user_id);
                            $fname = isset($user['profile']['firstname']) ? $user['profile']['firstname'] : '';
                            $lname = isset($user['profile']['lastname']) ? $user['profile']['lastname'] : '';
                            $name = "{$fname} {$lname}";
                            echo "{$name}"
                        ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>