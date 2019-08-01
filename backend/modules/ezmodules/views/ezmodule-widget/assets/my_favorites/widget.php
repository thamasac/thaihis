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
//appxq\sdii\utils\VarDumper::dump($options);
$id = appxq\sdii\utils\SDUtility::getMillisecTime();
?>
<span class="<?= isset($options['position_type']) ? $options['position_type'] : '';?>">
    <button class="btn btn-<?= $options['btn_type'];?> <?= $options['btn_block']?>  <?= $options['position_size']?> " id='btn-favorites-<?= $id ?>'>
        <i class="fa <?= isset($options['icon']) ? $options['icon'] : 'fa-home'?>"></i> <?= isset($options['btn_name']) ? $options['btn_name'] : 'My Favorites'?>
    </button>
</span>
<?php
echo \appxq\sdii\widgets\ModalForm::widget([
    'id' => 'favorites-' . $id,
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
?>
<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#btn-favorites-<?= $id ?>').on('click', function () {
        loadFavorites();
        return false;
    });
    function loadFavorites() {
        let url = '/site/my-favorites';
        modalApp(url);
    }
    function modalApp(url) {
        $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-main').modal('show')
                .find('.modal-content')
                .load(url);
    }
//        loadFavorites();
</script>
<?php \richardfan\widget\JSRegister::end(); ?>