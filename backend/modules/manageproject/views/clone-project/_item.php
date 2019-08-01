<?php
\cpn\chanpan\assets\mdi\MDIAsset::register($this);
\cpn\chanpan\assets\jdrag\JDragAssets::register($this);

$imgPath = Yii::getAlias('@storageUrl');
$imgBackend = Yii::getAlias('@backendUrl');
$imageSec = $imgBackend . "/img/health-icon.png";
$auty_key = \cpn\chanpan\classes\CNUser::getEmail();
$auty_key = cpn\chanpan\classes\CNEncript::encrypt_decrypt('encrypt', $auty_key);


if(isset(\Yii::$app->session['highlight'])){
    $highlight = isset(\Yii::$app->session['highlight'])?\Yii::$app->session['highlight']:'';
    
    if($highlight['num'] == '0'){
         \Yii::$app->session['highlight'] = [
            'data_id' => $highlight['data_id'],
            'bg_color' => '#fff13b47',
            'num' => (int)$highlight['num']+1
        ];
    }else if($highlight['num'] >= 2){
        unset(\Yii::$app->session['highlight']);
    }else{
        unset(\Yii::$app->session['highlight']);
    }
}

?>
 
        
<?php
echo yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => function ($model) {
        if(isset(\Yii::$app->session['highlight']) && \Yii::$app->session['highlight']['data_id'] == $model['id']){
            $bg_color = isset(\Yii::$app->session['highlight']['bg_color'])?\Yii::$app->session['highlight']['bg_color']:'';
            return [
                'data-id' => $model['id'],
                'class' => 'list-items draggable',
                'data-url' => "https://{$model['projurl']}.{$model['projdomain']}",
                'style'=>"background:{$bg_color}",
            ];
        }
        return [
            'data-id' => $model['id'],
            'class' => 'list-items draggable',
            'data-url' => "https://{$model['projurl']}.{$model['projdomain']}"
        ];
    },

    'layout' => "<div class='all-project-list' id='all-project-list'>{items}</div><strong class='clearfix'></strong><div class='clearfix text-center'>{pager}</div>",
    'itemView' => function ($model) {
        return $this->render('_list_item', [
            'model' => $model
        ]);
    },
]);
echo yii\bootstrap\Modal::widget([
    'id' => 'modal-create-project',
    'size' => 'modal-xxl',
    'options' => ['tabindex' => false]
]);
?>
<?php
$modalf = 'discon-modal';
echo yii\bootstrap\Modal::widget([
    'id' => $modalf,
    'size' => 'modal-xxl',
    'clientOptions' => [
        //'backdrop' => false, 'keyboard' => false
    ],
    'options' => ['tabindex' => false]
]);
?>
<!-- Modal -->
<div id="modal-alert-join-project" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Cannot join this project</h4>
            </div>
            <div class="modal-body"  id="modal-alert-join-project-text">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $(function () {
        $('[data-toggle=\"tooltip\"]').tooltip();
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>


<?php appxq\sdii\widgets\CSSRegister::begin(); ?>
<style>
    #all-project-list {
        display: grid;
        grid-gap: 20px;
        grid-template-columns: repeat(6, 1fr);
        grid-auto-rows: minmax(100px, auto);
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
        /*position: absolute;*/
        bottom: 0;
        margin-bottom: 50px;
            font-size: 16pt;
    }

    .dads-children:hover {
        background-color: #ffffff;
        border: 1px solid #f5f5f7;
    }

    .dads-children-placeholder {
        pointer-events: none;
        overflow: hidden;
        position: absolute !important;
        box-sizing: border-box;
        border: 1px solid #e0dfdf;
        margin: 5px;
        text-align: center;
        color: #639BF6;
        font-weight: bold;
        border-radius: 3px;
        box-shadow: rgba(0, 0, 0, 0.03) 0px 2px 0px 0px;
    }

    .list-items {
        cursor: pointer;
    }

    @media screen and (max-width: 768px) {
        #all-project-list {
            display: grid;
            grid-gap: 25px;
            grid-template-columns: repeat(auto-fit, minmax(115px, 1fr));
            text-align: center;
            margin-bottom: 20px;
        }
    }
</style>
<?php appxq\sdii\widgets\CSSRegister::end(); ?>

<?php 
    $element_id = isset($element_id)?$element_id:'co-creator';
?>
<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    var sta = '<?= !empty($status) ? $status : 'all'; ?>';
    /*pagination*/
    $('.pagination li a').on('click', function () {
        let url = $(this).attr('href');
        $.get(url, function(data){
            $('#<?= $element_id?>').html(data);
        }); 
        
        //loadData(url);
        return false;
    });
    loadData = function (url) {
        $.get(url, function (data) {
            $('#showProjectAll').html(data);
        });
    }

    /*tootip*/

    /*edit project*/
    $('.btnEdits').on('click', function () {
        let id = $(this).attr('data-id');
        let url = '<?= \yii\helpers\Url::to(['/manageproject/center-project/view-form-update-project'])?>';
        $('#modal-create-project .modal-content').html('<div class="sdloader "><i class="sdloader - icon"></i></div>');
        let status = 'update';
        if (sta == 'trash') {
            status = 'delete';
        }
        $.get(url, {id: id, status: status}, function (data) {
            $('#modal-create-project .modal-content').html(data);
            $('#modal-create-project').modal('show');
        })
        return false;
    });
    /* drag and drop*/
    var options = {
        draggable: '.btnDrag',
        callback: function (e) {
            var positionArray = [];
            $('.draggable').find('.children').each(function () {
                positionArray.push($(this).attr('data-id'));
            });
            //delete positionArray[positionArray.length-1];
            positionArray.splice(positionArray.length - 1, positionArray.length);
            $.get('/manage_modules/default/sort', {data: positionArray.toString()}, function (data) {
                console.log(data);
            });
        }
    };
    $('#all-project-list').dad(options);

    /*show btnManage*/
    $('.btnManage').hide();
    $('.list-items').hover(function () {
            let id = $(this).attr('data-id');
            //alert(id);
            $('.list-items[data-id=' + id + '] .btnManage').fadeIn('slow');
        },
        function () {
            $('.btnManage').hide();
        }
    );

    /* click go to project*/
    $('.list-items').on('click', function () {
        let id = $(this).attr('data-id');
        if (sta == 'all' || sta == 'co' || sta == 'assign') {
            let url = $(this).attr('data-url') + '?auth_key=<?= $auty_key?>';
            location.href = url;
        }
        return false;
    });

    /* click to join project*/
    $('.btnJoin').on('click', function () {
        let id = $(this).attr('data-id');
        let checkurl = $(this).attr('data-checkurl');
        let url = '<?= \yii\helpers\Url::to(['/manageproject/clone-project/join-project-view'])?>';
        $('#modal-create-project .modal-content').html('<div class="sdloader "><i class="sdloader - icon"></i></div>');
        $.post(checkurl + "/api/ncrc-project/check-request-join-project", {"user_id":<?=Yii::$app->user->id?>}, function (res) {
            if (res['success']) {
                if(!res['has_requested'] && !res['has_join']){
                    $.get(url, {"project_id": id}, function (data) {
                        $('#modal-ezform-main .modal-content').html(data);
                        $('#modal-ezform-main').modal('show');
                    });
                } else {
                    let text = res['has_join'] ? "Your already in this project" : "Your already requested to this project";
                    $('#modal-alert-join-project-text').html(' ' +
                        '<h3 style="text-align: center">'+text+'.</h3>'
                       );
                    $('#modal-alert-join-project').modal('show');
                }
            }

        });


        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
 
 