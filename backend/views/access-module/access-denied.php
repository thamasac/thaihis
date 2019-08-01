<?php 
use yii\helpers\Html;
$this->title = Yii::t("chanpan", "Access Denied");
//$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'EzModule'), 'url' => ['/ezmodules/default/index']];
$this->params['breadcrumbs'][] = $this->title;
$module_id = Yii::$app->request->get('module_id', '');
//\appxq\sdii\utils\VarDumper::dump($module_id);
?>
 
    <div class="alert alert-danger">
        <h3 style="font-size: 30pt;font-weight: bold"><i class="mdi mdi-eye-off"></i> 
            <?= Html::encode($this->title); ?></h3>

    </div>
    <div class="col-md-12">        
        <?php
        echo \yii\helpers\Html::a("<b><i class='fa fa-mail-reply'></i></b> ", ['/ezmodules/ezmodule/view?id='.$module_id], [
            'class' => 'btn btn-default',
            'data-toggle' => 'tooltip',
            'title' => Yii::t('ezmodule', 'Back to module'),
        ]);
        ?>
        <span>Back to Home </span>
        &nbsp;&nbsp;-OR-&nbsp;&nbsp;
        
        
        <?php
        echo \yii\helpers\Html::a("<b><i class='fa fa-users'></i></b> ", ["/ezmodules/ezmodule/permission", 'id' => $module_id], [
            'id' => 'modal-btn-ezmodule2',
            'class' => 'btn btn-warning',
            'data-toggle' => 'tooltip',
            'title' => Yii::t('ezmodule', 'Module'),
        ]);
        ?>
        <span>Show Permission </span>
    </div>
  

<div class="clearfix"></div><hr />
<?php
echo yii\bootstrap\Modal::widget([
    'id' => 'modal-ezform-main',
    'size' => 'modal-xxl',
    'options' => ['tabindex' => false],
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false]
]);
?>

<?php
$this->registerJs("
       
        $('#modal-btn-ezmodule2').on('click', function() {
            let url = $(this).attr('href');
             
            $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-ezform-main').modal('show')
            .find('.modal-content')
            .load(url);
            return false;
        });
    ");
?>






























