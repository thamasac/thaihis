<?php
   use yii\widgets\ListView;
   use yii\helpers\Html;
   use yii\helpers\Url;
?>
<section id="items-sides" class="items-sidebars" role="complementary" >
    <div id="items-side-scrolls" class="row">
        <div class="col-lg-12">
            <div class=" sidebar-nav-title" ><?= Yii::t('ezform', 'All Modules')?> 
                
            </div>
            <div id="ezf-search" style="padding: 5px;">
                <?= Html::textInput('search', '', ['id'=>'search','class'=>'form-control','placeholder'=> Yii::t('ezform', 'Search the form name.')]) ?>
            </div>
            <div id="ezf-items">
                <?php
                //appxq\sdii\utils\VarDumper::dump($dataProvider);
                echo ListView::widget([
                    'id'=>'ezf_dad',
                    'dataProvider' => $dataProvider,
                    'itemOptions' => ['class' => 'item dads-children'],
                    'layout'=>'<div class=" sidebar-nav-title text-right" >{summary}</div>{items}<div class="list-pager">{pager}</div>',
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render('_view', [
                            'model' => $model,
                            'key' => $key,
                            'index' => $index,
                            'widget' => $widget,
                            'ezm_id' => $model['ezm_id'],
                        ]);
                    },
                ])
                ?>
            </div>
        </div>
    </div>
</section>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
   
    $('.page-column').addClass('items-views');
    //itemsSidebar();
    $('#main-nav-app .navbar-header').append('<a class="a-collapse glyphicon glyphicon-th-list navbar-toggle" data-toggle="collapse" data-target="#items-side">&nbsp;</a>');

    /*function  getHeight() {
        var sidebarHeight = $('#module-all').height(); //- $('.header').height()
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
                position: 'right',
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

    }*/
    
    //Search 
    $('#search').change(function(){
        let values = $(this).val();
        Searchs(values); 
        return false;
    });
    $('#search').keyup(function(e){
        if(e.keyCode == 13)
        {
           let values = $(this).val(); 
           Searchs(values);
        }
        return false;
    });
    function Searchs(values){ 
        let url = '<?= Url::to(['/manage_modules/default/get-module']);?>';         
        $.get(url,{term:values}, function(data){
           $('#module-all').html(data); 
           
        });
        return false;
   }
   function pagenations(url){
        setTimeout(function(){
          $.get(url, function(data){
                //$('html, body').scrollTop(0);
                $('#module-all').html(data); 
                getManageModules();
                getManageModulesDisabled();
                
          });  
        },500);
        
        //return false;
   }
   $('.pagination li a').click(function(){
       let url = $(this).attr('href');
       pagenations(url);
       return false;
   });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>

<?php appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    .list-view .item a{text-decoration: none;}
    .list-view .item a.media {
        border-bottom: 1px solid #ddd;
        padding: 10px;
        display: block;
        color: #666;
    }
    .list-view .item a.media:hover {
        text-decoration: none;
        background-color: #eee;
    }
    .list-view .item a.media.active {
    background-color: #d1ecf1;
    }

    .list-view .item a.media.active:hover {
        background-color: #d1ecf1;
    }

    .list-view .item a.media:focus {
        text-decoration: none;
    }
    #items-side{
        margin: 0;
        padding: 0;
    }
     .items-views {
        margin-left: 0px;
    }
</style>
<?php appxq\sdii\widgets\CSSRegister::end();?>