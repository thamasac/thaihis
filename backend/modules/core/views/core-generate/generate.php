<?php

use yii\helpers\Html;
use backend\modules\core\models\CoreFields;
use yii\bootstrap\ActiveForm;
use backend\modules\core\classes\CoreFunc;

/* @var $this yii\web\View */
/* @var $modelFields backend\modules\core\models\CoreGenerate */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('core', 'Generate');
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Generates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="core-generate-generate">
    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>
	<div class="modal-header">
	    <h4 class="modal-title" id="itemModalLabel"><?= $modelFields->gen_name?></h4>
	</div>
	<div class="modal-body">
	    <p>
			<?php if(isset($modelFields->gen_tag) && $modelFields->gen_tag!=''):?>
				<span class="label label-success">Tags</span>
				<?= Yii::t('core', $modelFields->gen_tag) ?>
				&nbsp; 
			<?php endif;?> 
			<?php if(isset($modelFields->gen_link) && $modelFields->gen_link!=''):?>
				<span class="label label-info">Read More</span>
				<?= Yii::t('app', Html::a($modelFields->gen_link, $modelFields->gen_link, ['target'=>'_blank'])) ?>
			<?php endif;?>
	    </p>
	    
	    <?php
		if (!empty($modelFields->gen_ui)) {
		   $col = 1;
		   $fixCol = isset($_GET['col']) ? $_GET['col'] : 6;
		   $count = count($modelFields->gen_ui);
		   $mod = $count % $fixCol;
		   $total = floor($count / $fixCol);
		   $fieldsArr = [];
		   $row = $total + ($mod > 0 ? 1 : 0);

		   if ($mod > 0) {
			   $mod--;
		   }
		   $index = 1;
		   foreach ($modelFields->gen_ui as $key => $value) {
			   //field_name field_internal field_class
			   $data = CoreFields::findOne(['field_code' => $value['input_field']]);
			   if ($data) {
				   $value['field_name'] = $data['field_name'];
				   $value['field_internal'] = $data['field_internal'];
				   $value['field_class'] = $data['field_class'];
				   $value['field_meta'] = $data['field_meta'];
				   $value['field_code'] = $data['field_code'];
				   $value['field_description'] = $data['field_description'];
			   }

			   if ($index > $row) {
				   $row = $total + ($mod > 0 ? 1 : 0);
				   $index = 1;
				   if ($mod > 0) {
					   $mod--;
				   }
				   $col++;
			   }

			   $index++;

			   $fieldsArr[$col] = (isset($fieldsArr[$col]) ? $fieldsArr[$col] . "\n" : '') . CoreFunc::generateInput($value, $model, $form);
		   }
		   echo '<div class="row">';
		   for ($i = 1; $i <= $fixCol; $i++) {
			   $numCol = floor(12 / $fixCol);
			   echo '<div class="col-md-' . $numCol . ' ' . (($i == 1) ? '' : 'sdbox-col') . '">';
			   echo isset($fieldsArr[$i]) ? $fieldsArr[$i] : '';
			   echo '</div>';
		   }
		   echo '</div>';
		}
		?>
	</div>
	<div class="modal-footer">
	    <?= Html::submitButton(Yii::t('core', 'Generate'), ['class' => 'btn btn-success']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>
    <?php ActiveForm::end(); ?>
</div>
<?php if(isset($_POST['DynamicModel'])):?>
<?php
	$code_php = 'unidentified';
	$code_html = 'unidentified';
	$code_js = 'unidentified';
	$fields = $model->attributes;

	if ($modelFields->gen_process != '') {
		eval($modelFields->gen_process);
	}

	if ($modelFields->template_php != '') {
		$template_php = $modelFields->template_php;
		$code_php = strtr($template_php, CoreFunc::getTranslated($fields));
	}

	if ($modelFields->template_html != '') {
		$template_html = $modelFields->template_html;
		$code_html = strtr($template_html, CoreFunc::getTranslated($fields));
	}

	if ($modelFields->template_js != '') {
		$template_js = $modelFields->template_js;
		$code_js = strtr($template_js, CoreFunc::getTranslated($fields));
	}
	?>

<div class="row">
	<div class="col-lg-12">
		<h5 class="modal-title">Example</h5>
		<div class="well">
			<div class="row">
				<div class="col-lg-4"><?= $code_html ?></div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12">
		<h5 class="modal-title">HTML Code</h5>
		<pre class="prettyprint"><code class="html"><?= Html::encode($code_html) ?></code></pre>
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12">
	<h5 class="modal-title">JavaScript Code</h5>
	<pre class="prettyprint"><code class="javascript"><?= Html::encode($code_js) ?></code></pre>
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12">
	<h5 class="modal-title">PHP Code</h5>
	<pre class="prettyprint"><code class="php"><?= Html::encode($code_php) ?></code></pre>
	</div>
</div>
<br>

<?php  
/*$baseUrl = '@web/../modules/core/assets/prettify';
$this->registerCssFile($baseUrl.'/css/prettify.css');
$this->registerCssFile($baseUrl.'/css/monokai.css');
$this->registerJsFile($baseUrl.'/prettify.js');*/
backend\modules\core\assets\CoreAsset::register($this);

$this->registerJs("
    prettyPrint();
");?>

<?php endif;?>

