<style>
    .no-bounce {
        width: 100%;
        margin-left: 0px;
        overflow-y: scroll;
        -webkit-overflow-scrolling: touch;
    }
         /* width */
     ::-webkit-scrollbar {
         width: 10px;
     }

     /*Track*/
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
         left: 50px;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

</style>

<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 6/20/2018
 * Time: 9:26 PM
 */

use appxq\sdii\widgets\ModalForm;
use backend\modules\ezmodules\classes\ModuleFunc;

$imgStep1 = Yii::getAlias('@storageUrl') . '/ezform/img/mobile_step1.png';

$img = Yii::getAlias('@storageUrl') . '/ezform/img/mobile_mockup.png';
$img = "url($img)";
$addIcon = Yii::getAlias('@storageUrl') . '/ezform/img/add_icon.png';

//$imgStep1 = "url($imgStep1)";
?>

    <div style=' padding:5px;border-color: black;border: solid; background-image:<?= $img ?>;width: 1024px;height: 920px;'>
        <div style="position: absolute;">
            <h4><u> EzMobile Application Editor </u></h4>
        </div>

        <div style=' position: relative;left: 345px;top:130px;width: 325px;'>
            <div class="container" style="width: 325px">
                <h1 style="margin-bottom: 30px ;margin-top: 28px;color: #f16e02">
                    nCRC Mobile
                </h1>
                <img id="intruction-img" src="<?= $imgStep1 ?>"  style="height: 380px;max-height: 380px; position: relative;left: -30px;"/>
                <div id="applications_container" class="row no-bounce"  style="height: 380px;">

                </div>

                <div class="row" style="margin-top: 10px">
                    <div class="col-sm-1"></div>
                    <button id="favorite-form-manager" class='col-sm-10 btn btn-primary btn-lg'
                            data-url="<?= \yii\helpers\Url::to(['/ezforms2/mobile/list']) ?>">
                        <b>Add an EzForm</b>
                    </button>
                    <div class="col-sm-1"></div>
                </div>
            </div>
        </div>

        <div style=' padding-top: 20px; position: relative;left: 345px;top:170px;width: 325px;height: 100px;'>
            <div class="container" style="width: 325px">
                <div id="applications_favorite_container"  class="row">
                    <div class="col-sm-3" style="text-align: center;height:130px;">
                        <div ><img src="<?= $addIcon ?>" class="img-rounded"
                                                       width="54" height="54"></div>
                    </div>
                    <div class="col-sm-3" style="text-align: center;height:130px;">
                        <div ><img src="<?= $addIcon ?>" class="img-rounded"
                                                       width="54" height="54"></div>
                    </div>
                    <div class="col-sm-3" style="text-align: center;height:130px;">
                        <div ><img src="<?= $addIcon ?>" class="img-rounded"
                                                       width="54" height="54"></div>
                    </div>
                    <div class="col-sm-3" style="text-align: center;height:130px;">
                        <div><img src="<?= $addIcon ?>" class="img-rounded"
                                                       width="54" height="54"></div>
                    </div>

                </div>

            </div>
        </div>

    </div>


<?=
ModalForm::widget([
    'id' => 'modal-ezform-favorite',
    'size' => 'modal-lg',
]);
?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
    <script>
        $( document ).ready(function() {
            var fav = <?= json_encode($favoriteForm) ?>;
            if(fav.length > 0){
                $("#intruction-img").hide();
                $("#applications_container").show();
                // $("#applications_container").css("background-repeat","no-repeat");
            }else{
                $("#intruction-img").show();
                $("#applications_container").hide();
            }
            $.each(fav, function (index, value) {
                if (!value["ezf_icon"]) {
                    value["ezf_icon"] = "<?= ModuleFunc::getNoIconModule()?>";
                }
                $("#applications_container").append(" <div class=\"col-sm-4\" style=\"text-align: center;height:130px;\">\n" +
                    "                    <div><img src=\"" + value["ezf_icon"] + "\" class=\"img-rounded\" width=\"64\" height=\"64\"></div>\n" +
                    "                    " + value["ezf_name"] + "\n" +
                    "                                  </div> ");
            });
            console.log(fav);
        });

        $('#favorite-form-manager').click(function () {
            var url = $(this).attr('data-url');
            modalEzform(url);
            return false;
        });

        $('#modal-ezform-favorite').on('hidden.bs.modal', function (e) {
            location.reload();
        });

        function modalEzform(url) {
            $('#modal-ezform-favorite .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-ezform-favorite').modal('show')
                .find('.modal-content')
                .load(url);
        }
        $( function() {
            // $( "#applications_favorite_container" ).sortable({
            //     revert:  '100'
            // });
            $( "#applications_container" ).sortable({
                revert: '100'
            });
            // $( "icon" ).disableSelection();
        } );
    </script>
<?php \richardfan\widget\JSRegister::end(); ?>