<?php 
    $footer_text = '<p>Developed by <a href="#" target="_blank">DAMASAC at Khon Kaen University</a>, E-Mail: ncrcthailand@gmail.com<br/>Copyright@DAMASAC 2018. All Rights Reserved.</p>';
?>
<footer class="footer" role="contentinfo">
    <div class="footer-content">
        <div class="row">
            <div class="col-md-12">
                
                <!--		<p class="pull-right footer-tools"><a href="#" class="go-top"><i class="fa fa-arrow-circle-o-up"></i></a></p>-->
                <?= isset(Yii::$app->params['project_setup_footer'])?Yii::$app->params['project_setup_footer']:''?>
            </div>
        </div>
    </div>
</footer>
 
