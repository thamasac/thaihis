<?php

use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;
use appxq\sdii\widgets\ModalForm;


\backend\modules\ezforms2\assets\ListdataAsset::register($this);
\backend\modules\ezforms2\assets\EzfToolAsset::register($this);

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'EzForms'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = Yii::t('ezform', 'Data List');
?>
<?php EzfStarterWidget::begin(); ?>
<section id="items-side" class="items-sidebar navbar-collapse collapse" role="complementary" >
    <div id="items-side-scroll" class="row">
        <div class="col-lg-12">
            <div class=" sidebar-nav-title" ><?= Yii::t('ezform', 'Ezform Lists')?> 
                <a id="favorite-form-manager"  data-url="<?= \yii\helpers\Url::to(['/ezforms2/data-lists/list'])?>" class="pull-right " style="color: #007bff; cursor: pointer;"><i class="glyphicon glyphicon-star"></i> <?= Yii::t('ezform', 'Favorite Forms')?></a>
            </div>
            <?php echo $this->render('_search', ['model' => $searchModel]);  ?>
            <div id="ezf-items">
                <?=
                ListView::widget([
                    'id'=>'ezf_dad',
                    'dataProvider' => $dataProvider,
                    'itemOptions' => ['class' => 'item dads-children'],
                    'layout'=>'<div class=" sidebar-nav-title text-right" >{summary}</div>{items}<div class="list-pager">{pager}</div>',
                    'itemView' => function ($model, $key, $index, $widget) use ($ezf_id) {
                        return $this->render('_view', [
                            'model' => $model,
                            'key' => $key,
                            'index' => $index,
                            'widget' => $widget,
                            'ezf_id' => $ezf_id,
                        ]);
                    },
                ])
                ?>
            </div>
        </div>
    </div>
</section>

<section id="items-views" role="complementary" >
    <?php if($ezf_id>0): ?>
    
    <div class="row"> 
    <div class="col-md-12 ">
        <?php
        $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevOne($ezf_id);
        if($modelEzf){
            echo EzfHelper::uiEmr($ezf_id, $target, 'view-emr-lists', 'modal-ezform-main', 0, $view);
        } else {
            echo '<div class="alert alert-danger" role="alert"> '. \appxq\sdii\helpers\SDHtml::getMsgError().' '.Yii::t('app', 'EzForm is not allowed.').'</div>';
        }
        ?>
    </div>
        </div>
    
    <?php else:?>
    
    <!--<img src="http://storage.nhis.dev/?path=source/doc_icon.png&filt=sepia&w=50" >-->
    <h1 class="text-center" style="font-size: 45px; color: #ccc; margin: 200px 0;"><?= Yii::t('ezform', 'Please select a form.')?></h1>
    <?php endif;?>
</section>
<?php EzfStarterWidget::end(); ?>
<?=
ModalForm::widget([
    'id' => 'modal-ezform-favorite',
    'size' => 'modal-lg',
]);
?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('.page-column').addClass('items-views');
    
    $('#ezf_dad').dad({
        draggable:'.draggable',
        callback:function(e){
            var positionArray = [];
            $('#ezf_dad').find('.dads-children').each(function(){
                positionArray.push($(this).attr('data-key'));
            });

            $.post('<?= \yii\helpers\Url::to(['/ezforms2/data-lists/order-update'])?>',{position:positionArray},function(result){

            });
        }
    });

    itemsSidebar();

    $('#main-nav-app .navbar-header').append('<a class="a-collapse glyphicon glyphicon-th-list navbar-toggle" data-toggle="collapse" data-target="#items-side">&nbsp;</a>');

    $('#favorite-form-manager').click(function(){
        var url = $(this).attr('data-url');
        modalEzform(url);
        return false;
    });
    
    $('#modal-ezform-favorite').on('hidden.bs.modal', function (e) {
        location.reload();
    });
    
    function modalEzform(url) {
    $('#modal-ezform-favorite .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-favorite').modal('show')
    .find('.modal-content')
    .load(url);
}

    function  getHeight() {
        var sidebarHeight = $(window).height() - 51; //- $('.header').height()
        if ($('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - $('.footer').height();
        }
        return sidebarHeight;
    }

    function  itemsSidebar() {
        var itemside = $('#items-side-scroll');

        if ($('.page-sidebar-fixed').length === 0) {
            return;
        }

        if ($(window).width() >= 992) {
            var sidebarHeight = getHeight();

            itemside.slimScroll({
                size: '7px',
                color: '#a1b2bd',
                opacity: .8,
                //position: 'right',
                height: sidebarHeight,
                allowPageScroll: false,
                disableFadeOut: false
            });
        } else {
            if (itemside.parent('.slimScrollDiv').length === 1) {
                itemside.slimScroll({
                    destroy: true
                });
                itemside.removeAttr('style');
                $('.items-sidebar').removeAttr('style');
            }
        }

    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>