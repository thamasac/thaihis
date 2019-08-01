<?php

use appxq\sdii\widgets\GridView;
use appxq\sdii\utils\SDdate;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->registerJsFile('@web/js-vis/vis.js');
$this->registerCssFile("@web/js-vis/vis-timeline-graph2d.min.css");
$this->registerJsFile("@web/js-vis/html2canvas.js");
//$this->registerCssFile("@web/js-vis/vis.css");
$this->registerCss('
    #timelineMS{
    margin: 2em 0 2em 0;
    border-radius: 3px;
    box-shadow: 0 2px 4px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12)!important;
}
    .vis-item.success  { background-color: #34fe1f;border-color:#00ff00; }
    .vis-item.warning  { background-color: #ff7a2c;border-color: #ff7702; }
    .vis-item.danger { background-color: #ff3232;border-color: #ff0000; }
    
     .vis-item.vis-selected {  background-color: #fff785;border-color:#ffc200; box-shadow: 0 0 15px #ffd700; }

');
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>Timeline and Milestone</h4>
</div>
<div class="col-md-6 ">
    <?= Html::label('Sub-task filter:', '') ?>
    <?=
    kartik\select2\Select2::widget([
        'name' => 'subtask_filter',
        'value' => $sub_filter,
        'options' => ['placeholder' => Yii::t('ezmodule', 'Sub-task select...'), 'id' => 'config_subtask_filter'],
        'data' => ArrayHelper::map($subtask_list, 'id', 'task_name'),
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
</div>
<div class="clearfix"></div>
<div class="row modal-body">
    <div class="col-md-12">
        <?=
        Html::button('<i class="fa fa-picture-o"></i> Save as image ', [
            'class' => 'btn btn-success btn-sm pull-right', 'id' => 'btn-Preview-Image',
            'title' => Yii::t('ezform', 'Show Image'),
            'data-toggle' => "modal",
            'data-target' => "#ModalImage",
        ]);
        ?>
        <br/>
        <div id="timelineMS" style ="background-color: white"></div>
    </div>
    <div class="clearfix"></div>


    <div class="modal fade" id="ModalImage" role="dialog">
        <div class="modal-dialog"  style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><b>Show Image</b></h4>
                </div>

                <div class="modal-body">

                    <img id="previewImage" class="img-responsive"  src=""/>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <div class="col-md-12">
        <?php
        echo GridView::widget([
            'id' => 'timeline-grid',
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
                ],
                [
                    'attribute' => 'task_name',
                    'label' => 'Task Item Name',
                    'headerOptions' => ['style' => 'text-align: left;'],
                    'contentOptions' => ['style' => 'width:200px;text-align: left;'],
                ],
                [
                    'attribute' => 'start_date',
                    'label' => 'Start Date',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if (empty($data['start_date'])) {
                            return '';
                        } else {
                            $startDate = isset($data['start_date']) ? date('Y-m-d H:i:s', strtotime($data['start_date'])) : '';
                            return SDdate::mysql2phpDateTime($startDate);
                        }
                    },
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'width:150px;text-align: center;'],
                ],
                [
                    'attribute' => 'finish_date',
                    'label' => 'Due Date (planned)',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if (empty($data['finish_date'])) {
                            return '';
                        } else {
                            $endDate = isset($data['finish_date']) ? date('Y-m-d H:i:s', strtotime($data['finish_date'])) : '';
                            return SDdate::mysql2phpDateTime($endDate);
                        }
                    },
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'width:200px;text-align: center;'],
                ],
                [
                    'attribute' => 'actual_date',
                    'label' => 'Actual Date',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if (empty($data['actual_date'])) {
                            return '';
                        } else {
                            $actualDate = isset($data['actual_date']) ? date('Y-m-d H:i:s', strtotime($data['actual_date'])) : '';
                            return SDdate::mysql2phpDateTime($actualDate);
                        }
                    },
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'width:200px;text-align: center;'],
                ],
                [
                    'attribute' => 'respons_person',
                    'label' => 'Responsible person assigned individually',
                    'format' => 'raw',
                    'value' => function ($data) {
                        $respons_person = isset($data['respons_person']) ? json_decode($data['respons_person']) : '';

                        if (!empty($respons_person)) {
                            $user_assign = [];
                            $num = 0;
                            foreach ($respons_person as $key) {
                                $uname = \cpn\chanpan\classes\CNUser::GetUserNcrcById($key);
                                $user_assign[$num] = $uname['profile']['firstname'];
                                $num++;
                            }
                            $user_assign = implode(",", $user_assign);
                        } else {
                            $user_assign = '';
                        }
                        return $user_assign;
                    },
                    'headerOptions' => ['style' => 'text-align: left;'],
                    'contentOptions' => ['style' => 'width:200px;text-align: left;'],
                ],
            ],
        ]);
        ?>
    </div>
</div>
<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-info-app',
        //'size'=>'modal-lg',
]);
?>

<?php
\richardfan\widget\JSRegister::begin([
//'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>

<script>


    $(function () {
        var container = document.getElementById('timelineMS');
        var substr = '<?php echo json_encode($itemTL) ?>';
        var data = $.parseJSON(substr);

        var items = new vis.DataSet(data);
        var options = {};

        var timeline = new vis.Timeline(container, items, options); // Create a Timeline

        setTimeout(function () {
            var ids = ['<?= $dataid ?>'];
            timeline.setSelection(ids, {focus: true});
        }, 1000);
    });

    var element = $('#timelineMS'); // global variable
    var getCanvas; // global variable

    $('#btn-Preview-Image').on('click', function () {
        html2canvas(element, {
            onrendered: function (canvas) {
                var url = canvas.toDataURL();
                $('#previewImage').attr('src', url);
                var a = document.createElement('a');
                // toDataURL defaults to png, so we need to request a jpeg, then convert for file download.
                a.href = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");
                a.download = 'timelime-milestone-' + Date.now() + '.jpg';
                a.click();
            }
        });
    });

    $('#save_image_timeline').click(function () {
        html2canvas($('#previewImage'),
                {
                    onrendered: function (canvas) {
                        var a = document.createElement('a');
                        // toDataURL defaults to png, so we need to request a jpeg, then convert for file download.
                        a.href = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");
                        a.download = 'timelime-milestone-' + Date.now() + '.jpg';
                        a.click();
                    }
                });
    });
    
    $('#config_subtask_filter').on('change',function(){
        var sub_filter = $(this).val();
        var dataid = '<?=$dataid?>';
        var project_id = '<?=$project_id?>';
        var url = "/gantt/timeline-milestone/index";

        $.get(url,{dataid: dataid,project_id:project_id,sub_filter:sub_filter},function(result){
            $('#modal-ezform-project').find('.modal-content').empty();
            $('#modal-ezform-project').find('.modal-content').html(result);
        });
        return false;
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>