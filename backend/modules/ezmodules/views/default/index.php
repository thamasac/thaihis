<?php

use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

$tab = isset($_GET['tab']) ? $_GET['tab'] : 1;
$tablist = [
    'none',
    Yii::t('ezmodule', 'My Favorite'),
    Yii::t('ezmodule', 'Created by me'),
    Yii::t('ezmodule', 'Assigned to me'),
    Yii::t('ezmodule', 'Public'),
    Yii::t('ezmodule', 'Restricted'),
    Yii::t('ezmodule', 'My Sub-modules'),
    Yii::t('ezmodule', 'I co-created')
];
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'EzModule'), 'url' => ['/ezmodules/default/index']];
$this->params['breadcrumbs'][] = isset($tablist[$tab])?$tablist[$tab]:'';

backend\modules\ezforms2\assets\EzfAsset::register($this);

?>

<div class="ezmodules-app" >
  
    <?=
    \yii\bootstrap\Nav::widget([
        'items' => [
            [
                'label' => $tablist[1],
                'url' => Url::to(['/ezmodules/default/index']),
                'active' => $tab == 1,
            ],
            [
                'label' => $tablist[2],
                'url' => Url::to(['/ezmodules/default/index', 'tab' => 2]),
                'active' => $tab == 2,
            ],
            [
                'label' => $tablist[7],
                'url' => Url::to(['/ezmodules/default/index', 'tab' => 7]),
                'active' => $tab == 7,
            ],
//            [
//                'label' => $tablist[3],
//                'url' => Url::to(['/ezmodules/default/index', 'tab' => 3]),
//                'active' => $tab == 3,
//            ],
            [
                'label' => $tablist[4],
                'url' => Url::to(['/ezmodules/default/index', 'tab' => 4]),
                'active' => $tab == 4,
            ],
            [
                'label' => $tablist[5],
                'url' => Url::to(['/ezmodules/default/index', 'tab' => 5]),
                'active' => $tab == 5,
            ],
            [
                'label' => $tablist[6],
                'url' => Url::to(['/ezmodules/default/index', 'tab' => 6]),
                'active' => $tab == 6,
            ],
        ],
        'options' => ['class' => 'nav nav-tabs'],
    ]);
    ?>

    <div id="main-app" style="margin-top: 10px;">
      <a href="<?= Url::to(['/ezmodules/ezmodule/form-create'])?>" class="btn btn-success btn-sm" id="btn-add-module">
            <i class="glyphicon glyphicon-plus"></i> 
            <?= Yii::t('ezmodule', 'Create New EzModule')?>
        </a>
        <a href="<?= Url::to(['/ezmodules/ezmodule/index'])?>" class="btn btn-default btn-sm">
            <i class="glyphicon glyphicon-cog"></i> 
            <?= Yii::t('ezmodule', 'Manage the created Module')?>
        </a>
      <?php //Pjax::begin(['id' => 'ezmodule-grid-pjax', 'timeout' => FALSE]); ?>
        <div class="row">
            <div class="col-md-12">
                <?php if ($tab == 1): ?>
                    <?=
                    $this->render('_myfav')
                    ?>
                <?php elseif ($tab == 2): ?>
                    <?=
                    $this->render('_mymodule')
                    ?>    
                <?php elseif ($tab == 3): ?>
                    <?=
                    $this->render('_tome')
                    ?>
                <?php elseif ($tab == 4): ?>
                    <?=
                    $this->render('_public_module')
                    ?>
                <?php elseif ($tab == 5): ?>
                    <?=
                    $this->render('_restircted')
                    ?>
                 <?php elseif ($tab == 6): ?>
                    <?=
                    $this->render('_mysubmodule')
                    ?>   
                <?php elseif ($tab == 7): ?>
                    <?=
                    $this->render('_cocreated')
                    ?>   
                <?php endif; ?>
            </div>
        </div>
      <?php //Pjax::end(); ?>
    </div>

</div>
<?=\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-create',
    'size'=>'modal-xxl',
]);
?>

<?=\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-info-app',
    //'size'=>'modal-lg',
]);
?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>

    // JS script
    //$.pjax.reload({container:'#ezmodule-grid-pjax',timeout: false});
    reloadNow = 0;
    tab = <?=$tab?>;

    $('#modal-info-app').on('hidden.bs.modal', function(e){
        if(reloadNow==1 && tab==1){
            location.reload();
        }
    });   
    
    $('#modal-create').on('hidden.bs.modal', function(e){
        if(reloadNow==1 && (tab==1 || tab==2)){
            location.reload();
        }
    });

    $('.info-app').on('click', function() {
        modalApp($(this).attr('data-url'));
    });    
    
    $('#btn-add-module').on('click', function() {
        modalCreate($(this).attr('href'));
        return false;
    });    
    

    $('#modal-create').on('click', '.btn-back', function() {
        $('#modal-create #view-method').show();
        $('#modal-create #view-form').hide();
    });

    $('#modal-create').on('click', '#modal-step-ezmodule', function() {
        var url = $(this).attr('data-url');
        $('#modal-create #view-method').hide();
        $('#modal-create #view-form').show();
        $('#modal-create #view-form .content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');

        $.ajax({
            url:url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#modal-create #view-form .content').html(result);
            }
        });
    });

    $('#modal-create').on('click', '.btn-tmp', function() {
        var url = $(this).attr('href');

        $('body').waitMe({
                effect : 'facebook',
                text : '<?=Yii::t('ezform', 'Please wait...')?>',
                bg : 'rgba(0,0,0,0.7)',
                color : '#FFF',
                maxSize : '',
                waitTime : -1,
                textPos : 'vertical',
                fontSize : '20px',
                source : '',
                onClose : function() {
                    //$('#btn-line').trigger('click');
                }
            });

        $.ajax({
                url:url,
                dataType: 'JSON',
                success: function(result, textStatus) {
                    if(result.status == 'success') {
                        $('#modal-create #view-method').hide();
                        $('#modal-create #view-form').show();
                        //$.pjax.reload({container:'#ezmodule-grid-pjax',timeout: false});
                        reloadNow = 1;
                        $('#modal-create #view-form .content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');

                        $.ajax({
                            url:'<?=Url::to(['/ezmodules/ezmodule/save'])?>',
                            method: 'GET',
                            data:{id:result.id},
                            dataType: 'HTML',
                            success: function(result, textStatus) {
                                $('#modal-create #view-form .content').html(result);
                            }
                        });
                    } else {
                        <?=SDNoty::show('result.message', 'result.status')?>
                    } 
                    $('body').waitMe('hide');
                }
            });

        return false;
    });
    function modalApp(url) {
        $('#modal-info-app .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-info-app').modal('show')
        .find('.modal-content')
        .load(url);
    }
    
    function modalCreate(url) {
        $('#modal-create .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-create').modal('show')
        .find('.modal-content')
        .load(url);
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>