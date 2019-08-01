<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = '<?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?>#'.$model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= "<?=" ?> Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= "<?= " ?>DetailView::widget([
	    'model' => $model,
	    'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
	foreach ($generator->getColumnNames() as $name) {
	echo "\t\t'" . $name . "',\n";
	}
} else {
	foreach ($generator->getTableSchema()->columns as $column) {
	$format = $generator->generateColumnFormat($column);
	echo "\t\t'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
	}
}
?>
	    ],
	]) ?>
    </div>
</div>
