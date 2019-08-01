<?php
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;

/**
 * _textinput_options file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 19 ส.ค. 2559 19:02:45
 * @link http://www.appxq.com/
 * @example  
 */

$model_form = \backend\modules\ezforms2\models\Ezform::find()->where(['ezf_id'=>$modelFields->ezf_id])->One();

$sql = "SELECT * FROM ".$model_form->ezf_table." WHERE id='".$model->id."' ";

$dataFileInput = Yii::$app->db->createCommand($sql)->queryOne();

$getSite = isset($dataFileInput['hsitecode']);
$sitecode = (isset(Yii::$app->user->identity->profile->sitecode))?Yii::$app->user->identity->profile->sitecode:0;
if(!$getSite || ($getSite && $getSite == $sitecode)){
   
echo Html::beginTag('div', ['id'=>$modelFields->ezf_id.'-box-'.$modelFields->ezf_field_id]);

echo Html::endTag('div');

$this->registerJs("
gridUpdate('".Url::to(['/ezforms2/fileinput/grid-update', 'ezf_id'=>$modelFields->ezf_id, 'ezf_field_id'=>$modelFields->ezf_field_id, 'dataid'=>$model->id])."');
    
$('#".$modelFields->ezf_id.'-box-'.$modelFields->ezf_field_id."').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'clone'){
        return false;
    } else if(action === 'delete') {
		yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
			$.post(
				url, {'_csrf':'".Yii::$app->request->getCsrfToken()."'}
			).done(function(result){
				if(result.status == 'success'){
					". SDNoty::show('result.message', 'result.status') ."
                                    gridUpdate('".Url::to(['/ezforms2/fileinput/grid-update', 'ezf_id'=>$modelFields->ezf_id, 'ezf_field_id'=>$modelFields->ezf_field_id, 'dataid'=>$model->id])."');        
				} else {
					". SDNoty::show('result.message', 'result.status') ."
				}
			}).fail(function(){
				". SDNoty::show("'" . "Server Error'", '"error"') ."
				console.log('server error');
			});
		});
                return false;
    }
    
});

$('#".$modelFields->ezf_id.'-box-'.$modelFields->ezf_field_id."').on('click', '.pagination a', function() {
    gridUpdate($(this).attr('href'));
    return false;
});

$('#".$modelFields->ezf_id.'-box-'.$modelFields->ezf_field_id."').on('click', 'thead tr th a', function() {
    gridUpdate($(this).attr('href'));
    return false;
});

function gridUpdate(url) {
    $.ajax({
	method: 'GET',
	url: url,
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#".$modelFields->ezf_id.'-box-'.$modelFields->ezf_field_id."').html(result);
	}
    });
}


");
?>

<?php } ?>