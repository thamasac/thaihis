<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use <?= $generator->indexWidgetType === 'grid' ? "appxq\sdii\widgets\GridView" : "yii\\widgets\\ListView" ?>;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <div class="sdbox-header">
	<h3><?= "<?= " ?> Html::encode($this->title) ?></h3>
    </div>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?php " ?> Pjax::begin(['id'=>'<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax']);?>
    <?= "<?= " ?>GridView::widget([
	'id' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	<?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selection<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>Ids'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
		'template' => '{view} {update} {delete}',
	    ],
        ],
    ]); ?>
    <?= "<?php " ?> Pjax::end();?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>

</div>

<?= "<?= " ?> ModalForm::widget([
    'id' => 'modal-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>',
    'size'=>'modal-lg',
]);
?>

<?= "<?php " ?> \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
// JS script
$('#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax').on('click', '#modal-addbtn-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>', function() {
    modal<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>($(this).attr('data-url'));
});

$('#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax').on('click', '#modal-delbtn-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>', function() {
    selection<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>Grid($(this).attr('data-url'));
});

$('#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid').yiiGridView('getSelectedRows');
	disabled<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>Btn(key.length);
    },100);
});

$('#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax').on('click', '.selection<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>Ids', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabled<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>Btn(key.length);
});

$('#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modal<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>('<?= "<?= " ?>Url::to(['<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/update', 'id'=>''])?>'+id);
});	

$('#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modal<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>(url);
    } else if(action === 'delete') {
	yii.confirm('<?= "<?= " ?>Yii::t('app', 'Are you sure you want to delete this item?')?>', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    <?= "<?= " ?>SDNoty::show('result.message', 'result.status')?>
		    $.pjax.reload({container:'#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax'});
		} else {
		    <?= "<?= " ?>SDNoty::show('result.message', 'result.status')?>
		}
	    }).fail(function() {
		<?= "<?= " ?>SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
		console.log('server error');
	    });
	});
    }
    return false;
});

function disabled<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>Btn(num) {
    if(num>0) {
	$('#modal-delbtn-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>').attr('disabled', false);
    } else {
	$('#modal-delbtn-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>').attr('disabled', true);
    }
}

function selection<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>Grid(url) {
    yii.confirm('<?= "<?= " ?>Yii::t('app', 'Are you sure you want to delete these items?')?>', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selection<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>Ids:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    <?= "<?= " ?>SDNoty::show('result.message', 'result.status')?>
		    $.pjax.reload({container:'#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid-pjax'});
		} else {
		    <?= "<?= " ?>SDNoty::show('result.message', 'result.status')?>
		}
	    }
	});
    });
}

function modal<?= Inflector::classify(StringHelper::basename($generator->modelClass)) ?>(url) {
    $('#modal-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>').modal('show')
    .find('.modal-content')
    .load(url);
}
</script>
<?= "<?php " ?> \richardfan\widget\JSRegister::end(); ?>