<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformCommunity */

$this->title = 'Community #'.$ezf_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Query Tool'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-community-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?php
        echo backend\modules\ezforms2\classes\CommunityBuilder::Community()->type('query_tool')->object_id($object_id)->dataid($dataid)->buildQueryTool();
        ?>
    </div>
</div>
