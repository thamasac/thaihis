<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
backend\modules\ezforms2\assets\ListdataAsset::register($this);

$title = isset($options['title']) ? $options['title'] : '';
$width = isset($options['width']) ? $options['width'] : 400;

$url = \yii\helpers\Url::to(['/ezforms2/ezform-data/dropdown-items',
    'sql_id' => $sql_id,
    'reloadDiv' => $reloadDiv,
    'target' => $target,
    'options' => backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options),
    ]);
?>
<li>
  <a id="btn-dropdown-<?=$reloadDiv?>" href="#" class="dropdown-toggle load-items" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $title ?> <span class="badge badge-content" style="background-color: #d9534f;"><?=$count?></span> <span class="caret"></span></a>
  <ul id="menu-items-<?=$reloadDiv?>" class="dropdown-menu" style="width: <?=$width?>px;max-height: 500px; overflow-y: auto;"> 
        <li><div class="sdloader"><i class="sdloader-icon"></i></div></li> 
  </ul>
</li>

<?php \appxq\sdii\widgets\CSSRegister::begin([
    //'key' => 'bootstrap-modal',
    //'position' => []
]); ?>
<style>
    /*CSS script*/
    .dropdown-menu > li > a.media {
        padding: 8px 10px;
        border-bottom: 1px solid #ccc;
    }
</style>
<?php \appxq\sdii\widgets\CSSRegister::end(); ?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('#btn-dropdown-<?=$reloadDiv?>').click(function(){
        if($(this).hasClass('load-items')){
            getAjax('<?=$url?>');
            $(this).removeClass('load-items');
        }
    });
    
    $('#menu-items-<?=$reloadDiv?>').on('click', '.more-items', function(){
        
        let url = $(this).attr('href');
        $(this).parent().remove();
        $('#menu-items-<?=$reloadDiv?>').append('<li><div class="sdloader"><i class="sdloader-icon"></i></div></li>');
        getAjax(url);
        return false;
        
    });
    
    function getAjax(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#menu-items-<?=$reloadDiv?> .sdloader').parent().remove();
                $('#menu-items-<?=$reloadDiv?>').append(result);
            }
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>