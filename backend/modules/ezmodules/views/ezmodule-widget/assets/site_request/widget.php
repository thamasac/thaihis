<?php
// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
'widget_config' => $widget_config,
'model' => $model, 
'modelOrigin'=>$modelOrigin,
'menu' => $menu,
'module' => $module,
'addon' => $addon,
'filter' => $filter,
'reloadDiv' => $reloadDiv,
'dataFilter' => $dataFilter,
'modelFilter' => $modelFilter,
'target' => $target,
    */
 
?>
<?php if (\Yii::$app->user->can('administrator') || \Yii::$app->user->can('adminsite') ): ?>
    <?php

    $this->title = Yii::t('user', 'Request join users');
    $this->params['breadcrumbs'][] = $this->title;
    ?>

<!--    <div>-->
<!--        <b style="color: #eb622c;margin-top:5px;">"Admin Service" is the default member who own every new created Project. Therefor, it is recommended that the Project Creator or Owner should click Edit to change the Password.</b>-->
<!--    </div>-->



<?php endif; ?>
<div class="clearfix" style="margin-bottom:20px;"></div>
<div class="table-responsive">
    <div id="view-user"></div>
</div>
<?php
try {
    echo \appxq\sdii\widgets\ModalForm::widget([
        'id' => 'modal-user',
        'size' => 'modal-lg',
        'tabindexEnable' => FALSE,
    ]);
} catch (Exception $e) {
    echo "Modal not work property.";
}
?>


<?php

$url = yii\helpers\Url::to(['/user/site-admin/user-request-view']);
$this->registerJs(<<<JS
initUser=function(){        
        let url = '$url';
        $.get(url, function(data){
            $('#view-user').html(data);
        }).fail(function(err) {
             err = JSON.parse(JSON.stringify(err))['responseText'];
             $('#view-user').html(`<div class='alert alert-danger'>\${err}</div>`);
        });
    }
    initUser();

function modalUser(url) {
        $('#modal-user .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $('#modal-user').modal('show')
        .find('.modal-content')
        .load(url);
    }

JS
);
?>


 