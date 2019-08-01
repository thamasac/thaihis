<?php 
use backend\modules\ezforms2\classes\BtnBuilder;
//\appxq\sdii\utils\VarDumper::dump($field_taget);
?>
<div class="pull-right" style="margin-bottom: 10px;">
<?= BtnBuilder::btn()
        ->ezf_id($detail_ezf)
        ->target($target)
        ->label("<i class='fa fa-plus'></i> Add New Document")
        ->options(['class'=>'btn btn-success'])
        ->buildBtnAdd();
        ?>
</div>
<div class="clearfix"></div>
<?php
$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];




$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])
        ->orderBy(['ezf_field_order' => SORT_ASC])
        ->all();

if (!isset(Yii::$app->session['ezf_input'])) {
    Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}

foreach ($column as $field) {

    $fieldName = $field;
    if (is_array($field) && isset($field['attribute'])) {
        $fieldName = $field['attribute'];
    }

    $changeField = TRUE;
    foreach ($modelFields as $key => $value) {
        $var = $value['ezf_field_name'];
        $label = $value['ezf_field_label'];
        if ($fieldName == $var && $var != 'id') {
            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

//    $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
            $colTmp = [
                'attribute' => $var,
                'label' => $label,
                'value' => function ($data) use($dataInput, $value) {
                    return backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $data);
                },
                'headerOptions' => ['style' => "min-width:100px; text-align:center;"],
                'contentOptions' => ['style' => "min-width:100px;"],
                'filter' => $htmlFilter,
            ];
            if (is_array($field) && isset($field['attribute'])) {
                $colTmp = \yii\helpers\ArrayHelper::merge($colTmp, $field);
            }

            $changeField = FALSE;
            $columns[] = $colTmp;
//    break;
        }
    }
}


$columns[] = [
    'label' => 'View',
    'format' => 'raw',
    'value' => function ($data) use($field_taget, $ezf_id, $id, $pagSize, $column,$detail_ezf) {
        return BtnBuilder::btn()->ezf_id($detail_ezf)->reloadDiv('')
                ->label('<i class="glyphicon glyphicon-pencil"></i>')
                ->options(['class'=>'btn btn-success btn-sm'])
                ->buildBtnEdit($data['id'])." ".
                BtnBuilder::btn()->ezf_id($detail_ezf)->reloadDiv('')
                ->label('<i class="glyphicon glyphicon-eye-open"></i>')
                ->options(['class'=>'btn btn-info btn-sm'])
                ->buildBtnView($data['id']);
    },
    'headerOptions' => ['style' => "min-width:100px; text-align:center;"],
    'contentOptions' => ['style' => "min-width:100px;"],
];



//yii\widgets\Pjax::begin(['id'=>'pjax-sub-'. $reloadDiv]);
echo \appxq\sdii\widgets\GridView::widget([
    'id' => 'sub-grid-' . $reloadDiv,
    'dataProvider' => $dataProvider,
    'columns' => $columns
]);
//yii\widgets\Pjax::end();
richardfan\widget\JSRegister::begin(['position' => yii\web\View::POS_READY]);
?>
<script>
    $('#grid-<?= $reloadDiv ?> .pagination a').on('click', function () {
//        getGridView($(this).attr('href'));
        return false;
    });

    $('#grid-<?= $reloadDiv ?> thead tr th a').on('click', function () {
//        getGridView($(this).attr('href'));
        return false;
    });

    $('#grid-<?= $reloadDiv ?> tbody tr td a').on('click', function () {
//        getGridView($(this).attr('href'));
        return false;
    });

    function getGridView(url) {
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (result) {
                $('#divContent-<?= $reloadDiv ?>').html(result);
            }
        });
    }

    function getSubGrid(url) {
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (result) {
                $('#divContent-<?= $reloadDiv ?>').html(result);
            }
        });
    }
</script>
<?php
richardfan\widget\JSRegister::end();
