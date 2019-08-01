<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\core\classes\CoreFunc;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CorePosts */
/* @var $form yii\bootstrap\ActiveForm */

	
?>

<div class="posts-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<h4 class="modal-title" id="itemModalLabel"><?php echo CoreFunc::t(ucfirst($type));?></h4>
    </div>
    
    <div class="modal-body">
	<?= $form->field($model, 'post_title')->textInput() ?>
	<?= $form->field($model, 'post_content')->widget(dosamigos\tinymce\TinyMce::className(),[
		'options' => ['rows' => 20],
		'language' => CoreFunc::getConvertLanguage(),
		'clientOptions' => [
			'fontsize_formats' => '8pt 9pt 10pt 11pt 12pt 26pt 36pt',
			'plugins' => [
				"advlist autolink lists link image charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars code fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons template paste textcolor colorpicker textpattern",
			],
			'toolbar' => "undo redo | styleselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons",
			'content_css' => Yii::getAlias('@rootUrl').'/vendor/bower/bootstrap/dist/css/bootstrap.css',
			'image_advtab' => true,
			'filemanager_crossdomain' => true,
			'external_filemanager_path' => Yii::getAlias('@storageUrl').'/filemanager/',
			//'filemanager_access_key' => 'appxq_key',
			'filemanager_title' => 'Responsive Filemanager',
			'external_plugins' => [
			    'filemanager' => Yii::getAlias('@storageUrl').'/filemanager/plugin.min.js'
			]
		]
	]) ?>
	
	<?= Html::activeHiddenInput($model, 'post_author')?>
	<?= Html::activeHiddenInput($model, 'post_date')?>
	<?= Html::activeHiddenInput($model, 'post_date_gmt')?>
	<?= Html::activeHiddenInput($model, 'post_excerpt')?>
	<?= Html::activeHiddenInput($model, 'post_name')?>
	<?= Html::activeHiddenInput($model, 'to_ping')?>
	<?= Html::activeHiddenInput($model, 'pinged')?>
	<?= Html::activeHiddenInput($model, 'post_modified')?>
	<?= Html::activeHiddenInput($model, 'post_modified_gmt')?>
	<?= Html::activeHiddenInput($model, 'post_content_filtered')?>
	<?= Html::activeHiddenInput($model, 'guid')?>
	<?= Html::activeHiddenInput($model, 'post_type')?>
	<?= Html::activeHiddenInput($model, 'post_mime_type')?>
	<?= Html::activeHiddenInput($model, 'comment_count')?>
	
    </div>
    <div class="modal-footer">
	
    </div>

    <section id="right-side" class="right-sidebar" role="complementary" >
	<div id="right-side-scroll" class="row">
	    <div class="col-md-12">
		<div class=" sidebar-nav-title" ><?php echo CoreFunc::t('Publish');?></div>
		<div style="padding: 10px; ">
		    <?= $form->field($model, 'post_status')->dropDownList(Yii::$app->controller->module->postStatusList, ['onchange' => 'checkPostStatus(this.value)'])->label(false) ?>
		    <?= $form->field($model, 'post_password')->textInput(['maxlength' => true, 'placeholder' => CoreFunc::t('Password'), 'disabled' => false])->label(false) ?>
		    <?= $form->field($model, 'comment_status')->checkbox() ?>
		    <?= $form->field($model, 'ping_status')->checkbox() ?>
		    <?php if(!in_array($type, Yii::$app->controller->module->hasParentPost)): ?>
			<?= $form->field($model, 'sticky_posts')->checkbox() ?>
		    <?php endif;?>
		    <div class="form-group">
			    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-block btn-primary']) ?>
		    </div>
		</div>
		
		<?php if(in_array($type, Yii::$app->controller->module->hasParentPost)): ?>
		<div class=" sidebar-nav-title" ><?php echo CoreFunc::t('Page Attributes');?></div>
		<div style="padding: 10px; ">
		    <?= $form->field($model, 'post_parent')->dropDownList(CoreFunc::getPostDropDownList(0, $type), ['encode' => false, 'prompt' => CoreFunc::t('no perent')]) ?>
		    <?= $form->field($model, 'page_template')->dropDownList(Yii::$app->controller->module->templateList, ['encode' => false]) ?>
		    <div class="row">
			<div class="col-md-6">
				<?= $form->field($model, 'menu_order')->textInput(['type'=>'number']) ?>
			</div>
		    </div>
			
		</div>
		<?php else: ?>
		<?= Html::activeHiddenInput($model, 'menu_order')?>
		<?= Html::activeHiddenInput($model, 'post_parent')?>
		
		<div class=" sidebar-nav-title" ><?php echo CoreFunc::t('Categories');?></div>
		<div style="padding: 10px; ">
		    <?= $form->field($model, 'categories')->checkboxList(CoreFunc::getTaxonomyCheckList(), ['encode' => false, 'prompt' => CoreFunc::t('none'), 'multiple'=>true, 'size'=>10])->label(false) ?>
		</div>
		
		<div class=" sidebar-nav-title" ><?php echo CoreFunc::t('Tags');?></div>
		<div style="padding: 10px; width: 100% ">
		    <div id="tags_form"></div>
		    <?= $form->field($model, 'tags_id')->widget(kartik\select2\Select2::className(),[
			//'initValueText' => $model->tags_id, // set the initial display text
			'options' => ['placeholder' => 'Search Tags'],
			'pluginOptions' => [
			    'allowClear' => true,
			    //'tags' => true,
			    //'tokenSeparators' => [',', ' '],
			    //'minimumInputLength' => 1,
			    //'multiple'=>true,
			    'ajax' => [
				'type' => 'GET',
				'url' => Url::to(['/core/tags/search-lookup']),
				'dataType' => 'json',
				'data' => new JsExpression('function(params) { return {q:params.term}; }')
			    ],
			    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
			    'templateResult' => new JsExpression('function(tags) { return tags.text; }'),
			    'templateSelection' => new JsExpression('function (tags) { return tags.text; }'),
			],
//			'pluginEvents' => [
//			    "select2:select" => "function(e) { $('#research-author_email').val(e.params.data.email); $('#research-author').val(e.params.data.text); }",
//			    "select2:unselect" => "function() { $('#research-author_email').val(''); $('#research-author_id').val(''); }"
//			]
		    ])->label(false) ?>
		</div>
		
		<div class=" sidebar-nav-title" ><?php echo CoreFunc::t('Format');?></div>
		<div style="padding: 0 10px; ">
		    <?= $form->field($model, 'post_format')->radioList(Yii::$app->controller->module->formatPostList)->label(false) ?>
		</div>

		<?php endif; ?>
	    </div>
	</div>
    </section>

    <?php ActiveForm::end(); ?>

</div>

<?php  $this->registerJs("
$('.page-column').addClass('column2');

formTags();

function formTags(){
	$('#tags_form').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
	$.getJSON('".Url::to(['/core/tags/widget', 'taxonomy'=>'post_tag'])."', function(result) {
	    if(result.status == 'success'){
		$('#tags_form').html(result.content);
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	});
}

");?>

<?php  $this->registerJs("
function updateTags(value){
	var data = $('#postsform-tags_id').select2('data');
	
	for ( var index in value ) {
		data.push(value[index]);
	}
	
	$('#postsform-tags_id').val({id:1,text:'dfsdf'}).trigger('change');
	
	console.log($('#postsform-tags_id').select2('data'));
}	
", 3);?>