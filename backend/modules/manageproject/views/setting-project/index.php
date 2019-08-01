<?php 
    $this->title = Yii::t('chanpan',' Manage the Created Projects');
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-3" style="margin-bottom:10px;">
        <a href="/site/index" type="button" id="btnCreateProject" class="btn btn-default btn-block btnCreateProject" style="font-weight: bold;"><i class="glyphicon glyphicon-plus"></i> Create New Project</a>                    </div>
    <div class="col-md-3">
        <a id="btnManageProject"  class="btn btn-success btn-block" href="#" style="font-weight: bold;"><i class="fa fa-cogs"></i> Manage the Created Projects</a>    
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-cogs"></i> <?= yii\helpers\Html::encode($this->title);?>
    </div>
    <div class="panel-body">
        <div id="my-project"></div>
    </div>
</div>
<?php 
    $this->registerJs("        
        function initMyProject(){
            let url = '/manageproject/setting-project/my-project';
            loadUrl(url);
        }
        function loadUrl(url){            
            $('#my-project').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $.get(url, function(data){
                $('#my-project').html(data);
            });
        }
        
        initMyProject();
    ");
?>