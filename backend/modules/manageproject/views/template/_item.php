<?php
$this->title = "Clone from existing project templates";
$imgPath = Yii::getAlias('@storageUrl');
$imgBackend = Yii::getAlias('@backendUrl');
$imageSec = "/img/health-icon.png";
use appxq\sdii\helpers\SDNoty;
\backend\modules\ezforms2\assets\JLoading::register($this);

?>

<div class="col-md-12" id="templates">
    <div class="row" >   
        <?php 
                echo yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemOptions'=>function($model){
                        return [
                            'data-id'=>$model['id'],
                            'class'=>'list-items btnTemplates',
                            'data-toggle'=>'tooltip',
                            'data-placement'=>'top',
                            'title'=>"{$model['projectname']}",
                            'data-url'=>"https://{$model['projurl']}.{$model['projdomain']}"        
                        ];
                    },

                    'layout' => "<div class='all-project-list' id='all-project-list'>{items}</div><strong class='clearfix'></strong>{pager}",
                    'itemView' => function ($model) {
                        return $this->render('_list_item',[
                            'model' => $model
                        ]);
                    },
                ]);
        ?>     
        <div class="clearfix"></div>

    </div>
<hr  />
    <div class="col-md-12"style="padding-left:0px;">
        <div><label>OR From Scratch</label></div>
         <button class="btn btn-success btnTemplates" data-id="1531376278057471800"><i class="fa fa-plus"></i> Create from scratch</button>
        <hr />
        <div><label>OR From Backup file</label></div>
        <div>
             
            <form enctype="form" id="frm-restore" method="post" action="<?= yii\helpers\Url::to(['/manageproject/backup-restore/restore'])?>">
                <div style="margin-bottom: 10px;s">
                    <!--<input type="file" name="file-restore" id="file-restore" accept=".img"/>-->
                    <div class="col-md-6" style="padding-left:0;">
                        <?php 
                            echo kartik\file\FileInput::widget([
                                    'name' => 'file-restore',
                                    'pluginOptions' => [
                                        'showPreview' => false,
                                        'showCaption' => true,
                                        'showRemove' => true,
                                        'showUpload' => false
                                    ],
                                    'options' => ['multiple' =>false,'accept' => '.img', 'id'=>'file-restore']

                                ]);

                        ?>
                    </div>
                </div>
                <div class="clearfix" style="margin-bottom:10px;"></div>
                <div>
                    <button type="submit" class="btn btn-success btnSubmit"><i class="fa fa-refresh"></i> Restore form a backup file</button>
                </div>
                
            </form>
        </div>
        <div class="clearfix"></div><br><br>
        
    </div>
</div> 

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
    @media screen and (max-width: 768px) {      
        #all-project-list{             
            display: grid;
            grid-gap: 25px;
            grid-template-columns: repeat(auto-fit, minmax(115px, 1fr));
            text-align: center;
            margin-bottom: 20px;
        }
    }
</style>
<?php \appxq\sdii\widgets\CSSRegister::end();?>
<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    
    var element_loadings_items = 'body';
    function onLoadings() {
        $(element_loadings_items).waitMe({
            effect: 'facebook',
            text: 'Please wait...',
            bg: 'rgba(255,255,255,0.7)',
            color: '#000',
            maxSize: '',
            waitTime: -1,
            textPos: 'vertical',
            fontSize: '',
            source: '',
            onClose: function () {}
        });
    }
    function hideLoadings() {
        $(element_loadings_items).waitMe("hide");
    }
    
    
    $('.pagination li a').on('click', function(){
       let url = $(this).attr('href');
       loadDataList(url);
       return false;
    });
    loadDataList=function(url){
        $.get(url, function(data){
            $('#reloadList').html(data);
        });
    }
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('#frm-restore').on('submit', function(){
            onLoadings(); 
            $('.btnSubmit').prop('disabled', true);
            $.ajax({
                url: "/manageproject/backup-restore/restore", // Url to which the request is send
                type: "POST",             // Type of request to be send, called as method
                data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData:false,        // To send DOMDocument or non processed data file it is set to false
                
            }).done(function (result) {
               $('.btnSubmit').prop('disabled', false);
                    if(result.status == 'success') {
                         <?= SDNoty::show('result.message', 'result.status')?>           
                         $('#modal-ezform-main').modal('toggle');
                         let url = '/manageproject/clone-project/get-project-all?status=1';
                         setTimeout(function(){
                             loadUrl(url);
                             hideLoadings() 
                         },800);
                     } else {
                         hideLoadings() 
                         <?= SDNoty::show('result.message', 'result.status')?>
                     } 
            }).fail(function (jqXHR, textStatus) {
                hideLoadings() 
                let result = {status:'error', message:'Server Error!'};
                <?= SDNoty::show('result.message', 'result.status')?>
            });
            return false;
        });
     });
    function loadUrl(url){
        $('#showProjectAll').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $.get(url, function(data){
           $('#showProjectAll').html(data);
        });
    }  
      
      
</script>
<?php \richardfan\widget\JSRegister::end();?>


