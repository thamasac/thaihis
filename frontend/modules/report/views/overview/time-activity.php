<?php
use \yii\helpers\Html;
?>
<div id="time-activity" class="row">
    <div class="col-md-12 col-xs-12 col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3>Project Activity
                </h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th><?= Yii::t('report', 'Time') ?></th>
                        <th class="text-center"><?= Yii::t('report', 'Member') ?></th>
                        <th class="text-center"><?= Yii::t('report', 'Project') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?=Html::a(Yii::t('report', 'Today'), null, ['class'=>'showdilldown','data-type' => '1day']); ?></td>
                        <td class="text-right"><?=$sum['1dayU']?></td>
                        <td class="text-right"><?=$sum['1dayP']?></td>
                    </tr>
                    <tr>
                        <td><?=Html::a('1 '.Yii::t('report', 'Week'), null, ['class'=>'showdilldown','data-type' => '7day']); ?></td>
                        <td class="text-right"><?=$sum['7dayU']?></td>
                        <td class="text-right"><?=$sum['7dayP']?></td>

                    </tr>
                    <tr>
                        <td><?=Html::a('1 '.Yii::t('report', 'Month'), null, ['class'=>'showdilldown','data-type' => '1month']); ?></td>
                        <td class="text-right"><?=$sum['1monthU']?></td>
                        <td class="text-right"><?=$sum['1monthP']?></td>

                    </tr>
                    <tr>
                        <td><?=Html::a('3 '.Yii::t('report', 'Month'), null, ['class'=>'showdilldown','data-type' => '3month']); ?></td>
                        <td class="text-right"><?=$sum['3monthU']?></td>
                        <td class="text-right"><?=$sum['3monthP']?></td>

                    </tr>
                    <tr>
                        <td><?=Html::a('6 '.Yii::t('report', 'Month'), null, ['class'=>'showdilldown','data-type' => '6month']); ?></td>
                        <td class="text-right"><?=$sum['6monthU']?></td>
                        <td class="text-right"><?=$sum['6monthP']?></td>
                    </tr>
                    <tr>
                        <td><?=Html::a('1 '.Yii::t('report', 'Year'), null, ['class'=>'showdilldown','data-type' => '1year']); ?></td>
                        <td class="text-right"><?=$sum['1yearU']?></td>
                        <td class="text-right"><?=$sum['1yearP']?></td>
                    </tr>
                    </tbody>
                </table>
                <div id="project-activity"></div>
            </div>
        </div>
    </div>

</div>
<?php
\richardfan\widget\JSRegister::begin();
?>
<script>
    $('.showdilldown').click(function () {
        $('html,body').animate({
            scrollTop: $('#project-activity').offset().top
        }, 200);
        $('#project-activity').html('<div class="text-center"><h4>Loading....</h4></div>');
        var data = {type:$(this).attr('data-type'),text:$(this).text()}
        $.ajax({
            method: 'GET',
            url: '/report/overview/list-activity' ,
            data: data,
            dataType: 'HTML',
            success: function(result, textStatus) {
            $('#project-activity').html(result);
        }
    });
    });
    $(document).on('click', '.close', function () {
       $('#project-activity').empty();
    });

</script>
<?php
\richardfan\widget\JSRegister::end();
?>
<style>
    a{
        cursor: pointer;
    }
    tfoot > tr > td {
        text-align: right;
    }
</style>
