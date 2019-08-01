<?php 
use backend\modules\ezforms2\classes\EzfHelper;

?>
<div class="col-md-12 "><?= EzfHelper::btn()
        ->ezf_id($detail_ezf)
        ->initdata(['F2v2'=>$id])
        ->label("<i class='fa fa-plus'></i> Add New Document")
        ->options(['class'=>'btn btn-success pull-right'])
        ->buildBtnAdd();
        ?>
</div>
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
        if ($fieldName == $var) {
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


//$columns[] = [
//    'label' => 'View',
//    'format' => 'raw',
//    'value' => function ($data) use($taget, $ezf_id, $id, $pagSize, $column) {
//        return yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', yii\helpers\Url::to(['/ezforms2/my-workbench/get-sub-grid',
//                            'ezf_id' => $ezf_id,
//                            'taget' => $data['taget'],
//                            'id' => $id,
//                            'pageSize' => $pagSize,
//                            'column' => $column
//                        ]), [
//                    'data-action' => 'update',
//                    'title' => Yii::t('yii', 'View'),
//                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                    'class' => 'btn btn-primary btn-xs btnViewDoc',
//        ]);
//    },
//    'headerOptions' => ['style' => "min-width:100px; text-align:center;"],
//    'contentOptions' => ['style' => "min-width:100px;"],
//];




echo \appxq\sdii\widgets\GridView::widget([
    'id' => 'grid-' . $ezf_id,
    'dataProvider' => $dataProvider,
    'columns' => $columns
]);
richardfan\widget\JSRegister::begin(['position' => yii\web\View::POS_READY]);
?>
<script>
    $('#grid-<?= $ezf_id ?> .pagination a').on('click', function () {
//        getGridView($(this).attr('href'));
        return false;
    });

    $('#grid-<?= $ezf_id ?> thead tr th a').on('click', function () {
//        getGridView($(this).attr('href'));
        return false;
    });

    $('#grid-<?= $ezf_id ?> tbody tr td a').on('click', function () {
        getGridView($(this).attr('href'));
        return false;
    });

    function getGridView(url) {
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (result) {
                $('#divContent-<?= $ezf_id ?>').html(result);
            }
        });
    }

    function getSubGrid(url) {
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (result) {
                $('#divContent-<?= $ezf_id ?>').html(result);
            }
        });
    }
</script>
<?php
richardfan\widget\JSRegister::end();
