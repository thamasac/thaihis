<?php

use appxq\sdii\widgets\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$visit_date = isset($searchModel['create_date']) ? $searchModel['create_date'] : null;
$data_column = [];
$data_column[] = [
    'class' => 'yii\grid\SerialColumn',
    'header' => 'No.',
    'headerOptions' => ['width' => '80', 'style' => 'text-align:center;'],
    'contentOptions' => ['style' => 'text-align:center;'],
];

if (isset($options['widget_id'])) {
    $container_widget = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($options['widget_id']);

    $options_container = $container_widget['options'];
    $options_container = \appxq\sdii\utils\SDUtility::string2Array($options_container);
    $url = \yii\helpers\Url::to(['/thaihis/container-widget/container-content', 'options' => $options_container,
                'visitdate' => $visit_date, 'modal' => $modal]);
}


if (isset($left_refform) && is_array($left_refform)) {
    $refform = array_merge($refform, $left_refform);
}

if (isset($columns) && is_array($columns)) {
    foreach ($columns as $key => $val) {
        $fields = isset($val['fields'])? $val['fields']:null;
        $headOps = [];
        $conOps = [];
        if (isset($val['width']) && $val['width'] != '')
            $headOps['width'] = $val['width'];
        if (isset($val['align']) && $val['align'] != '')
            $conOps['style'] = "text-align:{$val['align']};";
            
            $headOps['style'] = 'text-align:center;';
        $data_column[] = [
            'class' => 'yii\grid\DataColumn', // can be omitted, as it is the default
            'header' => $val['header_label'],
            'format' => 'raw',
            'headerOptions' => $headOps,
            'contentOptions' => $conOps,
            'value' => function ($data) use ($val, $fields) {

                $result = '';
                $template_item = '<span id="{id}">{value}</span>';
                $template_content = '';
                $path_items = [];
                if($fields)
                    $dataField = backend\modules\thaihis\classes\ThaiHisQuery::getEzfieldById($fields);

                if (isset($dataField) && is_array($dataField)) {
                    foreach ($dataField as $keyF => $valF) {
                        $dataInput;
                        $fieldName = $valF['ezf_field_name'];
                        if (isset(Yii::$app->session['ezf_input'])) {
                            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($valF['ezf_field_type'], Yii::$app->session['ezf_input']);
                        }
                        $getValue = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $valF, $data);
                        if (!isset($val['get_value'])) {
                            $getValue = $data[$valF['ezf_field_name']];
                        }

                        $path_data = [
                            '{id}' => $fieldName . '-' . appxq\sdii\utils\SDUtility::getMillisecTime(),
                            '{value}' => $getValue,
                        ];
                        $template_content .= "{{$fieldName}}";
                        $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
                    }
                }
                
                $action_view = '';
                if(isset($val['action_field']) && $val['action_field'] != ''){
                    $action_view = Html::button(isset($val['action_label'])?$val['action_label']:'', ['class'=>'btn '.$val['btn_style']]);
                }
                
                if (isset($val['template']) && $val['template'] != '') {
                    $template_content = $val['template'];
                    $result = strtr($template_content, $path_items);
                } else {
                    $result = strtr($template_content, $path_items);
                }

                return $result.' '.$action_view; // $data['name'] for array data, e.g. using SqlDataProvider.
            },
        ];
    }
} else {
    foreach ($modelFields as $key => $val) {
        if ($val['field_to_join'] == 'no') {
            $fields = \appxq\sdii\utils\SDUtility::string2Array($val['field']);
            $data_column[] = [
                [
                    'header' => $val['ezf_field_label'],
                    'attribute' => $val['ezf_field_name'],
                    'format' => 'text'
                ],
            ];
        }
    }
}
if (isset($options['widget_id']) && $options['widget_id'] != '') {
    $data_column[] = [
        'header' => '#',
        'format' => 'raw',
        'value' => function($model) {
            $visitid = isset($model['visitid']) ? $model['visitid'] : null;
            $target = isset($model['target']) ? $model['target'] : null;
            $visit_type = isset($model['visit_type']) ? $model['visit_type'] : null;
            $result = Html::button('<i class="fa fa-pencil"></i> ' . Yii::t('thaihis', 'แก้ไขข้อมูล'), ['class' => 'btn btn-warning btn-sm btn-view-datapatient', 'data-visitid' => $visitid, 'data-target' => $target, 'data-visit_type' => $visit_type]);
            return $result;
        }
    ];
}
?>
<?php
$panelBtn = $this->renderAjax('_search', ['model' => $searchModel, 'reloadDiv' => $reloadDiv, 'options' => $options, 'modal' => $modal]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'panelBtn' => $panelBtn,
    'columns' => $data_column,
]);
?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script
    $('.btn-view-datapatient').click(function () {
        var visitid = $(this).attr('data-visitid');
        var target = $(this).attr('data-target');
        var visit_type = $(this).attr('data-visit_type');
        var modal = $('#<?= $modal ?>');
        modal.modal();
        modal.find('.modal-body').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        modal.find('.modal-content .modal-body').load('<?= $url ?>' + '&visitid=' + visitid + '' + '&target=' + target + '' + '&visit_type=' + visit_type);
    });

$('#content-multiple-grid').on('click', '.pagination li a', function () { //Next
      var url = $(this).attr('href');
      getUiAjax(url, 'subcontent-multiple-grid');
      return false;
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>