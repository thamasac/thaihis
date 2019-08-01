<?php

/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 6/30/2018
 * Time: 1:13 AM
 */

/* @var $this \yii\web\View */

cpn\chanpan\assets\CNLoadingAssets::register($this);


?>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin() ?>
<style>
    .project-item{
        height: 180px;
    }
    @media (min-width: 993px){
        .project-item{
            height: 180px;
        }
    }
    @media (min-width: 1201px){
        .project-item{
            height: 155px;
        }
    }
    @media (min-width: 1501px){
        .project-item{
            height: 220px;
        }
    }

    .col-container {
        display: table; /* Make the container element behave like a table */
        width: 100%; /* Set full-width to expand the whole page */
    }

    .col {
        display: table-cell; /* Make elements inside the container behave like table cells */
    }
</style>
    <div class=" box">     
        <div class="modal-body">
            <div class="row">
                <div id="showProjectAll"></div>
            </div>
        </div>
    </div>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::end() ?>

<?php
$url = yii\helpers\Url::to(['/manageproject/clone-project/get-collaboration-project-view', 'type' => 0]);
$this->registerJs(<<< JS
        showProjectAll=function(){
            let url = "$url";
            $.get(url,function(data){
                $('#showProjectAll').html(data);
                $("button[type='submit'][value='1']").attr('disabled', false);
            });
        }
        showProjectAll();
JS
);
?>
