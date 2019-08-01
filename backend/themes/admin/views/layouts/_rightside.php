<?php
$count = 0;
?>
<section id="right-side" class="right-sidebar" role="complementary" style="display: <?= (isset($_COOKIE['feedback_toggler']) && $_COOKIE['feedback_toggler'] == 1 && isset(Yii::$app->params['feedback']) && Yii::$app->params['feedback']==1) ? 'block' : 'none'; ?>;">
    <div id="right-side-scroll" class="row">
	<div class="col-lg-12">
	    <?php if (!empty($this->params['menu'])): ?>
		<div class=" sidebar-nav-title" >เมนูดำเนินการ</div>
		<div class=" sidebar-nav" >
		    <?= common\lib\sdii\widgets\SDMenu::widget([
			'options'=>['class'=>'nav nav-list'],
			'items' => $this->params['menu'],
		    ]) ?>
		</div>
	    <?php endif ?>

	    <?php if (!empty($this->params['widgets'])): ?>
		<?php echo $this->params['widgets']; ?>
	    <?php endif ?>
            
            <?php if (isset(Yii::$app->params['feedback']) && Yii::$app->params['feedback']==1 && !Yii::$app->user->isGuest): ?>    
                <div class=" sidebar-nav-title" style="margin-bottom: 10px;">Feedback <a id="btn-feedback-win" style="color: #777;" class="pull-right btn btn-link btn-xs"><i class="glyphicon glyphicon-triangle-bottom"></i></a></div>
                <div style="padding-left: 5px;padding-right: 5px;">
                <style>
        #chaticon {
    position: fixed;
    padding: 5px;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    cursor: pointer;
}
#chatbox, #chaticon {
    z-index: 4400;
    border-left: 1px solid #999;
    border-right: 1px solid #999;
    border-top: 1px solid #999;
    bottom: 0;
    right: 100px;
}
#chaticon, #chattext {
    background-color: #fff;
}
#chatlabel {
    font-size: 13px;
    font-weight: 700;
    color: #555;
    text-decoration: none;
    margin-right: 3px;
}
.buttonicon {
    width: 16px;
    height: 16px;
    display: inline-block;
    border: none;
    padding: 0;
    background: 0 0;
    font-size: 15px;
    font-style: normal;
    font-weight: 400;
    color: #666;
    cursor: pointer;
}
.buttonicon, .exportlink {
    font-family: fontawesome-etherpad;
}
#chatcounter {
    color: #777;
    font-size: 10px;
}
    </style>
    
            <?php 
                    $cUrl = Yii::$app->request->pathInfo;
                    $model = \backend\modules\core\models\Feedback::find()
                            ->where('url=:url', [':url'=>$cUrl])
                            ->one();
                    if($model){
                        
                    } else {
                        $model = new \backend\modules\core\models\Feedback();
                        $model->id = appxq\sdii\utils\SDUtility::getMillisecTime();
                        $model->url = $cUrl;
                        $model->save();
                    }
                    
                    $count = backend\modules\ezforms2\models\EzformCommunity::find()->where('object_id=:object_id AND type="feedback"', [':object_id'=>$model->id])->count();
                    
                    echo backend\modules\ezforms2\classes\CommunityBuilder::Community()
                        ->type('feedback')
                        ->object_id($model->id)
                        ->buildCommunity();?>
    
                
                </div>
            <?php endif ?>    
                
    
	</div>
    </div>
</section>

<div id="chaticon" style="display: <?= (isset($_COOKIE['feedback_toggler']) && $_COOKIE['feedback_toggler'] == 1 && isset(Yii::$app->params['feedback']) && Yii::$app->params['feedback']==1) ? 'none' : 'block'; ?>;">
    <span id="chatlabel" data-l10n-id="pad.chat" aria-label="Chat">Feedback</span>
    <span class="fa fa-comment-o"></span>
    <span ><code><?=$count?></code></span>
</div>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('#chaticon').click(function(){
        $('#right-side').show();
        $('#chaticon').hide();
        $('.page-column').addClass('column2');
        $.cookie('feedback_toggler', 1, { path: '/' });
    });
    
    $('#btn-feedback-win').click(function(){
        $('#right-side').hide();
        $('#chaticon').show();
        $('.page-column').removeClass('column2');
        $.cookie('feedback_toggler', 0, { path: '/' });
    });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>