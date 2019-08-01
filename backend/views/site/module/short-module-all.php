<?php 
    use yii\helpers\Url;
?> 


<?=
\yii\widgets\ListView::widget([
    'dataProvider' => $moduleAllProvider,
    'options' => [
        'tag' => 'div',
        'class' => 'row',
        //        'id' => 'section-all',
        'id' => 'ezf_dad_main',
    ],
    'itemOptions' => function($model) {
        return ['tag' => 'div', 'id' => '', 'data-id' => '', 'class' => 'col-md-12', 'style' => ''];
    },
    'layout' => '<div class=" sidebar-nav-title text-right" >{summary}</div><div class="letf-module">{items}</div><div class="pagers">{pager}</div>',
    'itemView' => function ($model, $key, $index, $widget) {
        return $this->render('_item-module-all', ['model' => $model]);
    },
     
]);
?>  
<?php appxq\sdii\widgets\CSSRegister::begin(); ?>
<style>
    .letf-module a{text-decoration:none;}
</style>
<?php appxq\sdii\widgets\CSSRegister::end();?>