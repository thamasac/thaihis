<?php
use yii\bootstrap\Html;
?>
<footer class="footer" role="contentinfo">
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!--		<p class="pull-right footer-tools"><a href="#" class="go-top"><i class="fa fa-arrow-circle-o-up"></i></a></p>-->
                    <p>
                        Developed by <a href="http://www.ncrc.in.th/" target="_blank">National Clinical Research
                            Center</a> E-Mail: ncrcthailand@gmail.com.<br/>
                        Copyright © nCRC 2018. All Rights Reserved.
                    </p>
                    <?php
                    if(Yii::$app->user->isGuest){
                        echo Html::a('Development Login', ['site/login'], ['class'=>'pull-right btn btn-outline-primary']);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>