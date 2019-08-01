<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CorePosts */

$this->title = 'Media';
//$this->params['breadcrumbs'][] = $this->title;
?>

<div id="iframe-view" class="media-view">
    <div class="sdloader "><i class="sdloader-icon"></i></div>
</div>

<?php  $this->registerJs("
$.ajax({
    method: 'POST',
    url: '". Url::to(['/core/media/media'])."',
    dataType: 'JSON',
    success: function(result, textStatus) {
	if(result.status == 'success') {
	    ". SDNoty::show('result.message', 'result.status') ."
	    $('#iframe-view').html(result.html);
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
	}
    }
});

");?>