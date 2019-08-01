<?php
// start widget builder
use yii\helpers\Url;
use \backend\modules\ezforms2\classes\EzfAuthFunc;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
// query
try {
    
    $q = new \yii\db\Query();
                $q->select('*')
                    ->from('advance_report_config')
                    ->where('proj_id = :proj_id', [':proj_id'=>Yii::$app->request->get('id',0)])->andWhere('status=0')->orderBy('conf_order');
                $data = $q->createCommand()->queryAll();
                
    //$sql = 'select * from advance_report_config where proj_id = :proj_id and status = 0 order by conf_order';
    //$data = Yii::$app->db->createCommand($sql, [':proj_id'=>Yii::$app->request->get('id','')])->queryAll();
//            return $model;
} catch (\yii\db\Exception $e) {
    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
    return FALSE;
}

$module = Yii::$app->request->get('id',0);
$time = strtotime("-1 year", time());
$date = date("Y-m-d", $time);
?>
<?php
$this->registerJs("
function loadReportWidget(id){
    var parentid = id;
    var report_type =$('#'+id+' .advance_site_code').val();
    var start_date = $('#'+id+' .advance_date_start').val();
    var end_date = $('#'+id+' .advance_date_end').val();
    var onwith = $('#'+id+' .advance_width').val();
    var site_code = $('#'+id+' .allsite').val();
    var selectbox = $('#'+id+' .advance_box').val();
    var sitechoose = $('#'+id+' .sitechoose').val();
    var valueRow = $('#'+id+' .valueRow').val();
    var valueCol = $('#'+id+' .valueCol').val();

    $('#'+id+' .reporttype-option').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');

    setTimeout(function(){ 
        $.ajax({
            method: 'GET',
            url: '" . Url::to(['/graphconfig/graphconfig/get-report-data']) . "',
            data: {selectbox:selectbox,onwith:onwith,end_date:end_date,
            start_date: start_date, parentid: parentid,report_type:report_type,
            site_code:site_code,sitechoose:sitechoose,valueCol:valueCol,valueRow:valueRow},
            dataType: 'HTML',
            success: function(result) {
                $('#'+id+' .reporttype-option').html(result);
            }
        });
    }, 1000); 
}

"); ?>

<div class="panel panel-primary">
    <div class="panel-heading ">
        
        <span ><?=$options['title'] != '' ? $options['title'] : '&nbsp'?></span>
        
    </div>
    <div class="panel-body">
        <div class="btn-group pull-right col-md-12">
            <a class="btn btn-default pull-right" id="add-report-setup" style="margin-right : 5px;"><i class="fa fa-cog"></i></a>
            <?php if(EzfAuthFunc::canManage($module) || EzfAuthFunc::canReadWrite($module)){ ?>
                <a class="btn btn-default pull-right" id="add-report-graph"  style="margin-right : 5px;"><i class="fa fa-plus"></i></a> 
            <?php } ?>
        </div>    
        <div class="col-md-12 setup-toggle" style="display: none;">
            <div class="col-md-12"><span><?= Yii::t('graphconfig', 'Setup date to all graph') ?> </span></div>
            <div class="col-md-6"><b><?= Yii::t('graphconfig', 'Start date') ?></b>
                <input type="date"  class="form-control" id="all-setup-startdate" value="<?= $date ?>" >
                <br>
                </div>
                <div class="col-md-6"><b><?= Yii::t('graphconfig', 'Stop date') ?></b>
                    <input type="date" class="form-control" id="all-setup-enddate" value="<?php echo date('Y-m-d') ?>">
                    <br>
                </div>
        </div>
        
        <div class="widget-graph col-md-12" >
            <?php
            if($data){
                foreach ($data as $datarow) {
                    echo $this->render('_advance_config', ['data'=>$datarow, 'module' => $module]);           
                }
            }
            ?>
        </div>
        
        
    </div>
    <div id="modal-advance-config-edit" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" class="col-md-12">
        <div class="modal-dialog" style="width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel"><?= Yii::t('graphconfig', 'Edit') ?></h3>
                </div>
                <div class="modal-body" >
                    <div class="col-md-12" style="padding : 15px 10px;" >
                        <span> <?= Yii::t('graphconfig', 'Variable can use in SQL COMMAND') ?></span>
                            <table class="table">
                                <tr>
                                    <td>_USERSITECODE_</td>
                                    <td><?= Yii::t('graphconfig', 'User sitecode') ?></td>
                                    <td>_SITECODE_</td>
                                    <td><?= Yii::t('graphconfig', 'Select sitecode') ?></td>
                                </tr>
                                <tr>
                                    <td>_STARTDATE_</td>
                                    <td><?= Yii::t('graphconfig', 'Start date') ?></td>
                                    <td>_STOPDATE_</td>
                                    <td><?= Yii::t('graphconfig', 'Stop date') ?></td>
                                </tr>
                            </table>
                    </div>
                    <?php $form = ActiveForm::begin([
                            'id'=>'advance-report-config-form',
                            'options'=>['enctype'=>'multipart/form-data'],
                        ]); ?>
                    <div id="advance-report-config-box" class="well report-widget-box" style="padding: 15px; background-color: #fcf8e3; border-color: #faebcc; ">
                        
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary form-submit-button', 'name'=>'action_submit', 'value'=>'submit']) ?>
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    
</div>

<?php
$this->registerCss("
    /*** set flex display when >md min width; *****/
    @media (min-width:992px) {
       .widget-graph .flex-widget{
        display: flex;
        flex-direction: column;
        }
        .widget-graph {
        display: flex;
        flex-wrap: wrap;
        }
    }
    .modal-header, .modal-body, .modal-footer{
        background-color : #fff;
    }
    
");
$this->registerJs("
    reportOrder();
    function reportOrder(){
        $.ajax({
                method: 'GET',
                url: '" . Url::to(['/graphconfig/graphconfig/advance-report-order']) . "',
                data: {module:'$module'},
                dataType: 'HTML',
                success: function(result) {
                    console.log(result);
                }
            });
    }
    $('.btn-clone').click(function(){
        var parentid = $(this).attr('data-parentid');
        var graphname = $(this).attr('data-graphname');
        var report_type =$('#'+parentid+' .advance_site_code').val();
        var start_date = $('#'+parentid+' .advance_date_start').val();
        var end_date = $('#'+parentid+' .advance_date_end').val();
        var onwith = $('#'+parentid+' .advance_width').val();
        var site_code = $('#'+parentid+' .allsite').val();
        var selectbox = $('#'+parentid+' .advance_box').val();
        var sitechoose = $('#'+parentid+' .sitechoose').val();
        var valueRow = $('#'+parentid+' .valueRow').val();
        var valueCol = $('#'+parentid+' .valueCol').val();

        var url = $(this).attr('data-url'); 

         $('#modal-ezform-main .modal-content').html('<div class=\"progress progress-striped active\"><div class=\"progress-bar\" style=\"width:100%\"></div></div>');
         // $('#modal-ezform-main .modal-title').html(graphname);


        $('#modal-ezform-main').modal('show');
        setTimeout(function(){ 
            $.ajax({
                method: 'GET',
                url: url,
                data: {selectbox:selectbox,onwith:onwith,end_date:end_date,
                    start_date: start_date, parentid: parentid,report_type:report_type,
                    site_code:site_code,sitechoose:sitechoose,valueCol:valueCol,valueRow:valueRow,modal:1, graphname : graphname},
                    dataType: 'HTML',
                    success: function(result) {
                    $('#modal-ezform-main .modal-content').html(result);
                    $('#modal-ezform-main .modal-title').html(graphname);
                    return false;
                }
            });
        }, 500);

    });
   
    $('.btn-maximize').click(function(e){
        var parentid = $(this).attr('data-parentid');
        var graphname = $(this).attr('data-graphname');
        var report_type =$('#'+parentid+' .advance_site_code').val();
        var start_date = $('#'+parentid+' .advance_date_start').val();
        var end_date = $('#'+parentid+' .advance_date_end').val();
        var onwith = $('#'+parentid+' .advance_width').val();
        var site_code = $('#'+parentid+' .allsite').val();
        var selectbox = $('#'+parentid+' .advance_box').val();
        var sitechoose = $('#'+parentid+' .sitechoose').val();
        var valueRow = $('#'+parentid+' .valueRow').val();
        var valueCol = $('#'+parentid+' .valueCol').val();
        var title = graphname;
        var newpage = 1;
         e.preventDefault(); 
        var url = $(this).attr('data-url'); 
        window.open(url+'?selectbox='+selectbox+'&onwith='+onwith+'&end_date='+end_date+'&start_date='+start_date+'&parentid='+parentid+'&report_type='+report_type+'&site_code='+site_code+'&sitechoose='+sitechoose+'&valueCol='+valueCol+'&valueRow='+valueRow+'&newpage='+newpage+'&title='+title, '_blank');


    });
    $('.btn-edit').click(function(e){
        var parentid = $(this).attr('data-parentid');
        var graphname = $(this).attr('data-graphname');
        $('.form-submit-button').html('Edit');
        $('#modal-advance-config-edit #myModalLabel').html('".Yii::t('graphconfig', 'Edit')."  '+graphname);
        $('#modal-advance-config-edit').modal('show');
        
        $.ajax({
                method: 'GET',
                url: '" . Url::to(['/graphconfig/graphconfig/get-advance-report-data']) . "',
                data: {id:parentid,module:'$module'},
                dataType: 'HTML',
                async : false,
                success: function(result) {
                    $('#advance-report-config-box').html(result)
                }
            });
        loadReportList(parentid);
    });
    
    $('#add-report-graph').click(function(e){
        var url = $(this).attr('data-url'); 
        $('#modal-advance-config-edit #myModalLabel').html('".Yii::t('graphconfig', 'Add graph')."');
        $('#modal-advance-config-edit').modal('show');
        $('.form-submit-button').html('Create');
        $.ajax({
                method: 'GET',
                url: '" . Url::to(['/graphconfig/graphconfig/get-advance-report-data']) . "',
                data: {id:0,module:'$module'},
                dataType: 'HTML',
                async : false,
                success: function(result) {
                    $('#advance-report-config-box').html(result)
                }
            });
        loadReportList();
        
    });
    
    $('#advance-report-config-form').submit(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $.ajax({
                method: 'POST',
                url: '" . Url::to(['/graphconfig/graphconfig/advance-report-save']) . "',
                data: $('#advance-report-config-form').serialize(),
                dataType: 'JSON',
                success: function(result) {
                    var stat = JSON.parse(result);
                    //console.log(stat.statustext);
                    " . SDNoty::show('stat.message', 'stat.status') . "
                    if(stat.status == 'success'){
                        
                        // set value
                        $('#'+stat.id +' .advance_site_code').val(stat.reporttype);
                        $('#'+stat.id +' .advance_width').val(stat.width);
                        $('#'+stat.id +' .advance_box').val(stat.selectbox);
                        $('#'+stat.id +' .sitechoose').val(stat.selecthospdef);
                        $('#'+stat.id +' .valueRow').val(stat.reporttypeval);
                        $('#'+stat.id +' .valueCol').val(stat.reporttypevariable);
                        
                        loadReportWidget(stat.id);
                        $('#modal-advance-config-edit').modal('hide');
                        
                    }
                }
            });
        
        return false;
    });

    function loadReportList(parentid = null){
        $.ajax({
                method: 'GET',
                url: '" . Url::to(['/graphconfig/graphconfig/report-list']) . "',
                data: {module:'$module', parentid : parentid},
                dataType: 'JSON',
                success: function(result) {
                    $('#advance-report-config-box .order').html('');
                    // clear and add new option
                    var data = result.data;
                    for(var i =0; i < data.length; i++){
                        // console.log('id= '+data[i].id+' text = '+data[i].text);
                        $('#advance-report-config-box .order').append($('<option>',
                            {
                               value: data[i].id,
                               text : data[i].text, 
                           }));
                    }
                    //
                    if(!parentid){
                        $('#forms-order').val(99999).trigger('change');
                    }
                }
            });
    }

    
    $('#all-setup-startdate').change(function(){
        var allval = $(this).val();
        $('.advance_date_start').each(function(){
            // set date
            $(this).val(allval);
            var id = $(this).attr('data-id');
            // reload graph
            loadReportWidget(id);
        });
    });
    $('#all-setup-enddate').change(function(){
        var allval = $(this).val();
        $('.advance_date_end').each(function(){
            // set date
            $(this).val(allval);
            var id = $(this).attr('data-id');
            // reload graph
            loadReportWidget(id);
        });
    });
    $(document).on('change', '.advance_config_input', function(){
        var allval = $(this).val();
        
            var id = $(this).attr('data-id');
            // reload graph
            loadReportWidget(id);
    });
    $('#add-report-setup').click(function(){
        $('.setup-toggle').toggle();
    });

    $('#advance_report').change(function(){
        var advreport = $(this).is(':checked') ? 1 : '';
        $.ajax({
                method: 'POST',
                url: '" . Url::to(['/graphconfig/graphconfig/advance-report-select']) . "',
                data: {advreport:advreport,module:'$module' },
                dataType: 'HTML',
                success: function(result) {
                   var data = JSON.parse(result);
                    " . SDNoty::show('data.message', 'data.status') . "
                }
            });
    });
" );
?>