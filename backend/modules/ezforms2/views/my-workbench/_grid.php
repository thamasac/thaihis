<?php 
use backend\modules\ezforms2\classes\EzfHelper;

?>

<?php
$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];




$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id', [':ezf_id' => '1518753299024918000'])
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
$columns[] = [
                'label' => 'Status',
                'format' => 'raw',
                'attribute'=>'status',
                'headerOptions' => ['style' => "min-width:100px; text-align:center;"],
                'contentOptions' => ['style' => "min-width:100px;"],
            ];
$columns[] = [
    'label' => 'Download',
    'format' => 'raw',
    'value' => function ($data) {
        return yii\helpers\Html::a('download file','#');
    },
    'headerOptions' => ['style' => "min-width:100px; text-align:center;"],
    'contentOptions' => ['style' => "min-width:100px;"],
];


\yii\widgets\Pjax::begin();
echo \appxq\sdii\widgets\GridView::widget([
    'id' => 'grid-' . $ezf_id,
    'dataProvider' => $dataProvider,
    'columns' => $columns
]);
\yii\widgets\Pjax::end();
richardfan\widget\JSRegister::begin(['position' => yii\web\View::POS_READY]);
?>
<script>
    $('#grid-<?= $ezf_id ?> .pagination a').on('click', function () {
        getGridView($(this).attr('href'));
//        return false;
    });

    $('#grid-<?= $ezf_id ?> thead tr th a').on('click', function () {
        getGridView($(this).attr('href'));
//        return false;
    });

    $('#grid-<?= $ezf_id ?> tbody tr td a').on('click', function () {
        var div = $(this);
        $('.modal-content-grid').modal();
         $.ajax({
            url: $(this).attr('href'),
            method: 'get',
            type: 'html',
            success: function (result) {
                //div.parents('tr').after(result);
                $('.modal-content-grid').find('.modal-body').html(result);
            }
        });
//        getGridView($(this).attr('href'),$(this));
        return false;
    });

    function getGridView(url,div) {
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (result) {
                $('#'+div).append(result);
            }
        });
    }

    function getSubGrid(url) {
        $('.modal-content-grid').modal();
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (result) {
                $('.modal-content-grid').find('.modal-body').html(result);
            }
        });
    }
</script>
<?php
richardfan\widget\JSRegister::end();
