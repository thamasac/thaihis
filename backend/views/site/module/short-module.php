<?php 
    use yii\helpers\Url;
?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= Yii::t('module', 'Project Short Cut ')?></h4>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="col-md-4" style="background: #e5e5e5;
    padding-top: 10px;    border-radius: 5px">
            <h4 class="text-left"><?= Yii::t('module', 'All Modules')?></h4> 
            <div>
                <input type="text" class="form-control txt-search-module" placeholder="Search Module" style="margin-bottom:5px;border-radius: 20px;"/>
            </div>
            <div id="module-all"></div>
        </div>
        <div class="col-md-8">
            <h4><?= Yii::t('module', 'Selected Modules')?></h4>
            <div class='clearfix'></div>
            <div id="module-select"></div>
        </div>  
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>





<?php \richardfan\widget\JSRegister::begin();?>
<script>
    
    $('.txt-search-module').on('change', function(){
        let term = $(this).val();
        initModuleAll(term);
        return false;
    });
    function initModuleAll(term=''){
       let params = {};
       if(term != ''){
           params = {term:term};
       }
       let url = '/site/short-module-all';
       let module ='#module-all';
       $(module).html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
       $.get(url,params, function(data){
           $(module).html(data);
           
       });
    }
    function initModuleSelect(){
       let url = '/site/short-module-select';
       let module ='#module-select';
       $(module).html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
       $.get(url, function(data){
           $(module).html(data);
       });
    }
    
    $('#modal-create-project').on('hidden.bs.modal', function () {
        //location.reload();
        //header-setting
        let url = '/site/short-module-select?edit_mode=1';
        $.get(url, function(data){
           $('#short-module-header-bar').html(data);
       });
        //initModuleSelect();
    })
    
    initModuleAll();
    initModuleSelect();
</script>
<?php \richardfan\widget\JSRegister::end();?>

<?php \appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    .letf-module{
        border-right: 1px solid #cecece;
        height: 350px;
        overflow-y: hidden;
        overflow: auto;
        padding-left: 15px;
    }
    .letf-module img.media-object {
        width: 60px;
    }
    .pagers .pagination{
        margin-bottom: 0;
        margin-top:10px;
    }
    .pagers{margin-top: 45px;}
    #module-select{overflow-x: scroll;overflow-y: hidden; }
</style>
<?php \appxq\sdii\widgets\CSSRegister::end();?>
