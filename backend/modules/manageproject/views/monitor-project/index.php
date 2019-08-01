<?php 
    $this->title = Yii::t('chanpan','Monitor Project');
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3><i class="fa fa-desktop"></i> <?= yii\helpers\Html::encode($this->title);?></h3>
    </div> 
    <div class="panel-body">
        <div class="row">
<!--            <div class="col-md-6">
                <div class="form-group">
                    <label>Search: </label>
                    <input type='text' id="txtSearch" class="form-control" placeholder="Search for Project Name , Acronym , URL , Domain"/>
                </div>
            </div>-->
           
            <div class="col-md-8">
                <?php
                yii\bootstrap\ActiveForm::begin([
                    'options' => [
                        'class' => 'text-right',
                        'id' => 'frmManagerProject'
            ]])
                ?>
                <div class="input-group" style="margin-bottom:30px;">
                    <input type="text" 
                           id="txtSearch" 
                           class="form-control input-lg" name="txtSearch" 
                           style="" placeholder="<?= Yii::t('chanpan', 'Search for Project Name , Acronym , URL , Domain') ?>">
                    <div class="input-group-btn">
                        <button tabindex="-1" type="submit" class="btn btn-primary btn-lg" id="btnSearch"><i class="fa fa-search"></i> <?= Yii::t('chapan', 'Search') ?></button>

                    </div>
                </div>     

            <?php yii\bootstrap\ActiveForm::end(); ?>
            </div>
        </div>
        <div id="my-project"></div>
    </div>
</div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
    $('#frmManagerProject').submit(function(){
        let val = $('#txtSearch').val();        
         let url = '/manageproject/monitor-project/my-project?search='+val;
         loadUrl(url);   
        
       return false;
    });
    
    function initMyProject(){
        let url = '/manageproject/monitor-project/my-project';
        loadUrl(url);
    }
    function loadUrl(url){
     $('#my-project').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
   
//     $('#my-project').html('<div class=\'text-center\'><i class=\"fa fa-spinner fa-pulse fa-3x fa-fw\"></i></div>');
     $.get(url, function(data){
        $('#my-project').html(data);
     });
    }
    initMyProject();
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
 