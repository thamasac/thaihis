<div id="reloadList">
<?= $this->render('_item',[
    'template' => $template,
    'dataProvider' => $dataProvider,
    'status' => 'assign'
])?>
</div>
<div id="show-content"></div>
<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $('#btnSelectTemplate').hide();
    $('.btnTemplates').on('click', function(){
       let id = $(this).attr('data-id');
       let url = "<?= yii\helpers\Url::to(['/manageproject/template/get-form-create'])?>";
       $.get(url, {id:id}, function(data){
           $('#show-content').html(data);
           $('#templates').hide();
       })
       
       return false;
    });
    $('#btnSelectTemplate').on('click', function(){
        $('#templates').show();
    });
</script>
<?php \richardfan\widget\JSRegister::end();?>

<?php \appxq\sdii\widgets\CSSRegister::begin();?>
<style>
        
    #all-project-list { 
            margin-bottom: 0px;
        } 
        .pagination {
            position: relative;             
        }
    #all-project-list{
        display: grid;
        grid-gap: 20px;        
        grid-template-columns: repeat(6,1fr);
        grid-auto-rows: minmax(100px , auto);
        text-align: center;
        margin-bottom: 20px; 
    }
    .list-items {
        display: flex;
        flex-direction: column;
        height: 160px;
        padding-top: 8px;
        border: 1px solid transparent;
        border-radius: 2px;
    }
    .pagination {
        position: absolute;
        bottom: 0;
        display: block;
        margin-bottom: 50px;
    } 
    .dads-children:hover {
        background-color: #ffffff;
        border: 1px solid #f5f5f7;
    } 
     
    .dads-children-placeholder{
        pointer-events: none;
        overflow: hidden;
        position: absolute !important;
        box-sizing: border-box;
        border:1px solid #e0dfdf;
        margin:5px;
        text-align: center;
        color: #639BF6;
        font-weight: bold;
        border-radius:3px;
        box-shadow: rgba(0, 0, 0, 0.03) 0px 2px 0px 0px;
    }
    .list-items{cursor: pointer;}
     
</style>
<?php \appxq\sdii\widgets\CSSRegister::end();?>
