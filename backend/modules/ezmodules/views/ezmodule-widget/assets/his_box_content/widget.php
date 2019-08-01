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
$modal = 'modal-content-widget' . $widget_config['widget_id'];

$visit_id = isset($visitid)?$visitid:Yii::$app->request->get('visitid');
$target = isset($target)?$target:Yii::$app->request->get('target');
$visit_type = isset($visit_type)?$visit_type:Yii::$app->request->get('visit_type');
$dept_id = Yii::$app->user->identity->profile->department;
if(isset($options['dept_display']) && $options['dept_display'] == '1'){
    if(!in_array($dept_id, $options['dept_list'])){
        return false;
    }
}

if ($visit_id) {
    echo backend\modules\thaihis\classes\BoxContentBuilder::contentDisplay()
            ->ezf_id(isset($options['ezf_id']) ? $options['ezf_id'] : 0)
            ->target($target)
            ->visitid($visit_id)
            ->visit_type($visit_type)
            ->fields(isset($options['fields']) ? $options['fields'] : [])
            ->title(isset($options['title']) ? $options['title'] : '')
            ->initdata(isset($options['initdata']) ? $options['initdata'] : [])
            ->disabled_box(isset($options['disabled_box']) ? $options['disabled_box'] : 1)
            ->column(isset($options['column']) ? $options['column'] : [])
            ->action(isset($options['action']) ? $options['action'] : [])
            ->image_field(isset($options['image_field']) ? $options['image_field'] : '')
            ->template_content(isset($options['template_content']) ? $options['template_content'] : '')
            ->template_box(isset($options['template_box']) ? $options['template_box'] : '')
            ->display(isset($options['display']) ? $options['display'] : '')
            ->theme(isset($options['theme']) ? $options['theme'] : '')
            ->tabs(isset($options['tabs']) ? $options['tabs'] : '')
            ->show_label(isset($options['show_label']) ? $options['show_label'] : 0)
            ->require_data(isset($options['require_data']) ? $options['require_data'] : 0)
            ->graphdisplay(isset($options['graphdisplay']) ? $options['graphdisplay'] : 0)
            ->widget_id($widget_config['widget_id'])
            ->options($options)
            ->readonly(isset($readonly)?$readonly:null)
            ->modal('modal-content-widget' . $widget_config['widget_id'])
            ->buildBox('/thaihis/box-content/content');
}else if($target != ''){

}

echo appxq\sdii\widgets\ModalForm::widget([
    'id' => $modal,
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
?>


<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
$urlDrugAll = yii\helpers\Url::to(['/pis/pis-item/drug-allergy']);
?>
<script>
    $(function () {
      $('#<?= $modal ?>').on('hidden.bs.modal', function () {
        $('#<?= $modal ?>').find('.modal-content').html('');
      });
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>