<?=

\appxq\sdii\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions'=>function($model){
        return ['class' => 'danger', 'id'=>'tr_'.$model['id']];
     },
    'columns' => [
        [
                    'format'=>'raw',
                    'attribute'=>'message',
                    'label'=>'Message',
                    'value'=>function($model){
                            $html = "<div style='height:100px;width:450px;overflow:hidden;overflow-y: auto;'>";
                            $html .= isset($model['message']) ? $model['message'] : 'not found';
                            $html .= "</div>";
                            return $html; 
                        
                    }
        ],
        [
                    'format'=>'raw',
                    'attribute'=>'status',
                    'label'=>'Status',
                    'value'=>function($model){
                        return "Error";
                    }
        ],
        [
                    'format'=>'raw',
                    'attribute'=>'user_id',
                    'label'=>'User Create',
                    'value'=>function($model){
                        //\appxq\sdii\utils\VarDumper::dump(::GetUserNcrcById($model['user_id']));
                        $user = \cpn\chanpan\classes\CNUser::GetUserNcrcById($model['user_id']);
                        if($user['profile']){
                            return "{$user['profile']['firstname']} {$user['profile']['firstname']}";
                        } 
                        return '';
                    }
        ],
        [
                    'format'=>'raw',
                    'attribute'=>'date',
                    'label'=>'Date',
                    'value'=>function($model){
                        return appxq\sdii\utils\SDdate::mysql2phpDate($model['date']);
                    }
        ],
        [
                    'format'=>'raw',
                    'attribute'=>'sql_command',
                    'label'=>'SQl Command', 
                    'value'=>function($model){
                        $html = "<div style='height:100px;width:450px;overflow:hidden;overflow-y: auto;'>";
                            $html .= \yii\helpers\Html::encode($model['sql_command']);
                        $html .= "</div>";
                        return $html;
                    }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}', // the default buttons + your custom button
            'buttons' => [
                'update' => function($url, $model, $key) {     // render your custom button
                    return yii\helpers\Html::a("<i class='fa fa-edit'></i> Delete", $url, ['data-id'=>$model['id'],'class' => 'btn btn-danger btnDelete']);
                }
            ]
        ]
    ],
])
?>

<?php \richardfan\widget\JSRegister::begin();?>
<script>
    $('.btnDelete').on('click', function(){
        let id = $(this).attr('data-id');
        let url = '/update_project/default/delete-log';
        bootbox.confirm({
            title: 'Confirm',
            message: 'Are you sure?',            
            callback: function (result) {
                if (result) {
                    $.post(url, {id:id}, function(result){
                        $('#tr_'+id).remove();
                         
                        <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') ?>
                    });
                }else{

                }
            }
        });
        
         
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end();?>

